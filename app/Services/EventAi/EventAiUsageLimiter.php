<?php

namespace App\Services\EventAi;

use App\Models\Event\EventAiAssistantRun;

class EventAiUsageLimiter
{
  public function check(int $eventId, int $organizerId): array
  {
    return $this->checkType(
      $eventId,
      $organizerId,
      'analysis',
      (int) config('openai.event_assistant.limits.max_runs_per_event', 2),
      (int) config('openai.event_assistant.limits.max_runs_per_organizer_day', 10)
    );
  }

  public function checkContent(int $eventId, int $organizerId): array
  {
    return $this->checkType(
      $eventId,
      $organizerId,
      'content',
      (int) config('openai.event_assistant.limits.max_content_drafts_per_event', 2),
      (int) config('openai.event_assistant.limits.max_content_drafts_per_organizer_day', 10)
    );
  }

  private function checkType(int $eventId, int $organizerId, string $type, int $maxEventRuns, int $maxDailyRuns): array
  {
    $maxEventRuns = max($maxEventRuns, 0);
    $maxDailyRuns = max($maxDailyRuns, 0);

    $eventRuns = $this->billableRunQuery()
      ->where('event_id', $eventId)
      ->where('organizer_id', $organizerId)
      ->where('type', $type)
      ->count();

    $dailyRuns = $this->billableRunQuery()
      ->where('organizer_id', $organizerId)
      ->where('type', $type)
      ->where('created_at', '>=', now()->startOfDay())
      ->count();

    $remainingEventRuns = max($maxEventRuns - $eventRuns, 0);
    $remainingDailyRuns = max($maxDailyRuns - $dailyRuns, 0);

    if ($maxEventRuns > 0 && $eventRuns >= $maxEventRuns) {
      return $this->blocked($type, 'event_limit_reached', $maxEventRuns, $eventRuns, $remainingEventRuns, $maxDailyRuns, $dailyRuns, $remainingDailyRuns);
    }

    if ($maxDailyRuns > 0 && $dailyRuns >= $maxDailyRuns) {
      return $this->blocked($type, 'organizer_daily_limit_reached', $maxEventRuns, $eventRuns, $remainingEventRuns, $maxDailyRuns, $dailyRuns, $remainingDailyRuns);
    }

    return [
      'allowed' => true,
      'reason' => null,
      'max_event_runs' => $maxEventRuns,
      'used_event_runs' => $eventRuns,
      'remaining_event_runs' => $remainingEventRuns,
      'max_daily_runs' => $maxDailyRuns,
      'used_daily_runs' => $dailyRuns,
      'remaining_daily_runs' => $remainingDailyRuns,
      'message' => null,
    ];
  }

  private function blocked(string $type, string $reason, int $maxEventRuns, int $eventRuns, int $remainingEventRuns, int $maxDailyRuns, int $dailyRuns, int $remainingDailyRuns): array
  {
    $label = $type === 'content' ? 'generaciones de copy IA' : 'análisis IA';

    return [
      'allowed' => false,
      'reason' => $reason,
      'max_event_runs' => $maxEventRuns,
      'used_event_runs' => $eventRuns,
      'remaining_event_runs' => $remainingEventRuns,
      'max_daily_runs' => $maxDailyRuns,
      'used_daily_runs' => $dailyRuns,
      'remaining_daily_runs' => $remainingDailyRuns,
      'message' => $reason === 'event_limit_reached'
        ? "Ya usaste el límite de {$label} para este evento."
        : "Ya usaste el límite diario de {$label} para tu cuenta.",
    ];
  }

  private function billableRunQuery()
  {
    return EventAiAssistantRun::query()
      ->where(function ($query) {
        $query->whereNull('input_payload->actor_guard')
          ->orWhere('input_payload->actor_guard', '!=', 'admin');
      });
  }
}
