<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Exceptions\OpenAiNonRetryableException;
use App\Jobs\AnalyzeEventFlyerJob;
use App\Jobs\GenerateEventContentDraftJob;
use App\Models\Event;
use App\Models\Event\EventAiAssistantReview;
use App\Models\Event\EventAiAssistantRun;
use App\Models\Event\EventAiContentDraft;
use App\Models\Event\EventContent;
use App\Models\Language;
use App\Services\EventAi\EventAiUsageLimiter;
use App\Services\OpenAI\EventAiAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Mews\Purifier\Facades\Purifier;
use RuntimeException;
use Throwable;

class EventAiAssistantController extends Controller
{
  public function analyzeTemporaryCover(Request $request, EventAiAssistantService $assistant): JsonResponse
  {
    if (!config('features.event_ai_assistant_enabled', false)) {
      return response()->json([
        'error' => 'ai_assistant_disabled',
        'message' => 'El asistente IA para eventos no está habilitado.',
      ], 503);
    }

    if (empty(config('openai.api_key'))) {
      return response()->json([
        'error' => 'openai_not_configured',
        'message' => 'Falta configurar OPENAI_API_KEY.',
      ], 503);
    }

    $data = $request->validate([
      'thumbnail' => 'required|file|mimes:jpeg,jpg,png,webp|max:' . (int) config('openai.reference.max_size_kb', 10240),
      'event_type' => 'nullable|string|max:30',
      'date_type' => 'nullable|string|max:30',
      'start_date' => 'nullable|string|max:30',
      'start_time' => 'nullable|string|max:30',
      'end_date' => 'nullable|string|max:30',
      'end_time' => 'nullable|string|max:30',
      'generate_content' => 'nullable|boolean',
      'ai_tone' => 'required_if:generate_content,1|string|max:60',
      'ai_intensity' => 'required_if:generate_content,1|string|max:30',
      'ai_audience_location' => 'required_if:generate_content,1|array|min:1',
      'ai_audience_location.*' => 'nullable|string|max:80',
      'ai_community' => 'required_if:generate_content,1|array|min:1',
      'ai_community.*' => 'nullable|string|max:80',
      'ai_age_range' => 'required_if:generate_content,1|array|min:1',
      'ai_age_range.*' => 'nullable|string|max:80',
      'ai_interests' => 'required_if:generate_content,1|array|min:1',
      'ai_interests.*' => 'nullable|string|max:80',
      'ai_language_style' => 'required_if:generate_content,1|string|max:80',
      'ai_event_brief' => 'required_if:generate_content,1|string|min:20|max:700',
      'ai_audience' => 'nullable|string|max:700',
      'ai_goal' => 'required_if:generate_content,1|string|max:80',
      'ai_selling_angle' => 'nullable|string|max:240',
      'ai_notes' => 'nullable|string|max:1200',
    ]);

    $imagePath = $request->file('thumbnail')->getRealPath();
    if (!$imagePath || !is_file($imagePath)) {
      return response()->json([
        'error' => 'thumbnail_not_found',
        'message' => 'No pudimos leer la imagen de portada.',
      ], 422);
    }

    $imageError = $this->validateAnalysisImage($imagePath);
    if ($imageError) {
      return response()->json($imageError, 422);
    }

    $quota = $this->consumeTemporaryAnalysisQuota();
    if (!$quota['allowed']) {
      return response()->json([
        'error' => 'temporary_analysis_limit_reached',
        'message' => $quota['message'],
        'usage' => $quota,
      ], 429);
    }

    $formFacts = $this->temporaryFormFacts($request, $data);

    try {
      $moderation = $assistant->moderateImageAndText($imagePath, implode("\n", array_filter([
        $formFacts['title'] ?? null,
        $formFacts['description'] ?? null,
        $formFacts['address'] ?? null,
      ])));

      if ((bool) data_get($moderation, 'results.0.flagged', false)) {
        return response()->json([
          'error' => 'content_needs_review',
          'message' => 'La imagen o el texto necesitan revisión antes de usar el asistente IA.',
        ], 422);
      }

      $analysis = $assistant->analyzeFlyer($imagePath, $formFacts);
    } catch (OpenAiNonRetryableException $e) {
      return response()->json([
        'error' => 'ai_analysis_failed',
        'message' => $e->getMessage(),
      ], 422);
    } catch (Throwable $e) {
      report($e);

      return response()->json([
        'error' => 'ai_analysis_failed',
        'message' => 'No pudimos analizar la portada en este momento. Intentá de nuevo en unos minutos.',
      ], 503);
    }

    $canonicalFacts = $this->temporaryCanonicalFacts($formFacts, $analysis);
    $draft = null;
    $draftError = null;

    if ($request->boolean('generate_content', false)) {
      try {
        $generated = $this->generateContentWithQualityGate($assistant, $canonicalFacts, $this->temporaryPreferences($request));
        $moderation = $assistant->moderateText(json_encode($generated, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '');
        $audit = $generated['audit'] ?? [];
        $moderationFlagged = (bool) data_get($moderation, 'results.0.flagged', false);
        $needsHumanReview = (bool) ($audit['needs_human_review'] ?? false) || $moderationFlagged;

        $draft = [
          'id' => null,
          'status' => 'temporary',
          'needs_human_review' => $needsHumanReview,
          'audit_status' => $moderationFlagged ? 'moderation_review' : ($audit['status'] ?? ($needsHumanReview ? 'needs_human_review' : 'passed')),
          'generated_payload' => $generated,
          'audit_payload' => array_merge($audit, ['moderation' => $moderation]),
        ];
      } catch (OpenAiNonRetryableException $e) {
        $draftError = $e->getMessage();
      } catch (RuntimeException $e) {
        $draftError = 'La IA devolvió una propuesta incompleta y no la aplicamos. Probá ajustar las preferencias o volver a generar el evento.';
      } catch (Throwable $e) {
        report($e);
        $draftError = 'No pudimos generar el copy y SEO en este momento. Podés aplicar los datos detectados y completar el texto manualmente.';
      }
    }

    return response()->json([
      'status' => 'completed',
      'usage' => $quota,
      'review' => [
        'id' => null,
        'status' => 'temporary',
        'canonical_event_facts' => $canonicalFacts,
      ],
      'draft' => $draft,
      'draft_error' => $draftError,
    ]);
  }

  public function startAnalysis(Request $request, Event $event, EventAiUsageLimiter $limiter): JsonResponse
  {
    if (!$this->canManageEvent($event)) {
      return response()->json(['error' => 'forbidden'], 403);
    }

    if (!config('features.event_ai_assistant_enabled', false)) {
      return response()->json([
        'error' => 'ai_assistant_disabled',
        'message' => 'El asistente IA para eventos no está habilitado.',
      ], 503);
    }

    if (empty(config('openai.api_key'))) {
      return response()->json([
        'error' => 'openai_not_configured',
        'message' => 'Falta configurar OPENAI_API_KEY.',
      ], 503);
    }

    if (empty($event->thumbnail)) {
      return response()->json([
        'error' => 'thumbnail_required',
        'message' => 'Subí y guardá una imagen de portada antes de usar el asistente IA.',
      ], 422);
    }

    $imagePath = public_path('assets/admin/img/event/thumbnail/' . $event->thumbnail);
    if (!is_file($imagePath)) {
      return response()->json([
        'error' => 'thumbnail_not_found',
        'message' => 'No encontramos el archivo de la imagen de portada.',
      ], 422);
    }

    $imageError = $this->validateAnalysisImage($imagePath);
    if ($imageError) {
      return response()->json($imageError, 422);
    }

    if (empty($event->organizer_id)) {
      return response()->json([
        'error' => 'organizer_required',
        'message' => 'Asigná un organizador al evento antes de usar el asistente IA.',
      ], 422);
    }

    $isAdminRequest = $this->isAdminRequest();

    $result = DB::transaction(function () use ($event, $imagePath, $limiter, $isAdminRequest) {
      Event::whereKey($event->id)->lockForUpdate()->first();

      $active = EventAiAssistantRun::where('event_id', $event->id)
        ->where('organizer_id', $event->organizer_id)
        ->where('type', 'analysis')
        ->whereIn('status', ['pending', 'running'])
        ->exists();

      if ($active) {
        return ['response' => response()->json([
          'error' => 'analysis_in_progress',
          'message' => 'Ya hay un análisis IA en curso para este evento.',
        ], 409)];
      }

      $usage = $isAdminRequest
        ? $this->unlimitedUsagePayload('analysis')
        : $limiter->check($event->id, $event->organizer_id);
      if (!$isAdminRequest && !$usage['allowed']) {
        return ['response' => response()->json([
          'error' => $usage['reason'],
          'message' => $usage['message'],
          'usage' => $usage,
        ], 429)];
      }

      $run = EventAiAssistantRun::create([
        'event_id' => $event->id,
        'organizer_id' => $event->organizer_id,
        'type' => 'analysis',
        'status' => 'pending',
        'model' => config('openai.event_assistant.models.extract', 'gpt-5.6-luna'),
        'prompt_version' => config('openai.event_assistant.prompt_version', '2026-07-23-v2'),
        'source_image_path' => 'assets/admin/img/event/thumbnail/' . $event->thumbnail,
        'source_image_hash' => hash_file('sha256', $imagePath),
        'input_payload' => [
          'actor_guard' => $isAdminRequest ? 'admin' : 'organizer',
          'actor_id' => optional(Auth::guard($isAdminRequest ? 'admin' : 'organizer')->user())->id,
          'progress' => [
            'percent' => 0,
            'stage' => 'Esperando turno de procesamiento',
            'message' => 'El análisis quedó en cola y empezará en unos segundos.',
            'is_estimated' => true,
            'updated_at' => now()->toIso8601String(),
          ],
        ],
      ]);

      return [
        'run' => $run,
        'usage' => $usage,
      ];
    });

    if (isset($result['response'])) {
      return $result['response'];
    }

    $run = $result['run'];

    AnalyzeEventFlyerJob::dispatch($run->id);

    return response()->json([
      'status' => 'dispatched',
      'run_id' => $run->id,
      'usage' => $result['usage'],
    ], 202);
  }

  public function status(Request $request, Event $event, EventAiUsageLimiter $limiter): JsonResponse
  {
    if (!$this->canManageEvent($event)) {
      return response()->json(['error' => 'forbidden'], 403);
    }

    $analysisRun = EventAiAssistantRun::where('event_id', $event->id)
      ->where('organizer_id', $event->organizer_id)
      ->where('type', 'analysis')
      ->latest()
      ->first();

    $review = EventAiAssistantReview::where('event_id', $event->id)
      ->where('organizer_id', $event->organizer_id)
      ->latest()
      ->first();

    $draft = EventAiContentDraft::with('run')
      ->where('event_id', $event->id)
      ->where('organizer_id', $event->organizer_id)
      ->latest()
      ->first();

    return response()->json([
      'enabled' => (bool) config('features.event_ai_assistant_enabled', false),
      'usage' => $this->isAdminRequest()
        ? $this->unlimitedUsagePayload('analysis')
        : $limiter->check($event->id, $event->organizer_id),
      'content_usage' => $this->isAdminRequest()
        ? $this->unlimitedUsagePayload('content')
        : $limiter->checkContent($event->id, $event->organizer_id),
      'analysis' => $this->runPayload($analysisRun),
      'review' => $review ? [
        'id' => $review->id,
        'status' => $review->status,
        'tone' => $review->tone,
        'intensity' => $review->intensity,
        'canonical_event_facts' => $review->canonical_event_facts,
      ] : null,
      'draft' => $draft ? [
        'id' => $draft->id,
        'status' => $draft->status,
        'needs_human_review' => $draft->needs_human_review,
        'audit_status' => $draft->audit_status,
        'generated_payload' => $draft->generated_payload,
        'audit_payload' => $draft->audit_payload,
        'run' => $this->runPayload($draft->run),
      ] : null,
    ]);
  }

  public function updateReview(Request $request, Event $event): JsonResponse
  {
    if (!$this->canManageEvent($event)) {
      return response()->json(['error' => 'forbidden'], 403);
    }

    $review = EventAiAssistantReview::where('event_id', $event->id)
      ->where('organizer_id', $event->organizer_id)
      ->latest()
      ->first();

    if (!$review) {
      return response()->json([
        'error' => 'review_not_found',
        'message' => 'Todavía no hay un análisis listo para revisar.',
      ], 404);
    }

    $data = $request->validate([
      'tone' => 'nullable|string|max:60',
      'intensity' => 'nullable|string|max:30',
      'audience' => 'nullable|array',
      'canonical_event_facts' => 'nullable|array',
      'accepted_fields' => 'nullable|array',
      'ignored_fields' => 'nullable|array',
    ]);

    $review->update([
      'tone' => $data['tone'] ?? $review->tone,
      'intensity' => $data['intensity'] ?? $review->intensity,
      'audience_payload' => $data['audience'] ?? $review->audience_payload,
      'canonical_event_facts' => $data['canonical_event_facts'] ?? $review->canonical_event_facts,
      'accepted_fields' => $data['accepted_fields'] ?? $review->accepted_fields,
      'ignored_fields' => $data['ignored_fields'] ?? $review->ignored_fields,
      'status' => 'reviewed',
      'reviewed_at' => now(),
    ]);

    return response()->json(['status' => 'saved', 'review_id' => $review->id]);
  }

  public function generateDraft(Request $request, Event $event, EventAiUsageLimiter $limiter): JsonResponse
  {
    if (!$this->canManageEvent($event)) {
      return response()->json(['error' => 'forbidden'], 403);
    }

    if (!config('features.event_ai_assistant_enabled', false)) {
      return response()->json(['error' => 'ai_assistant_disabled'], 503);
    }

    $data = $request->validate([
      'tone' => 'required|string|max:60',
      'intensity' => 'required|string|max:30',
      'audience' => 'nullable|array',
    ]);

    $review = EventAiAssistantReview::where('event_id', $event->id)
      ->where('organizer_id', $event->organizer_id)
      ->latest()
      ->first();

    if (!$review) {
      return response()->json([
        'error' => 'review_required',
        'message' => 'Primero analizá el flyer y revisá los datos detectados.',
      ], 422);
    }

    if (empty($event->organizer_id)) {
      return response()->json([
        'error' => 'organizer_required',
        'message' => 'Asigná un organizador al evento antes de generar copy IA.',
      ], 422);
    }

    $isAdminRequest = $this->isAdminRequest();

    $result = DB::transaction(function () use ($event, $review, $data, $limiter, $isAdminRequest) {
      Event::whereKey($event->id)->lockForUpdate()->first();

      $active = EventAiContentDraft::where('event_id', $event->id)
        ->where('organizer_id', $event->organizer_id)
        ->whereIn('status', ['pending', 'running'])
        ->exists();

      if ($active) {
        return ['response' => response()->json([
          'error' => 'draft_in_progress',
          'message' => 'Ya hay una generación de copy en curso para este evento.',
        ], 409)];
      }

      $usage = $isAdminRequest
        ? $this->unlimitedUsagePayload('content')
        : $limiter->checkContent($event->id, $event->organizer_id);
      if (!$isAdminRequest && !$usage['allowed']) {
        return ['response' => response()->json([
          'error' => $usage['reason'],
          'message' => $usage['message'],
          'usage' => $usage,
        ], 429)];
      }

      $review->update([
        'tone' => $data['tone'],
        'intensity' => $data['intensity'],
        'audience_payload' => $data['audience'] ?? [],
        'status' => 'reviewed',
        'reviewed_at' => now(),
      ]);

      $run = EventAiAssistantRun::create([
        'event_id' => $event->id,
        'organizer_id' => $event->organizer_id,
        'type' => 'content',
        'status' => 'pending',
        'model' => config('openai.event_assistant.models.generate', 'gpt-5.6-terra'),
        'prompt_version' => config('openai.event_assistant.prompt_version', '2026-07-23-v2'),
        'input_payload' => [
          'actor_guard' => $isAdminRequest ? 'admin' : 'organizer',
          'actor_id' => optional(Auth::guard($isAdminRequest ? 'admin' : 'organizer')->user())->id,
          'review_id' => $review->id,
          'tone' => $data['tone'],
          'intensity' => $data['intensity'],
          'audience' => $data['audience'] ?? [],
          'progress' => [
            'percent' => 0,
            'stage' => 'Esperando turno de procesamiento',
            'message' => 'La generación de copy quedó en cola y empezará en unos segundos.',
            'is_estimated' => true,
            'updated_at' => now()->toIso8601String(),
          ],
        ],
      ]);

      $draft = EventAiContentDraft::create([
        'review_id' => $review->id,
        'run_id' => $run->id,
        'event_id' => $event->id,
        'organizer_id' => $event->organizer_id,
        'status' => 'pending',
      ]);

      return ['run' => $run, 'draft' => $draft];
    });

    if (isset($result['response'])) {
      return $result['response'];
    }

    $run = $result['run'];
    $draft = $result['draft'];

    GenerateEventContentDraftJob::dispatch($draft->id);

    return response()->json([
      'status' => 'dispatched',
      'draft_id' => $draft->id,
      'run_id' => $run->id,
    ], 202);
  }

  public function applyDraft(Request $request, Event $event, EventAiContentDraft $draft): JsonResponse
  {
    if (!$this->canManageEvent($event) || (int) $draft->event_id !== (int) $event->id || (int) $draft->organizer_id !== (int) $event->organizer_id) {
      return response()->json(['error' => 'forbidden'], 403);
    }

    if ($draft->status !== 'completed' || empty($draft->generated_payload)) {
      return response()->json([
        'error' => 'draft_not_ready',
        'message' => 'El copy todavía no está listo para aplicar.',
      ], 422);
    }

    $data = $request->validate([
      'fields' => 'required|array|min:1',
      'fields.*' => 'string',
    ]);

    $allowed = array_intersect($data['fields'], ['title', 'description', 'meta_keywords', 'meta_description']);
    if (empty($allowed)) {
      return response()->json([
        'error' => 'empty_selection',
        'message' => 'Seleccioná al menos un campo válido para aplicar.',
      ], 422);
    }

    $payload = $draft->generated_payload;
    $content = $this->defaultContent($event);
    if (!$content) {
      return response()->json([
        'error' => 'event_content_not_found',
        'message' => 'No encontramos el contenido principal del evento.',
      ], 404);
    }

    DB::transaction(function () use ($content, $payload, $allowed, $draft) {
      if (in_array('title', $allowed, true)) {
        $content->title = data_get($payload, 'content.public_title', $content->title);
      }

      if (in_array('description', $allowed, true)) {
        $content->description = Purifier::clean($this->htmlDescription($payload), 'youtube');
      }

      if (in_array('meta_keywords', $allowed, true)) {
        $tags = data_get($payload, 'seo.tags', []);
        $secondary = data_get($payload, 'seo.secondary_keywords', []);
        $content->meta_keywords = implode(',', array_values(array_unique(array_filter(array_merge($tags, $secondary)))));
      }

      if (in_array('meta_description', $allowed, true)) {
        $content->meta_description = data_get($payload, 'seo.google_short_description')
          ?: data_get($payload, 'seo.meta_description', $content->meta_description);
      }

      $content->save();
      $draft->update(['applied_at' => now()]);
    });

    return response()->json(['status' => 'applied', 'fields' => array_values($allowed)]);
  }

  private function canManageEvent(Event $event): bool
  {
    if (Auth::guard('admin')->check()) {
      return true;
    }

    $organizer = Auth::guard('organizer')->user();

    return $organizer && (int) $event->organizer_id === (int) $organizer->id;
  }

  private function isAdminRequest(): bool
  {
    return Auth::guard('admin')->check();
  }

  private function unlimitedUsagePayload(string $type): array
  {
    return [
      'allowed' => true,
      'is_unlimited' => true,
      'reason' => null,
      'max_event_runs' => 999,
      'used_event_runs' => 0,
      'remaining_event_runs' => 999,
      'max_daily_runs' => 999,
      'used_daily_runs' => 0,
      'remaining_daily_runs' => 999,
      'message' => 'Modo admin: IA sin límites para pruebas.',
    ];
  }

  private function consumeTemporaryAnalysisQuota(): array
  {
    if ($this->isAdminRequest()) {
      return $this->unlimitedUsagePayload('analysis');
    }

    $organizer = Auth::guard('organizer')->user();
    $limit = max(0, (int) config('openai.event_assistant.limits.max_temp_cover_analysis_per_organizer_day', 2));

    if (!$organizer || $limit === 0) {
      return [
        'allowed' => false,
        'message' => 'No hay análisis IA disponibles para esta cuenta.',
      ];
    }

    $key = 'event_ai_temp_cover_analysis:' . $organizer->id . ':' . now()->toDateString();
    $used = (int) Cache::get($key, 0);

    if ($used >= $limit) {
      return [
        'allowed' => false,
        'max_daily_runs' => $limit,
        'used_daily_runs' => $used,
        'remaining_daily_runs' => 0,
        'message' => 'Ya usaste los análisis IA disponibles para crear eventos hoy.',
      ];
    }

    Cache::put($key, $used + 1, now()->endOfDay());

    return [
      'allowed' => true,
      'max_daily_runs' => $limit,
      'used_daily_runs' => $used + 1,
      'remaining_daily_runs' => max($limit - ($used + 1), 0),
      'message' => null,
    ];
  }

  private function temporaryFormFacts(Request $request, array $data): array
  {
    $defaultLanguage = Language::where('is_default', 1)->first();
    $code = $defaultLanguage?->code ?: 'es';

    return [
      'event_id' => null,
      'event_type' => $data['event_type'] ?? $request->input('event_type'),
      'date_type' => $data['date_type'] ?? $request->input('date_type'),
      'start_date' => $data['start_date'] ?? $request->input('start_date'),
      'start_time' => $data['start_time'] ?? $request->input('start_time'),
      'end_date' => $data['end_date'] ?? $request->input('end_date'),
      'end_time' => $data['end_time'] ?? $request->input('end_time'),
      'duration' => null,
      'category' => $this->categoryName($request->input($code . '_category_id')),
      'title' => $request->input($code . '_title'),
      'description' => $request->input($code . '_description') ? trim(strip_tags($request->input($code . '_description'))) : null,
      'address' => $request->input($code . '_address'),
      'city' => $request->input($code . '_city'),
      'state' => $request->input($code . '_state'),
      'country' => $request->input($code . '_country'),
      'zip_code' => $request->input($code . '_zip_code'),
      'has_thumbnail' => true,
      'timezone' => config('app.timezone', 'America/Argentina/Buenos_Aires'),
    ];
  }

  private function categoryName($categoryId): ?string
  {
    if (empty($categoryId)) {
      return null;
    }

    return \App\Models\Event\EventCategory::whereKey($categoryId)->value('name');
  }

  private function temporaryCanonicalFacts(array $formFacts, array $analysis): array
  {
    return [
      'source_priority' => [
        'confirmed_form_fields',
        'organizer_free_text',
        'organizer_notes',
        'organizer_review',
        'accepted_image_fields',
        'marketing_inference',
      ],
      'form_facts' => $formFacts,
      'image_analysis' => [
        'summary' => $analysis['summary'] ?? '',
        'extracted_fields' => $analysis['extracted_fields'] ?? [],
        'found_information' => $analysis['found_information'] ?? [],
        'complementary_information' => $analysis['complementary_information'] ?? [],
        'optional_suggestions' => $analysis['optional_suggestions'] ?? [],
        'critical_differences' => $analysis['critical_differences'] ?? [],
        'conflicts' => $analysis['conflicts'] ?? [],
        'missing_information' => $analysis['missing_information'] ?? [],
        'sensitive_fields' => $analysis['sensitive_fields'] ?? [],
        'sponsors' => $analysis['sponsors'] ?? [],
        'warnings' => $analysis['warnings'] ?? [],
      ],
      'confirmed_fields' => [],
      'ignored_fields' => [],
      'locale' => 'es-AR',
      'timezone' => config('app.timezone', 'America/Argentina/Buenos_Aires'),
    ];
  }

  private function temporaryPreferences(Request $request): array
  {
    return [
      'tone' => $request->input('ai_tone', 'cercano_rioplatense'),
      'intensity' => $request->input('ai_intensity', 'equilibrado'),
      'event_brief' => $request->input('ai_event_brief'),
      'audience' => [
        'locations' => $this->temporaryPreferenceArray($request, 'ai_audience_location', ['argentina']),
        'communities' => $this->temporaryPreferenceArray($request, 'ai_community', ['publico_argentino']),
        'age_ranges' => $this->temporaryPreferenceArray($request, 'ai_age_range'),
        'interests' => $this->temporaryPreferenceArray($request, 'ai_interests'),
        'language_style' => $request->input('ai_language_style', 'automatico'),
        'description' => $request->input('ai_audience'),
        'goal' => $request->input('ai_goal', 'reservas_equilibradas'),
        'selling_angle' => $request->input('ai_selling_angle'),
        'organizer_notes' => $request->input('ai_notes'),
      ],
      'locale' => 'es-AR',
      'timezone' => config('app.timezone', 'America/Argentina/Buenos_Aires'),
      'creation_flow' => true,
      'human_review_required' => true,
    ];
  }

  private function temporaryPreferenceArray(Request $request, string $key, array $default = []): array
  {
    $value = $request->input($key, $default);
    if (!is_array($value)) {
      $value = array_filter(array_map('trim', explode(',', (string) $value)));
    }

    return array_values(array_unique(array_filter(array_map(fn($item) => mb_substr((string) $item, 0, 80), $value))));
  }

  private function strengthenGeneratedDraft(array $generated, array $canonicalFacts, bool $allowFallback = true): array
  {
    $content = $generated['content'] ?? [];
    $sourceTitle = data_get($canonicalFacts, 'form_facts.title') ?: $this->canonicalFactValue($canonicalFacts, ['titulo', 'título', 'nombre del evento']);
    $venue = $this->canonicalFactValue($canonicalFacts, ['marca o nombre visible', 'venue', 'lugar', 'sede']) ?: data_get($canonicalFacts, 'form_facts.venue');
    $style = $this->canonicalFactValue($canonicalFacts, ['subtitulo', 'subtítulo', 'estilo', 'musical']);
    $city = data_get($canonicalFacts, 'form_facts.city') ?: data_get($canonicalFacts, 'form_facts.state');

    $strongTitle = $this->strongEventTitle($content['public_title'] ?? '', $sourceTitle, $venue, $style, $city);
    if ($strongTitle) {
      $generated['content']['public_title'] = $strongTitle;
    }

    $options = $content['title_options'] ?? [];
    if (!is_array($options)) {
      $options = [];
    }

    $generated['content']['title_options'] = array_values(array_unique(array_filter(array_merge(
      [$generated['content']['public_title'] ?? null],
      $options,
      $this->fallbackTitleOptions($sourceTitle, $venue, $style, $city)
    ))));

    if ($allowFallback && (empty(trim(strip_tags($content['main_description'] ?? ''))) || mb_strlen(trim(strip_tags($content['main_description'] ?? ''))) < 450)) {
      $generated['content']['main_description'] = $this->fallbackMainDescription($generated, $canonicalFacts);
    }

    return $allowFallback ? $this->normalizeGeneratedPackage($generated, $canonicalFacts) : $generated;
  }

  private function generateContentWithQualityGate(EventAiAssistantService $assistant, array $canonicalFacts, array $preferences): array
  {
    $lastFailures = [];

    for ($attempt = 1; $attempt <= 2; $attempt++) {
      $attemptPreferences = $preferences;
      if ($attempt > 1) {
        $attemptPreferences['quality_retry'] = [
          'previous_failures' => $lastFailures,
          'instruction' => 'Regenerá el paquete completo. No devuelvas una propuesta mínima ni OCR. Cumplí título fuerte, descripción completa, FAQ, OG, resumen IA, tags y checklist.',
        ];
      }

      $generated = $this->strengthenGeneratedDraft($assistant->generateContent($canonicalFacts, $attemptPreferences), $canonicalFacts, $attempt > 1);
      $failures = $this->draftQualityFailures($generated);

      if (empty($failures)) {
        return $this->normalizeGeneratedPackage($generated, $canonicalFacts);
      }

      $lastFailures = $failures;
    }

    throw new RuntimeException('La IA devolvió una propuesta incompleta: ' . implode(' ', $lastFailures));
  }

  private function normalizeGeneratedPackage(array $generated, array $canonicalFacts): array
  {
    $content = $generated['content'] ?? [];
    $seo = $generated['seo'] ?? [];
    $social = $generated['social'] ?? [];

    $title = $content['public_title'] ?? $this->canonicalFactValue($canonicalFacts, ['titulo', 'título', 'nombre del evento']) ?? 'Evento en Tukipass';
    $short = trim((string) ($content['short_description'] ?? ''));
    $main = trim((string) ($content['main_description'] ?? ''));
    $address = $this->canonicalFactValue($canonicalFacts, ['direccion', 'dirección', 'ubicacion', 'ubicación']);
    $promo = $this->canonicalFactValue($canonicalFacts, ['promocion', 'promoción', 'acceso']);

    if (empty($seo['ai_search_summary'])) {
      $generated['seo']['ai_search_summary'] = trim($title . ' es una publicación de evento en Tukipass. ' . ($short ?: strip_tags($main)) . ($address ? ' Dirección: ' . $address . '.' : '') . ($promo ? ' Dato destacado: ' . $promo . '.' : ''));
    }

    if (empty($social['open_graph_title'])) {
      $generated['social']['open_graph_title'] = mb_substr($title, 0, 70);
    }

    if (empty($social['open_graph_description'])) {
      $generated['social']['open_graph_description'] = mb_substr($short ?: strip_tags($main), 0, 220);
    }

    if (empty($generated['faq']) || !is_array($generated['faq'])) {
      $generated['faq'] = $this->fallbackFaq($generated, $canonicalFacts);
    }

    if (count($generated['faq']) < 4) {
      $generated['faq'] = array_slice(array_merge($generated['faq'], $this->fallbackFaq($generated, $canonicalFacts)), 0, 5);
    }

    if (empty($generated['review_checklist']) || !is_array($generated['review_checklist'])) {
      $generated['review_checklist'] = $this->fallbackReviewChecklist();
    }

    if (count($generated['review_checklist']) < 6) {
      $generated['review_checklist'] = array_slice(array_merge($generated['review_checklist'], $this->fallbackReviewChecklist()), 0, 8);
    }

    return $generated;
  }

  private function draftQualityFailures(array $generated): array
  {
    $content = $generated['content'] ?? [];
    $seo = $generated['seo'] ?? [];
    $social = $generated['social'] ?? [];
    $failures = [];

    if (mb_strlen(trim((string) ($content['public_title'] ?? ''))) < 24) {
      $failures[] = 'El título público es demasiado corto o genérico.';
    }
    if (count(array_filter($content['title_options'] ?? [])) < 4) {
      $failures[] = 'Faltan opciones de título.';
    }
    if (mb_strlen(trim(strip_tags((string) ($content['main_description'] ?? '')))) < 450) {
      $failures[] = 'La descripción principal es demasiado breve.';
    }
    if (count(array_filter($content['what_you_will_experience'] ?? [])) < 3) {
      $failures[] = 'Faltan beneficios o experiencia del evento.';
    }
    if (count(array_filter($content['important_information'] ?? [])) < 3) {
      $failures[] = 'Falta información importante para decidir la reserva.';
    }
    if (count(array_filter($seo['tags'] ?? [])) < 8) {
      $failures[] = 'Faltan tags SEO útiles.';
    }
    if (mb_strlen(trim((string) ($seo['google_short_description'] ?? ''))) < 110) {
      $failures[] = 'La descripción corta para Google es insuficiente.';
    }
    if (mb_strlen(trim((string) ($seo['ai_search_summary'] ?? ''))) < 160) {
      $failures[] = 'El resumen para agentes IA es insuficiente.';
    }
    if (mb_strlen(trim((string) ($social['open_graph_description'] ?? ''))) < 80) {
      $failures[] = 'La descripción Open Graph es insuficiente.';
    }
    if (count(array_filter($generated['faq'] ?? [])) < 4) {
      $failures[] = 'Faltan preguntas frecuentes para IA y Google.';
    }
    if (count(array_filter($generated['review_checklist'] ?? [])) < 6) {
      $failures[] = 'Falta checklist de revisión humana.';
    }

    return $failures;
  }

  private function canonicalFactValue(array $canonicalFacts, array $needles): ?string
  {
    $fields = array_merge(
      data_get($canonicalFacts, 'image_analysis.extracted_fields', []),
      data_get($canonicalFacts, 'image_analysis.sponsors', [])
    );

    foreach ($fields as $field) {
      $label = mb_strtolower((string) (($field['label'] ?? '') . ' ' . ($field['key'] ?? '')));
      foreach ($needles as $needle) {
        if (str_contains($label, mb_strtolower($needle))) {
          $value = trim((string) ($field['value'] ?? $field['raw_text'] ?? ''));
          return $value !== '' && $value !== '-' ? $value : null;
        }
      }
    }

    return null;
  }

  private function strongEventTitle(?string $current, ?string $sourceTitle, ?string $venue, ?string $style, ?string $city): ?string
  {
    $base = trim((string) ($current ?: $sourceTitle));
    if ($base === '') {
      return null;
    }

    $normalizedBase = mb_strtolower($base);
    $normalizedSource = mb_strtolower(trim((string) $sourceTitle));
    $isBare = $normalizedSource !== '' && ($normalizedBase === $normalizedSource || mb_strlen($base) < 24);

    if (!$isBare) {
      return $base;
    }

    if ($venue && $style) {
      return mb_substr($base . ' en ' . $venue . ': ' . mb_strtolower($style), 0, 90);
    }

    if ($venue) {
      return mb_substr($base . ' en ' . $venue, 0, 90);
    }

    if ($style) {
      return mb_substr($base . ': ' . mb_strtolower($style), 0, 90);
    }

    if ($city) {
      return mb_substr($base . ' en ' . $city, 0, 90);
    }

    return $base;
  }

  private function fallbackTitleOptions(?string $sourceTitle, ?string $venue, ?string $style, ?string $city): array
  {
    $base = trim((string) $sourceTitle);
    if ($base === '') {
      return [];
    }

    return array_values(array_filter([
      $venue ? mb_substr($base . ' en ' . $venue, 0, 90) : null,
      $style && $venue ? mb_substr($base . ': ' . mb_strtolower($style) . ' en ' . $venue, 0, 90) : null,
      $style ? mb_substr($base . ' | ' . $style, 0, 90) : null,
      $city ? mb_substr($base . ' en ' . $city, 0, 90) : null,
      $base,
    ]));
  }

  private function fallbackMainDescription(array $generated, array $canonicalFacts): string
  {
    $content = $generated['content'] ?? [];
    $title = $content['public_title'] ?? $this->canonicalFactValue($canonicalFacts, ['titulo', 'título', 'nombre del evento']) ?? 'Este evento';
    $venue = $this->canonicalFactValue($canonicalFacts, ['marca o nombre visible', 'venue', 'lugar', 'sede']);
    $style = $this->canonicalFactValue($canonicalFacts, ['subtitulo', 'subtítulo', 'estilo', 'musical']);
    $address = $this->canonicalFactValue($canonicalFacts, ['direccion', 'dirección', 'ubicacion', 'ubicación']);
    $date = $this->canonicalFactValue($canonicalFacts, ['fecha']);
    $promo = $this->canonicalFactValue($canonicalFacts, ['promocion', 'promoción', 'acceso']);

    $sentences = [
      $content['short_description'] ?? null,
      trim($title . ' propone una experiencia pensada para quienes buscan una salida clara, atractiva y fácil de reservar.'),
      $style ? 'La propuesta gira alrededor de ' . mb_strtolower($style) . ', con un enfoque directo para comunicar qué se vive y por qué vale la pena reservar.' : null,
      $venue || $address ? 'El evento se realiza' . ($venue ? ' en ' . $venue : '') . ($address ? ', en ' . $address : '') . '.' : null,
      $date ? 'Fecha visible o sugerida para revisar: ' . $date . '.' : null,
      $promo ? 'Dato destacado para comunicar: ' . $promo . '.' : null,
      'Revisá los datos finales de fecha, horario, acceso y condiciones antes de publicar para que la información quede completa y confiable.',
    ];

    return implode("\n\n", array_values(array_filter($sentences)));
  }

  private function fallbackFaq(array $generated, array $canonicalFacts): array
  {
    $title = data_get($generated, 'content.public_title') ?: $this->canonicalFactValue($canonicalFacts, ['titulo', 'título', 'nombre del evento']) ?: 'este evento';
    $address = $this->canonicalFactValue($canonicalFacts, ['direccion', 'dirección', 'ubicacion', 'ubicación']);
    $date = $this->canonicalFactValue($canonicalFacts, ['fecha']);
    $promo = $this->canonicalFactValue($canonicalFacts, ['promocion', 'promoción', 'acceso']);
    $style = $this->canonicalFactValue($canonicalFacts, ['subtitulo', 'subtítulo', 'estilo', 'musical']);

    return [
      [
        'question' => '¿Qué es ' . $title . '?',
        'answer' => $title . ' es una publicación de evento en Tukipass. La descripción del evento reúne los datos confirmados por el organizador y la información visible de la portada para ayudar a decidir la reserva.',
      ],
      [
        'question' => '¿Dónde se realiza el evento?',
        'answer' => $address ? 'El evento se realiza en ' . $address . '. Revisá la publicación final para confirmar indicaciones de ingreso, piso, sala o referencias adicionales.' : 'La ubicación debe confirmarse en la publicación final antes de reservar.',
      ],
      [
        'question' => '¿Cuándo es el evento?',
        'answer' => $date ? 'La fecha visible o informada para el evento es ' . $date . '. Antes de reservar, revisá que la publicación final incluya día, mes, año y horario.' : 'La fecha y el horario deben confirmarse en la publicación final antes de reservar.',
      ],
      [
        'question' => '¿Qué incluye la propuesta del evento?',
        'answer' => $style ? 'La propuesta comunica ' . mb_strtolower($style) . ' y una experiencia pensada para quienes buscan una salida clara y fácil de reservar.' : 'La propuesta del evento debe revisarse en la descripción final, donde el organizador informa experiencia, acceso, artistas, horarios y condiciones.',
      ],
      [
        'question' => '¿Hay promociones o condiciones especiales?',
        'answer' => $promo ? 'La promoción visible o informada es: ' . $promo . '. Las condiciones finales dependen del organizador y deben revisarse antes de reservar.' : 'Las promociones, precios y condiciones de acceso deben revisarse en la publicación final del evento.',
      ],
    ];
  }

  private function fallbackReviewChecklist(): array
  {
    return [
      ['label' => 'Título', 'status' => 'revisar', 'note' => 'Confirmá que el título sea claro, vendible y coherente con el flyer.'],
      ['label' => 'Fecha y horario', 'status' => 'revisar', 'note' => 'Validá día, mes, año, hora de inicio y hora de cierre antes de publicar.'],
      ['label' => 'Dirección', 'status' => 'revisar', 'note' => 'Revisá dirección, ciudad, provincia, piso o referencias de ingreso.'],
      ['label' => 'Acceso o precio', 'status' => 'revisar', 'note' => 'Confirmá precios, gratuidades, cupos o condiciones especiales.'],
      ['label' => 'Descripción', 'status' => 'revisar', 'note' => 'Leé la descripción completa y ajustá cualquier dato propio del organizador.'],
      ['label' => 'SEO y Google', 'status' => 'revisar', 'note' => 'Validá palabras clave y descripción corta para Google.'],
      ['label' => 'Imagen', 'status' => 'revisar', 'note' => 'Confirmá que la portada se vea clara y represente correctamente el evento.'],
    ];
  }

  private function runPayload(?EventAiAssistantRun $run): ?array
  {
    if (!$run) {
      return null;
    }

    return [
      'id' => $run->id,
      'type' => $run->type,
      'status' => $run->status,
      'model' => $run->model,
      'progress' => $this->progressPayload($run),
      'output_payload' => $run->output_payload,
      'error_message' => $run->error_message,
      'duration_ms' => $run->duration_ms,
      'created_at' => optional($run->created_at)->toIso8601String(),
      'updated_at' => optional($run->updated_at)->toIso8601String(),
    ];
  }

  private function progressPayload(EventAiAssistantRun $run): array
  {
    $stored = data_get($run->input_payload, 'progress', []);
    $status = $run->status;
    $type = $run->type;
    $elapsedSeconds = $run->duration_ms
      ? (int) ceil($run->duration_ms / 1000)
      : ($run->created_at ? max(0, now()->diffInSeconds($run->created_at)) : 0);
    $estimateSeconds = (int) config("openai.event_assistant.progress.{$type}_estimate_seconds", 90);
    $delayedAfter = (int) config('openai.event_assistant.progress.delayed_after_seconds', 120);
    $defaultStage = $status === 'pending'
      ? 'Esperando turno de procesamiento'
      : ($status === 'completed' ? 'Completado' : ($status === 'failed' ? 'No se pudo completar' : 'Procesando'));
    $defaultMessage = $status === 'pending'
      ? 'El proceso quedó en cola y empezará en unos segundos.'
      : 'El proceso sigue activo. Normalmente tarda entre 20 segundos y 2 minutos.';
    $percent = data_get($stored, 'percent');

    if ($status === 'completed') {
      $percent = 100;
    }

    return [
      'title' => $type === 'content' ? 'Generando copy y SEO' : 'Analizando flyer',
      'stage' => data_get($stored, 'stage', $defaultStage),
      'message' => data_get($stored, 'message', $defaultMessage),
      'percent' => is_numeric($percent) ? (int) $percent : null,
      'is_estimated' => (bool) data_get($stored, 'is_estimated', true),
      'is_indeterminate' => !is_numeric($percent),
      'elapsed_seconds' => $elapsedSeconds,
      'estimate_seconds' => $estimateSeconds,
      'delayed' => in_array($status, ['pending', 'running'], true) && $elapsedSeconds >= $delayedAfter,
      'support_id' => 'AI-' . $run->id,
      'updated_at' => data_get($stored, 'updated_at', optional($run->updated_at)->toIso8601String()),
    ];
  }

  private function validateAnalysisImage(string $imagePath): ?array
  {
    $allowedMimes = config('openai.reference.allowed_mimes', ['image/png', 'image/jpeg', 'image/webp']);
    $mime = mime_content_type($imagePath) ?: '';
    if (!in_array($mime, $allowedMimes, true)) {
      return [
        'error' => 'unsupported_image_type',
        'message' => 'La imagen debe ser JPG, PNG o WebP.',
      ];
    }

    $maxKb = (int) config('openai.reference.max_size_kb', 10240);
    if ($maxKb > 0 && filesize($imagePath) > $maxKb * 1024) {
      return [
        'error' => 'image_too_large',
        'message' => 'La imagen supera el tamaño máximo permitido para análisis IA.',
      ];
    }

    $size = getimagesize($imagePath);
    $minDimension = (int) config('openai.reference.min_dimension', 512);
    if (!$size || min((int) $size[0], (int) $size[1]) < $minDimension) {
      return [
        'error' => 'image_too_small',
        'message' => 'La imagen es demasiado chica para analizarla con confianza.',
      ];
    }

    return null;
  }

  private function defaultContent(Event $event): ?EventContent
  {
    $defaultLanguageId = Language::where('is_default', 1)->value('id');

    $query = EventContent::where('event_id', $event->id);
    if ($defaultLanguageId) {
      $query->orderByRaw('language_id = ? desc', [$defaultLanguageId]);
    }

    return $query->orderBy('id')->first();
  }

  private function htmlDescription(array $payload): string
  {
    $content = $payload['content'] ?? [];
    $parts = [];

    foreach (['short_description', 'main_description'] as $key) {
      if (!empty($content[$key])) {
        $parts[] = '<p>' . nl2br(e($content[$key])) . '</p>';
      }
    }

    if (!empty($content['what_you_will_experience']) && is_array($content['what_you_will_experience'])) {
      $parts[] = '<h3>Qué vas a vivir</h3><ul>' . $this->htmlList($content['what_you_will_experience']) . '</ul>';
    }

    if (!empty($content['important_information']) && is_array($content['important_information'])) {
      $parts[] = '<h3>Información importante</h3><ul>' . $this->htmlList($content['important_information']) . '</ul>';
    }

    if (!empty($payload['seo']['ai_search_summary'])) {
      $parts[] = '<h3>Resumen para buscadores e IA</h3><p>' . nl2br(e($payload['seo']['ai_search_summary'])) . '</p>';
    }

    if (!empty($payload['faq']) && is_array($payload['faq'])) {
      $faq = [];
      foreach ($payload['faq'] as $item) {
        $question = trim((string) ($item['question'] ?? ''));
        $answer = trim((string) ($item['answer'] ?? ''));
        if ($question !== '' && $answer !== '') {
          $faq[] = '<h4>' . e($question) . '</h4><p>' . nl2br(e($answer)) . '</p>';
        }
      }
      if (!empty($faq)) {
        $parts[] = '<h3>Preguntas frecuentes</h3>' . implode("\n", $faq);
      }
    }

    if (!empty($content['cta'])) {
      $parts[] = '<p><strong>' . e($content['cta']) . '</strong></p>';
    }

    return implode("\n", $parts);
  }

  private function htmlList(array $items): string
  {
    return implode('', array_map(fn($item) => '<li>' . e((string) $item) . '</li>', array_filter($items)));
  }
}
