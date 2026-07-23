<?php

namespace App\Support;

final class EventAiDraftPreferences
{
  public static function fromReview(object $review): array
  {
    $audience = is_array($review->audience_payload ?? null) ? $review->audience_payload : [];
    $eventBrief = trim((string) ($audience['event_brief'] ?? $audience['description'] ?? ''));

    return [
      'tone' => $review->tone,
      'intensity' => $review->intensity,
      'event_brief' => $eventBrief,
      'audience' => $audience,
      'locale' => 'es-AR',
      'timezone' => config('app.timezone', 'America/Argentina/Buenos_Aires'),
    ];
  }
}
