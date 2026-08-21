<?php

namespace App\Services\OpenAI\EventAI\Quality;

use App\Services\EventAi\BrandVoice;
use App\Services\EventAi\CanonicalEventFacts;

/**
 * Gate de GENERICNESS. Detecta clichés literales, densidad de adjetivos genéricos
 * y repetición de frases. Devuelve un score 0-10 (0 = muy específico) + señales.
 */
class EventContentGenericnessGate
{
  private const GENERIC_ADJECTIVES = [
    'increíble', 'increible', 'inolvidable', 'única', 'unica', 'único', 'unico',
    'épico', 'epico', 'espectacular', 'mágico', 'magico', 'imperdible',
    'especial', 'inmejorable', 'sorprendente', 'extraordinario', 'fabuloso',
    'asombroso', 'genial', 'maravilloso', 'perfecto',
  ];

  private const GENERIC_PHRASES = [
    'música, energía y diversión', 'musica, energia y diversion',
    'una noche para recordar', 'una noche que no vas a olvidar',
    'viví una experiencia única', 'vivi una experiencia unica',
    'el mejor plan para', 'el plan perfecto para',
    'diversión asegurada', 'diversion asegurada',
  ];

  public function score(array $payload, ?CanonicalEventFacts $facts = null): array
  {
    $texts = app(EventContentQualityGate::class)->publicTexts($payload);
    $allText = mb_strtolower(implode("\n", $texts));
    $signals = [];

    foreach (BrandVoice::BANNED_CLICHES as $cliche) {
      if (str_contains($allText, $cliche)) {
        $signals[] = "cliché: {$cliche}";
      }
    }
    foreach (self::GENERIC_PHRASES as $phrase) {
      if (str_contains($allText, $phrase)) {
        $signals[] = "frase genérica: {$phrase}";
      }
    }

    $words = preg_split('/[\s,.;:!?()\-"«»]+/u', $allText, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $totalWords = count($words);
    $genericCount = 0;
    foreach ($words as $word) {
      if (in_array($word, self::GENERIC_ADJECTIVES, true)) {
        $genericCount++;
      }
    }

    $adjectiveDensity = $totalWords > 0 ? ($genericCount / $totalWords) * 100 : 0;

    $factDensity = $this->factDensity($facts, $totalWords);

    $score = min(
      10,
      count($signals) * 1.5
        + min($adjectiveDensity * 4, 4)
        + max(0, 2.5 - $factDensity * 2.5),
    );

    return [
      'score' => round($score, 1),
      'signals' => $signals,
      'generic_adjective_density_percent' => round($adjectiveDensity, 2),
      'fact_anchors' => $this->factAnchorCount($facts, $allText),
    ];
  }

  private function factDensity(?CanonicalEventFacts $facts, int $totalWords): float
  {
    if (!$facts || $totalWords === 0) {
      return 0;
    }

    return min(1.0, count($facts->all()) / max(1, $totalWords / 40));
  }

  private function factAnchorCount(?CanonicalEventFacts $facts, string $allText): int
  {
    if (!$facts) {
      return 0;
    }

    $anchors = 0;
    foreach ($facts->all() as $fact) {
      if (!is_string($fact['value']) && !is_numeric($fact['value'])) {
        continue;
      }
      $value = mb_strtolower(trim((string) $fact['value']));
      if (mb_strlen($value) >= 4 && str_contains($allText, $value)) {
        $anchors++;
      }
    }

    return $anchors;
  }
}
