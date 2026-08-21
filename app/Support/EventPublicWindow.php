<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Política única de publicación de eventos en el frontend público
 * (usada por EventController@index y por los tests — F-006).
 *
 * Un evento es público si:
 *  - events.status = 1
 *  - no pertenece a DemoEventExclusion::EVENT_IDS
 *  - end_date_time >= now (ventana de venta vigente)
 */
final class EventPublicWindow
{
  public static function apply($query, CarbonInterface $now): mixed
  {
    return $query
      ->where('events.status', 1)
      ->whereNotIn('events.id', DemoEventExclusion::EVENT_IDS)
      ->where('events.end_date_time', '>=', $now);
  }
}
