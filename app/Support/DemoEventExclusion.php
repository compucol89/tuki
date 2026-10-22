<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Fuente única de verdad para eventos demo/prueba.
 *
 * Un evento es demo si:
 *  - está en la lista histórica de IDs,
 *  - está en la lista de slugs conocidos,
 *  - su slug termina en -demo, o
 *  - su slug termina en -prueba.
 */
final class DemoEventExclusion
{
  public const EVENT_IDS = [104, 103, 102, 101, 93, 92, 91];

  public const EVENT_SLUGS = [
    'the-conference-planners',
    'design-research-by-australia',
    'decoration-of-the-marriage',
    'motivation-for-online-business',
    'small-business-ideas',
    'grand-night-party',
    'sports-grand-opening',
    'auditoria-tipografica-citrus-125',
    'tour-de-bares-y-boliches-en-palermo-recorrido-viernes-demo',
    'tour-de-bares-y-boliches-en-palermo-recorrido-sabado-demo',
    'tour-de-bares-en-palermo-recorrido-sin-cargo-demo',
    'rooftop-networking-night-conexiones-musica-y-cocteleria-en-palermo-demo',
    'art-design-weekend-exhibiciones-charlas-y-experiencias-creativas-demo',
    'gourmet-street-food-fest-sabores-musica-y-aire-libre-en-palermo-demo',
    'retiro-de-bienestar-yoga-meditacion-y-desconexion-frente-al-agua-demo',
    'noche-de-cachengue-perreo-beats-en-vivo-y-fiesta-hasta-tarde-demo',
    'fiesta-de-cachengue-noche-de-club-prueba',
  ];

  public static function isDemo(?int $eventId = null, ?string $slug = null): bool
  {
    if ($eventId !== null && in_array((int) $eventId, self::EVENT_IDS, true)) {
      return true;
    }

    $normalizedSlug = Str::slug((string) $slug);
    if ($normalizedSlug === '') {
      return false;
    }

    return in_array($normalizedSlug, self::EVENT_SLUGS, true)
      || Str::endsWith($normalizedSlug, '-demo')
      || Str::endsWith($normalizedSlug, '-prueba');
  }

  /**
   * Aplica la exclusión a una query que ya une events y event_contents.
   */
  public static function apply($query, string $idColumn = 'events.id', string $slugColumn = 'event_contents.slug')
  {
    return $query
      ->whereNotIn($idColumn, self::EVENT_IDS)
      ->whereNotIn($slugColumn, self::EVENT_SLUGS)
      ->where($slugColumn, 'not like', '%-demo')
      ->where($slugColumn, 'not like', '%-prueba');
  }

  public static function eventIdColumn(): string
  {
    return 'events.id';
  }

  public static function eventSlugColumn(): string
  {
    return 'event_contents.slug';
  }
}
