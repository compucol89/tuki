<?php

namespace App\Jobs;

use App\Exceptions\OpenAiNonRetryableException;
use App\Models\Event\EventAiContentDraft;
use App\Services\OpenAI\EventAiAssistantService;
use App\Support\EventAiDraftPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class GenerateEventContentDraftJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public int $tries = 2;
  public int $backoff = 90;
  public int $timeout = 300;

  public function __construct(private int $draftId)
  {
    $this->onQueue(config('openai.event_assistant.queue', 'ai-content'));
  }

  public function handle(EventAiAssistantService $assistant): void
  {
    $draft = EventAiContentDraft::with(['review', 'run'])->findOrFail($this->draftId);
    $startedAt = microtime(true);

    try {
      $draft->update(['status' => 'running']);
      $draft->run?->markRunning();
      $draft->run?->markProgress(5, 'Preparando información', 'Estamos reuniendo datos del evento, flyer y preferencias del copy.');

      $preferences = EventAiDraftPreferences::fromReview($draft->review);

      $draft->run?->markProgress(25, 'Adaptando el enfoque comercial', 'Tomamos público, tono, objetivo e intereses para orientar el mensaje.');
      $generated = $assistant->generateContent($draft->review->canonical_event_facts ?? [], $preferences);

      $draft->run?->markProgress(75, 'Revisando copy y SEO', 'Validamos consistencia, políticas y textos para Google y redes.');
      $moderationSource = trim(implode("\n", array_filter([
        data_get($generated, 'content.public_title'),
        data_get($generated, 'content.short_description'),
        data_get($generated, 'content.main_description'),
        data_get($generated, 'seo.meta_description'),
        data_get($generated, 'social.instagram_caption'),
      ])));
      $moderation = $assistant->moderateText(mb_substr($moderationSource !== '' ? $moderationSource : 'evento', 0, 4000));
      $audit = $generated['audit'] ?? [];
      $moderationFlagged = (bool) data_get($moderation, 'results.0.flagged', false);
      $needsHumanReview = (bool) ($audit['needs_human_review'] ?? false) || $moderationFlagged;

      $draft->run?->markProgress(95, 'Guardando resultado', 'Estamos dejando listo el copy para revisar y aplicar.');
      $auditPayload = array_merge($audit, [
        'moderation_flagged' => $moderationFlagged,
        'moderation_categories' => data_get($moderation, 'results.0.categories'),
      ]);

      // Post-proceso determinista (mismo filtro que el preview del controller):
      // elimina FAQs con datos ausentes/notas internas y completa fallbacks antes de persistir.
      $generated = app(\App\Services\EventAi\EventAiDraftPostProcessor::class)
        ->sanitize($generated, $draft->review->canonical_event_facts ?? []);

      // Guardar el draft PRIMERO (fuente de verdad del copy). markCompleted del run
      // va slim: el payload completo duplicado colgaba/fallaba el UPDATE y dejaba el UI en 95%.
      $draft->update([
        'status' => 'completed',
        'generated_payload' => $generated,
        'audit_payload' => $auditPayload,
        'audit_status' => EventAiContentDraft::normalizeAuditStatus(
          $moderationFlagged ? 'moderation_review' : ($audit['status'] ?? ($needsHumanReview ? 'needs_human_review' : 'passed'))
        ),
        'needs_human_review' => $needsHumanReview,
      ]);

      $durationMs = (int) ((microtime(true) - $startedAt) * 1000);
      try {
        $draft->run?->markCompleted([
          'draft_id' => $draft->id,
          'saved_to_draft' => true,
        ], $durationMs, [
          'status' => $moderationFlagged ? 'moderation_review' : ($audit['status'] ?? ($needsHumanReview ? 'needs_human_review' : 'passed')),
          'needs_human_review' => $needsHumanReview,
          'moderation_flagged' => $moderationFlagged,
        ]);
      } catch (Throwable $e) {
        // El copy ya está en el draft; el status endpoint sana el run.
        report($e);
      }
    } catch (OpenAiNonRetryableException $e) {
      $draft->update(['status' => 'failed']);
      $draft->run?->markFailed($e->getMessage());
    } catch (Throwable $e) {
      $draft->update(['status' => 'failed']);
      $draft->run?->markFailed($e->getMessage());
      throw $e;
    }
  }
}
