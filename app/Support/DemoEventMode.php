<?php

namespace App\Support;

/**
 * Compatibilidad: la lógica vive en DemoEventExclusion.
 */
final class DemoEventMode
{
  public const EVENT_SLUGS = DemoEventExclusion::EVENT_SLUGS;

  public static function isDemo(?int $eventId = null, ?string $slug = null): bool
  {
    return DemoEventExclusion::isDemo($eventId, $slug);
  }
}
