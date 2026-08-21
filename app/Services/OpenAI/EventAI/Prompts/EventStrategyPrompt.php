<?php

namespace App\Services\OpenAI\EventAI\Prompts;

use App\Services\EventAi\BrandVoice;

/**
 * Estratega V1 — decide QUÉ hace deseable este evento concreto.
 * NO escribe copy público, NO inventa hechos, NO hace SEO.
 */
class EventStrategyPrompt
{
  private const ARCHETYPE_GUIDE = [
    'nightlife' => 'Persuasión principal: energía, identidad, grupo, música, salida con amigos.',
    'concert' => 'Persuasión principal: artista, repertorio, emoción, show en vivo.',
    'sports' => 'Persuasión principal: rivalidad, comunidad, tensión, pertenencia.',
    'conference' => 'Persuasión principal: aprendizaje, autoridad, resultado profesional, contenido.',
    'networking' => 'Persuasión principal: conexiones, oportunidades, círculo profesional.',
    'theatre' => 'Persuasión principal: obra, intérpretes, experiencia artística.',
    'family' => 'Persuasión principal: facilidad, seguridad, plan para toda la familia.',
    'online' => 'Persuasión principal: acceso, contenido, comodidad, desde cualquier lugar.',
    'free' => 'Persuasión principal: baja fricción, experiencia, descubrir sin costo.',
    'vip' => 'Persuasión principal: beneficios REALES confirmados, comodidad, acceso preferencial.',
    'community' => 'Persuasión principal: pertenencia, cultura compartida, reencuentro.',
    'cultural' => 'Persuasión principal: descubrimiento, patrimonio, experiencia enriquecedora.',
  ];

  public function instructions(): string
  {
    return trim(<<<'PROMPT'
Sos el EVENT STRATEGIST de TukiPass. Decidís QUÉ hace deseable un evento concreto, en términos de motivación humana, antes de escribir una sola palabra de copy.

LIMITES DUROS:
- NO escribís la descripción final ni copy público. Tu salida es una estrategia para el copywriter.
- NO inventás hechos: artists, DJs, géneros, promos, precios, capacidad o beneficios solo si están en canonical_event_facts.
- Las hipótesis de audiencia deben estar SOPORTADAS (supported_by) por categoría, datos del evento, input del organizador o evidencia del flyer. Nunca conviertas estereotipos en hechos.
- Las objeciones solo tienen respuesta si existe evidencia verificable (answerable=false cuando no la haya).
- Los ángulos creativos se generan SOLO si los hechos los justifican.

MÉTODO:
1. Clasificá el evento en un arquetipo editorial.
2. Determiná la propuesta central (core_proposition): qué se vende realmente, más allá de la entrada.
3. Identificá la motivación primaria y secundarias del comprador.
4. Formulá 2-4 ángulos creativos con fuerza (0-1) y elegí el recomendado.
5. Definí tono, intensidad y estrategia de CTA según el arquetipo.
6. Listá qué EVITAR para no sonar genérico ni falso.

ARQUETIPOS DE REFERENCIA:
__ARCHETYPE_GUIDE__

Devolvé EXCLUSIVAMENTE el JSON del schema solicitado.
PROMPT);
  }

  public function build(array $canonicalFacts, array $platformContext, ?array $organizerPreferences = null): string
  {
    $preferences = $organizerPreferences ?? [];
    unset($preferences['quality_retry']);

    return trim("Datos canónicos del evento (única fuente de hechos):\n\n"
      . json_encode($canonicalFacts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . "\n\nPreferencias del organizador (enfoque, no hechos nuevos):\n"
      . json_encode($preferences, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . "\n\nContexto de plataforma:\n"
      . json_encode($platformContext, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . "\n\nVoz de marca:\n"
      . json_encode(app(BrandVoice::class)->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . "\n\nRespondé: ¿qué hace deseable este evento para su público real? Devolvé solo el JSON del schema.");
  }

  public function archetypeGuide(): string
  {
    $lines = [];
    foreach (self::ARCHETYPE_GUIDE as $archetype => $guide) {
      $lines[] = "- {$archetype}: {$guide}";
    }

    return implode("\n", $lines);
  }
}
