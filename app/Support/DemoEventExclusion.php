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
 *
 * Los eventos demo se dividen en dos grupos:
 *  - SHOWCASE_SLUGS: demos de vidriera intencional. Se muestran en la web
 *    pública, pero DemoEventMode sigue bloqueando checkout/pago.
 *  - El resto: demos históricos o de prueba; se ocultan de la web pública.
 *
 * Visibilidad != comprabilidad. No usar isDemo() para ocultar en la web;
 * usar isHidden()/applyVisibility(). isDemo() sigue siendo la verdad para
 * bloquear una compra.
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

  /**
   * Demos de vidriera: visibles en la web pública, no comprables.
   */
  public const SHOWCASE_SLUGS = [
    'rooftop-networking-night-conexiones-musica-y-cocteleria-en-palermo-demo',
    'art-design-weekend-exhibiciones-charlas-y-experiencias-creativas-demo',
    'gourmet-street-food-fest-sabores-musica-y-aire-libre-en-palermo-demo',
    'retiro-de-bienestar-yoga-meditacion-y-desconexion-frente-al-agua-demo',
    'fiesta-de-cachengue-noche-de-club-prueba',
    'noche-de-cachengue-perreo-beats-en-vivo-y-fiesta-hasta-tarde-demo',
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
   * Un demo se oculta de la web pública solo si no es de vidriera.
   */
  public static function isHidden(?int $eventId = null, ?string $slug = null): bool
  {
    if (!self::isDemo($eventId, $slug)) {
      return false;
    }

    return !in_array(Str::slug((string) $slug), self::SHOWCASE_SLUGS, true);
  }

  /**
   * Exclusión estricta para sitemap/AI: oculta todos los demos.
   */
  public static function apply($query, string $idColumn = 'events.id', string $slugColumn = 'event_contents.slug')
  {
    return $query
      ->whereNotIn($idColumn, self::EVENT_IDS)
      ->whereNotIn($slugColumn, self::EVENT_SLUGS)
      ->where($slugColumn, 'not like', '%-demo')
      ->where($slugColumn, 'not like', '%-prueba');
  }

  /**
   * Exclusión para la web pública: deja visibles los demos de vidriera,
   * pero oculta los históricos y cualquier demo no listado.
   */
  public static function applyVisibility($query, string $idColumn = 'events.id', string $slugColumn = 'event_contents.slug')
  {
    $hiddenSlugs = array_values(array_diff(self::EVENT_SLUGS, self::SHOWCASE_SLUGS));

    return $query
      ->whereNotIn($idColumn, self::EVENT_IDS)
      ->whereNotIn($slugColumn, $hiddenSlugs)
      ->where(function ($q) use ($slugColumn) {
        $q->where(function ($q2) use ($slugColumn) {
          $q2->where($slugColumn, 'not like', '%-demo')
            ->where($slugColumn, 'not like', '%-prueba');
        })->orWhereIn($slugColumn, self::SHOWCASE_SLUGS);
      });
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
