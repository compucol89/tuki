<?php

namespace App\Services\OpenAI\EventAI;

use App\Services\EventAi\CanonicalEventFacts;
use App\Services\EventAi\EventArchetypes;
use App\Services\EventAi\PlatformContext;
use App\Services\OpenAI\EventAI\Prompts\EventAuditPrompt;
use App\Services\OpenAI\EventAI\Prompts\EventCopyPrompt;
use App\Services\OpenAI\EventAI\Prompts\EventRepairPrompt;
use App\Services\OpenAI\EventAI\Prompts\EventSeoPrompt;
use App\Services\OpenAI\EventAI\Prompts\EventStrategyPrompt;
use App\Services\OpenAI\EventAI\Prompts\FlyerExtractionPrompt;
use App\Services\OpenAI\EventAI\Quality\EventContentGenericnessGate;
use App\Services\OpenAI\EventAI\Quality\EventContentPolicyGate;
use App\Services\OpenAI\EventAI\Quality\EventContentQualityGate;
use App\Services\OpenAI\EventAI\Schemas\EventAuditSchema;
use App\Services\OpenAI\EventAI\Schemas\EventCopySchema;
use App\Services\OpenAI\EventAI\Schemas\EventSeoSchema;
use App\Services\OpenAI\EventAI\Schemas\EventStrategySchema;
use App\Services\OpenAI\EventAI\Schemas\FlyerExtractionSchema;
use App\Services\OpenAI\EventAiAssistantService;
use App\Services\EventAi\EventAiDraftPostProcessor;
use RuntimeException;

/**
 * Orquestador V3 — pipeline por etapas:
 * extraction → strategy → copy → seo → audit → repair → gates deterministas.
 * Detrás de la flag AI_EVENT_ASSISTANT_V3_ENABLED; V2 intacto por defecto.
 */
class EventAiOrchestrator
{
  public const BUNDLE_VERSION = '2026-08-21-v3';

  public function __construct(
    private EventAiAssistantService $assistant,
    private EventAiDraftPostProcessor $postProcessor,
  ) {
  }

  public function isEnabled(): bool
  {
    return (bool) config('features.event_ai_assistant_v3_enabled', false);
  }

  /* ---------- ETAPA 1: EXTRACCIÓN ---------- */

  public function extract(string $imagePath, array $formFacts): array
  {
    $prompt = app(FlyerExtractionPrompt::class);
    $model = config('openai.event_assistant.models.extract', 'gpt-5.6-luna');

    $result = $this->assistant->createStructured(
      $model,
      $prompt->instructions(),
      [[
        'role' => 'user',
        'content' => [
          ['type' => 'input_text', 'text' => $prompt->build($formFacts)],
          ['type' => 'input_image', 'image_url' => $this->assistant->imageAsDataUrl($imagePath), 'detail' => 'high'],
        ],
      ]],
      FlyerExtractionSchema::NAME,
      FlyerExtractionSchema::toArray(),
    );

    if ($this->shouldEscalateExtraction($result)) {
      $result = $this->assistant->createStructured(
        config('openai.event_assistant.models.escalate', 'gpt-5.6-sol'),
        $prompt->instructions(),
        [[
          'role' => 'user',
          'content' => [
            ['type' => 'input_text', 'text' => $prompt->build($formFacts) . "\n\nEscalamiento: detectá con máxima precisión los datos ambiguos o contradictorios."],
            ['type' => 'input_image', 'image_url' => $this->assistant->imageAsDataUrl($imagePath), 'detail' => 'high'],
          ],
        ]],
        FlyerExtractionSchema::NAME,
        FlyerExtractionSchema::toArray(),
      );
    }

    return $result;
  }

  private function shouldEscalateExtraction(array $result): bool
  {
    if (!(bool) config('openai.event_assistant.v3.escalation_enabled', true)) {
      return false;
    }

    foreach (($result['extracted_fields'] ?? []) as $field) {
      if (($field['sensitive'] ?? false) && ((float) ($field['confidence'] ?? 0)) < 0.75) {
        return true;
      }
    }

    return !empty($result['critical_differences']) || !empty($result['conflicts']);
  }

  /* ---------- ETAPAS 2-6: ESTRATEGIA → COPY → SEO → AUDIT → REPAIR ---------- */

  public function generate(CanonicalEventFacts $facts, array $preferences, string $category, string $eventType): array
  {
    $archetypes = app(EventArchetypes::class);
    $archetype = $archetypes->guess(
      $category ?? '',
      (string) ($facts->resolve('title')['value'] ?? ''),
      $eventType,
    );

    $strategy = $this->strategize($facts, $preferences, $archetype);

    $copy = $this->copy($facts, $strategy, $preferences, $archetypes->example($archetype));

    $seo = $this->seo($facts, $copy);

    $audit = null;
    if ((bool) config('openai.event_assistant.v3.audit_enabled', true)) {
      $audit = $this->audit($facts, $strategy, $copy, $seo);

      $attempts = (int) config('openai.event_assistant.v3.max_repair_attempts', 1);
      while ($audit['status'] === 'repair' && $attempts > 0) {
        $attempts--;
        $copy = $this->repair($copy, $audit['blocking_failures'], $audit['repair_instructions'], $facts);
        $seo = $this->seo($facts, $copy);
        $audit = $this->audit($facts, $strategy, $copy, $seo);
      }
    }

    $payload = $this->mergeV2Payload($copy, $seo, $audit);

    $payload = $this->postProcessor->sanitize($payload, $facts->toArray());

    $gateFailures = app(EventContentQualityGate::class)->failures($payload, $facts);
    if ($gateFailures !== []) {
      throw new RuntimeException('El paquete generado no pasó los gates deterministas: ' . implode(' ', $gateFailures));
    }

    $policyFailures = app(EventContentPolicyGate::class)->failures($payload, $facts);
    if ($policyFailures !== []) {
      throw new RuntimeException('El paquete generado no pasó los gates de política: ' . implode(' ', $policyFailures));
    }

    $genericness = app(EventContentGenericnessGate::class)->score($payload, $facts);
    if ((float) ($genericness['score'] ?? 0) > 4) {
      $signals = implode(' ', $genericness['signals'] ?? []);
      throw new RuntimeException('El paquete generado no pasó el gate de genericidad: score ' . $genericness['score'] . '/10. ' . $signals);
    }

    return $payload;
  }

  private function strategize(CanonicalEventFacts $facts, array $preferences, string $archetype): array
  {
    $prompt = app(EventStrategyPrompt::class);
    $instructions = str_replace('__ARCHETYPE_GUIDE__', $prompt->archetypeGuide(), $prompt->instructions());

    $strategy = $this->assistant->createStructured(
      config('openai.event_assistant.models.generate', 'gpt-5.6-terra'),
      $instructions,
      [[
        'role' => 'user',
        'content' => [
          ['type' => 'input_text', 'text' => $prompt->build($facts->toArray(), app(PlatformContext::class)->toArray(), $preferences)],
        ],
      ]],
      EventStrategySchema::NAME,
      EventStrategySchema::toArray(),
    );

    $strategy['event_archetype'] = $strategy['event_archetype'] ?: $archetype;

    return $strategy;
  }

  private function copy(CanonicalEventFacts $facts, array $strategy, array $preferences, ?string $fewShot): array
  {
    $prompt = app(EventCopyPrompt::class);

    return $this->assistant->createStructured(
      config('openai.event_assistant.models.generate', 'gpt-5.6-terra'),
      $prompt->instructions(),
      [[
        'role' => 'user',
        'content' => [
          ['type' => 'input_text', 'text' => $prompt->build($facts->toArray(), $strategy, $preferences, $fewShot)],
        ],
      ]],
      EventCopySchema::NAME,
      EventCopySchema::toArray(),
    );
  }

  private function seo(CanonicalEventFacts $facts, array $copy): array
  {
    $prompt = app(EventSeoPrompt::class);

    return $this->assistant->createStructured(
      config('openai.event_assistant.models.generate', 'gpt-5.6-terra'),
      $prompt->instructions(),
      [[
        'role' => 'user',
        'content' => [
          ['type' => 'input_text', 'text' => $prompt->build($facts->toArray(), $copy, $facts->locale())],
        ],
      ]],
      EventSeoSchema::NAME,
      EventSeoSchema::toArray(),
    );
  }

  private function audit(CanonicalEventFacts $facts, array $strategy, array $copy, array $seo): array
  {
    $prompt = app(EventAuditPrompt::class);

    return $this->assistant->createStructured(
      config('openai.event_assistant.models.audit', 'gpt-5.6-terra'),
      $prompt->instructions(),
      [[
        'role' => 'user',
        'content' => [
          ['type' => 'input_text', 'text' => $prompt->build($facts->toArray(), $strategy, $copy, $seo)],
        ],
      ]],
      EventAuditSchema::NAME,
      EventAuditSchema::toArray(),
    );
  }

  private function repair(array $copy, array $failures, array $repairInstructions, CanonicalEventFacts $facts): array
  {
    $prompt = app(EventRepairPrompt::class);

    return $this->assistant->createStructured(
      config('openai.event_assistant.models.generate', 'gpt-5.6-terra'),
      $prompt->instructions(),
      [[
        'role' => 'user',
        'content' => [
          ['type' => 'input_text', 'text' => $prompt->build($copy, $failures, $repairInstructions, $facts->toArray())],
        ],
      ]],
      EventCopySchema::NAME,
      EventCopySchema::toArray(),
    );
  }

  /**
   * Fusiona las etapas V3 en el payload con forma V2 (compatible con UI/DB actuales).
   */
  private function mergeV2Payload(array $copy, array $seo, ?array $audit): array
  {
    $payload = [
      'content' => $copy['content'] ?? [],
      'social' => $copy['social'] ?? [],
      'faq' => $copy['faq'] ?? [],
      'review_checklist' => $copy['review_checklist'] ?? [],
      'missing_information' => $copy['missing_information'] ?? [],
      'seo' => $seo,
    ];

    $payload['audit'] = $this->normalizeAudit($audit);

    return $payload;
  }

  private function normalizeAudit(?array $audit): array
  {
    if (!$audit) {
      return [
        'status' => 'needs_human_review',
        'needs_human_review' => true,
        'warnings' => ['Auditoría no disponible en esta ejecución.'],
        'policy_notes' => [],
      ];
    }

    $blocked = $audit['status'] === 'blocked';
    $repair = $audit['status'] === 'repair';
    $needsHumanReview = $blocked;

    return [
      'status' => $blocked ? 'needs_human_review' : ($repair ? 'repaired' : 'passed'),
      'needs_human_review' => $needsHumanReview,
      'warnings' => array_slice(array_merge($audit['unsupported_claims'] ?? [], $audit['contradictions'] ?? []), 0, 8),
      'policy_notes' => array_slice($audit['stale_fact_risks'] ?? [], 0, 8),
    ];
  }
}
