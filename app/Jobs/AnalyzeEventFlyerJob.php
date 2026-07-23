<?php

namespace App\Jobs;

use App\Exceptions\OpenAiNonRetryableException;
use App\Models\Event\EventAiAssistantReview;
use App\Models\Event\EventAiAssistantRun;
use App\Services\EventAi\EventFactsBuilder;
use App\Services\OpenAI\EventAiAssistantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class AnalyzeEventFlyerJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public int $tries = 2;
  public int $backoff = 90;

  public function __construct(private int $runId)
  {
    $this->onQueue(config('openai.event_assistant.queue', 'ai-content'));
  }

  public function handle(EventAiAssistantService $assistant, EventFactsBuilder $factsBuilder): void
  {
    $run = EventAiAssistantRun::with('event')->findOrFail($this->runId);
    $startedAt = microtime(true);

    try {
      $run->markRunning();

      $event = $run->event;
      $imagePath = public_path('assets/admin/img/event/thumbnail/' . $event->thumbnail);
      if (empty($event->thumbnail) || !is_file($imagePath)) {
        throw new OpenAiNonRetryableException('La imagen de portada del evento no está disponible.');
      }

      $formFacts = $factsBuilder->fromEvent($event);
      $moderation = $assistant->moderateImageAndText($imagePath, implode("\n", array_filter([
        $formFacts['title'] ?? null,
        $formFacts['description'] ?? null,
        $formFacts['address'] ?? null,
      ])));

      $run->update([
        'source_image_path' => 'assets/admin/img/event/thumbnail/' . $event->thumbnail,
        'source_image_hash' => hash_file('sha256', $imagePath),
        'input_payload' => ['form_facts' => $formFacts],
        'moderation_payload' => $moderation,
      ]);

      if ((bool) data_get($moderation, 'results.0.flagged', false)) {
        throw new OpenAiNonRetryableException('La imagen o el texto necesitan revisión antes de usar el asistente IA.');
      }

      $analysis = $assistant->analyzeFlyer($imagePath, $formFacts);
      $canonicalFacts = $factsBuilder->canonicalFromAnalysis($event, $analysis);

      $run->markCompleted($analysis, (int) ((microtime(true) - $startedAt) * 1000));

      EventAiAssistantReview::create([
        'run_id' => $run->id,
        'event_id' => $run->event_id,
        'organizer_id' => $run->organizer_id,
        'canonical_event_facts' => $canonicalFacts,
        'accepted_fields' => [],
        'ignored_fields' => [],
        'audience_payload' => [],
        'tone' => 'cercano_rioplatense',
        'intensity' => 'equilibrado',
        'status' => 'pending',
      ]);
    } catch (OpenAiNonRetryableException $e) {
      $run->markFailed($e->getMessage());
    } catch (Throwable $e) {
      $run->markFailed($e->getMessage());
      throw $e;
    }
  }
}
