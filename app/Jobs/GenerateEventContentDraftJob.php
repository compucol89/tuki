<?php

namespace App\Jobs;

use App\Exceptions\OpenAiNonRetryableException;
use App\Models\Event\EventAiContentDraft;
use App\Services\OpenAI\EventAiAssistantService;
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

      $preferences = [
        'tone' => $draft->review->tone,
        'intensity' => $draft->review->intensity,
        'audience' => $draft->review->audience_payload ?? [],
        'locale' => 'es-AR',
        'timezone' => config('app.timezone', 'America/Argentina/Buenos_Aires'),
      ];

      $generated = $assistant->generateContent($draft->review->canonical_event_facts ?? [], $preferences);
      $moderation = $assistant->moderateText(json_encode($generated, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '');
      $audit = $generated['audit'] ?? [];
      $moderationFlagged = (bool) data_get($moderation, 'results.0.flagged', false);
      $needsHumanReview = (bool) ($audit['needs_human_review'] ?? false) || $moderationFlagged;

      $draft->update([
        'status' => 'completed',
        'generated_payload' => $generated,
        'audit_payload' => array_merge($audit, ['moderation' => $moderation]),
        'audit_status' => $moderationFlagged ? 'moderation_review' : ($audit['status'] ?? ($needsHumanReview ? 'needs_human_review' : 'passed')),
        'needs_human_review' => $needsHumanReview,
      ]);

      $draft->run?->markCompleted($generated, (int) ((microtime(true) - $startedAt) * 1000), array_merge($audit, ['moderation' => $moderation]));
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
