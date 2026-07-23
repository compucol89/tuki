<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Jobs\AnalyzeEventFlyerJob;
use App\Jobs\GenerateEventContentDraftJob;
use App\Models\Event;
use App\Models\Event\EventAiAssistantReview;
use App\Models\Event\EventAiAssistantRun;
use App\Models\Event\EventAiContentDraft;
use App\Models\Event\EventContent;
use App\Models\Language;
use App\Services\EventAi\EventAiUsageLimiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mews\Purifier\Facades\Purifier;

class EventAiAssistantController extends Controller
{
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

    $result = DB::transaction(function () use ($event, $imagePath, $limiter) {
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

      $usage = $limiter->check($event->id, $event->organizer_id);
      if (!$usage['allowed']) {
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
        'prompt_version' => config('openai.event_assistant.prompt_version', '2026-07-23-v1'),
        'source_image_path' => 'assets/admin/img/event/thumbnail/' . $event->thumbnail,
        'source_image_hash' => hash_file('sha256', $imagePath),
      ]);

      return [
        'run' => $run,
        'usage' => $limiter->check($event->id, $event->organizer_id),
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

    $draft = EventAiContentDraft::where('event_id', $event->id)
      ->where('organizer_id', $event->organizer_id)
      ->latest()
      ->first();

    return response()->json([
      'enabled' => (bool) config('features.event_ai_assistant_enabled', false),
      'usage' => $limiter->check($event->id, $event->organizer_id),
      'content_usage' => $limiter->checkContent($event->id, $event->organizer_id),
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

    $result = DB::transaction(function () use ($event, $review, $data, $limiter) {
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

      $usage = $limiter->checkContent($event->id, $event->organizer_id);
      if (!$usage['allowed']) {
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
        'prompt_version' => config('openai.event_assistant.prompt_version', '2026-07-23-v1'),
        'input_payload' => [
          'review_id' => $review->id,
          'tone' => $data['tone'],
          'intensity' => $data['intensity'],
          'audience' => $data['audience'] ?? [],
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
    $organizer = Auth::guard('organizer')->user();

    return $organizer && (int) $event->organizer_id === (int) $organizer->id;
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
      'output_payload' => $run->output_payload,
      'error_message' => $run->error_message,
      'duration_ms' => $run->duration_ms,
      'created_at' => optional($run->created_at)->toIso8601String(),
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
