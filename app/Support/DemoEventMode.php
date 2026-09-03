<?php

namespace App\Support;

use Illuminate\Support\Str;

final class DemoEventMode
{
  public const EVENT_SLUGS = [
    'auditoria-tipografica-citrus-125',
    'tour-de-bares-y-boliches-en-palermo-recorrido-viernes-demo',
    'tour-de-bares-y-boliches-en-palermo-recorrido-sabado-demo',
    'tour-de-bares-en-palermo-recorrido-sin-cargo-demo',
    'rooftop-networking-night-conexiones-musica-y-cocteleria-en-palermo-demo',
    'art-design-weekend-exhibiciones-charlas-y-experiencias-creativas-demo',
    'gourmet-street-food-fest-sabores-musica-y-aire-libre-en-palermo-demo',
    'retiro-de-bienestar-yoga-meditacion-y-desconexion-frente-al-agua-demo',
    'noche-de-cachengue-perreo-beats-en-vivo-y-fiesta-hasta-tarde-demo',
  ];

  public static function isDemo(?int $eventId = null, ?string $slug = null): bool
  {
    unset($eventId);

    $normalizedSlug = Str::slug((string) $slug);

    return in_array($normalizedSlug, self::EVENT_SLUGS, true)
      || Str::endsWith($normalizedSlug, '-demo');
  }
}
