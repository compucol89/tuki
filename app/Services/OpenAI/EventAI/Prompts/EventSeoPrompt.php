<?php

namespace App\Services\OpenAI\EventAI\Prompts;

/**
 * SEO/AEO V1 — metadatos de descubrimiento. Separado del copy persuasivo.
 */
class EventSeoPrompt
{
  public function instructions(): string
  {
    return trim(<<<'PROMPT'
Sos el SEO/AEO Specialist de TukiPass. Generás ÚNICAMENTE metadatos de descubrimiento (búsqueda, redes, agentes IA). No reescribís el copy del evento.

RECIBÍS:
- canonical_event_facts: única fuente de hechos.
- final event copy: el copy ya aprobado (no lo modifiques).

REGLAS:
- Específico del evento, nunca boilerplate de la plataforma.
- SEO title: NOMBRE DEL EVENTO + ATRIBUTO DIFERENCIAL REAL + CIUDAD/VENUE cuando aporte.
- Sin keyword stuffing, sin listas artificiales, sin cortar palabras.
- meta description 120–160 caracteres: evento + atractivo + fecha/contexto + llamada suave.
- ai_search_summary: resumen factual compacto para agentes IA (qué, cuándo, dónde, cómo reservar cuando exista). Nunca como sección pública del cuerpo.
- No inventes datos ausentes; no filtres notas internas.
- locale: es-AR. "entrada" nunca "ticket".

Devolvé EXCLUSIVAMENTE el JSON del schema solicitado.
PROMPT);
  }

  public function build(array $canonicalFacts, array $finalCopy, string $locale = 'es-AR'): string
  {
    $copyDigest = [
      'public_title' => data_get($finalCopy, 'content.public_title'),
      'short_description' => data_get($finalCopy, 'content.short_description'),
      'main_description' => data_get($finalCopy, 'content.main_description'),
    ];

    return trim("Hechos canónicos:\n"
      . json_encode($canonicalFacts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . "\n\nCopy final aprobado (solo referencia; no lo reescribas):\n"
      . json_encode($copyDigest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . "\n\nLocale: {$locale}. Generá los metadatos de descubrimiento. Devolvé solo el JSON del schema.");
  }
}
