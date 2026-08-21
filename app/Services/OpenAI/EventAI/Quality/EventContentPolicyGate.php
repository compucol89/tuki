<?php

namespace App\Services\OpenAI\EventAI\Quality;

use App\Services\EventAi\CanonicalEventFacts;
use App\Services\EventAi\EventAiDraftPostProcessor;

/**
 * Gate determinista de POLÍTICA. Reglas de seguridad/publicación que PHP puede probar:
 * escasez falsa, notas internas, datos volátiles, "gratis" cuando no es free.
 */
class EventContentPolicyGate
{
  private const FALSE_SCARCITY_PATTERN = '/(?:últimas entradas|ultimas entradas|se está agotando|se esta agotando|entradas volando|quedan pocos lugares|últimos lugares|va a explotar|sold ?out|más de \d+ personas)/iu';

  private const INTERNAL_LEAK_PATTERN = '/(?:revisar antes de publicar|pendiente de revisión|no confiamos en|auditoría|auditoria interna|checklist)/iu';

  public function failures(array $payload, ?CanonicalEventFacts $facts = null): array
  {
    $failures = [];
    $texts = $this->publicTexts($payload);
    $allText = mb_strtolower(implode("\n", $texts));

    if (preg_match(self::FALSE_SCARCITY_PATTERN, $allText)) {
      $factsText = $facts ? mb_strtolower(json_encode($facts->all(), JSON_UNESCAPED_UNICODE)) : '';
      $hasEvidence = $factsText !== '' && (str_contains($factsText, 'sold_out') || str_contains($factsText, 'sold out'));

      if (!$hasEvidence) {
        $failures[] = 'Falsa escasez sin evidencia canónica.';
      }
    }

    if (preg_match(self::INTERNAL_LEAK_PATTERN, $allText)) {
      $failures[] = 'Posible nota interna filtrada a texto público.';
    }

    $processor = app(EventAiDraftPostProcessor::class);
    foreach (($payload['faq'] ?? []) as $item) {
      $answer = trim((string) ($item['answer'] ?? ''));
      if ($answer !== '' && preg_match(EventAiDraftPostProcessor::BANNED_PATTERN, $answer)) {
        $failures[] = 'FAQ con notas internas o datos ausentes visibles.';
        break;
      }
    }
    if ($processor->containsInternalNote($payload)) {
      $failures[] = 'Nota interna detectada en el paquete.';
    }

    if ($facts) {
      if (!$this->eventIsFree($facts) && preg_match('/\bgratis\b|\bentrada libre\b|\bsin costo\b/iu', $allText)) {
        $failures[] = 'Se menciona "gratis" pero el evento no es gratuito según los hechos canónicos.';
      }

      foreach ($this->volatilePrices($facts) as $price) {
        if (str_contains($allText, (string) $price)) {
          $failures[] = "Precio volátil ({$price}) incrustado en copy con copy_safe=false.";
        }
      }
    }

    return $failures;
  }

  private function eventIsFree(CanonicalEventFacts $facts): bool
  {
    $fact = $facts->resolve('pricing_type');

    return $fact !== null && $fact['value'] === 'free';
  }

  private function volatilePrices(CanonicalEventFacts $facts): array
  {
    $prices = [];
    foreach (['price', 'price_min', 'price_max'] as $key) {
      $fact = $facts->resolve($key);
      if ($fact && $fact['sensitive'] && !$fact['copy_safe'] && is_numeric($fact['value'])) {
        $prices[] = (int) $fact['value'];
      }
    }

    return array_values(array_unique($prices));
  }

  private function publicTexts(array $payload): array
  {
    return app(EventContentQualityGate::class)->publicTexts($payload);
  }
}
