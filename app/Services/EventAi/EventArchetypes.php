<?php

namespace App\Services\EventAi;

/**
 * Arquetipos editoriales + few-shot contextual por arquetipo.
 * Se elige determinísticamente desde la categoría/título; el modelo puede refinar.
 */
class EventArchetypes
{
  public const FEW_SHOT = [
    'nightlife' => [
      'name' => 'Fiesta / discoteca',
      'example' => <<<'TXT'
Título: "Sábado de cumbia y reggaetón en Palermo"
Descripción: "Este sábado, Palermo se prende fuego con la mezcla que más bailás: cumbia, reggaetón y los clásicos que no pueden faltar. Desde la primera hora, pista llena, DJ en vivo y el mejor ambiente para llegar con tu grupo. Entrada anticipada desde $8.000. Cupos limitados por capacidad del lugar."
TXT,
    ],
    'concert' => [
      'name' => 'Concierto / música en vivo',
      'example' => <<<'TXT'
Título: "Banda del Sur en vivo en Teatro Vorterix"
Descripción: "Banda del Sur vuelve a los escenarios porteños con su repertorio completo y una noche pensada para cantar de principio a fin. Apertura de puertas 20:00, show 21:00. Entradas anticipadas disponibles."
TXT,
    ],
    'online' => [
      'name' => 'Evento online',
      'example' => <<<'TXT'
Título: "Masterclass online de marketing digital"
Descripción: "Aprendé paso a paso cómo planificar campañas que venden, desde tu casa y en vivo. Dos horas con material descargable y espacio para preguntas. Recibís el enlace de acceso al confirmar tu reserva."
TXT,
    ],
    'family' => [
      'name' => 'Evento familiar',
      'example' => <<<'TXT'
Título: "Tarde de circo para toda la familia"
Descripción: "Un plan ideal para el fin de semana: show de circo para grandes y chicos, con funciones cortas, zona de juegos y entrada libre para menores de 3 años. Llegá temprano y disfrutá la tarde completa."
TXT,
    ],
    'conference' => [
      'name' => 'Conferencia / negocios',
      'example' => <<<'TXT'
Título: "Summit de negocios digitales 2026"
Descripción: "Una jornada para conectar con referentes del sector, aprender de casos reales y llevarte contactos que valen el viaje. Agenda completa de charlas, espacio de networking y coffee breaks."
TXT,
    ],
    'theatre' => [
      'name' => 'Teatro / show',
      'example' => <<<'TXT'
Título: "Comedia en el Multiteatro"
Descripción: "Una comedia que no te suelta desde la primera escena, con un elenco que la rompe y risas garantizadas. Funciones viernes y sábado. Entradas anticipadas con ubicación asignada."
TXT,
    ],
    'sports' => [
      'name' => 'Deporte',
      'example' => <<<'TXT'
Título: "Clásico de fútbol en el estadio"
Descripción: "El clásico se vive en la cancha. Llegá temprano, cantá con tu hinchada y guardate la tarde que todos van a recordar. Entradas por sector, acceso anticipado recomendado."
TXT,
    ],
    'community' => [
      'name' => 'Comunidad / cultura',
      'example' => <<<'TXT'
Título: "Festival gastronómico latinoamericano"
Descripción: "Un domingo a puro sabor: más de 20 puestos de cocina latinoamericana, música en vivo y un patio para compartir con amigos y familia. Entrada libre y gratuita."
TXT,
    ],
  ];

  /**
   * Heurística determinista: elige un arquetipo editorial desde datos reales.
   * El modelo puede refinar el arquetipo, pero nunca inventar hechos para rellenarlo.
   */
  public function guess(string $category, string $title, string $eventType): string
  {
    $haystack = mb_strtolower($category . ' ' . $title);

    if ($eventType === 'online') {
      return 'online';
    }

    foreach (self::FEW_SHOT as $archetype => $meta) {
      if ($archetype === 'online') {
        continue;
      }
      $hay = mb_strtolower($meta['name']);
      if (str_contains($haystack, $hay) || $this->keywords($archetype, $haystack)) {
        return $archetype;
      }
    }

    return 'nightlife';
  }

  public function example(string $archetype): ?string
  {
    return self::FEW_SHOT[$archetype]['example'] ?? null;
  }

  private function keywords(string $archetype, string $haystack): bool
  {
    $map = [
      'concert' => ['concierto', 'banda', 'show en vivo', 'en vivo', 'recital', 'música en vivo', 'musica en vivo'],
      'sports' => ['fútbol', 'futbol', 'partido', 'deporte', 'torneo', 'copa'],
      'conference' => ['conferencia', 'summit', 'charla', 'workshop', 'congreso', 'negocios', 'networking'],
      'theatre' => ['teatro', 'obra', 'comedia', 'drama', 'stand up', 'standup'],
      'family' => ['familiar', 'familia', 'niños', 'ninos', 'chicos', 'circo', 'infantil'],
      'community' => ['festival', 'gastronom', 'feria', 'cultural', 'comunidad'],
      'nightlife' => ['fiesta', 'discoteca', 'boliche', 'noche', 'dj', 'party', 'rumba', 'reggaetón', 'reggaeton'],
    ];

    foreach (($map[$archetype] ?? []) as $keyword) {
      if (str_contains($haystack, $keyword)) {
        return true;
      }
    }

    return false;
  }
}
