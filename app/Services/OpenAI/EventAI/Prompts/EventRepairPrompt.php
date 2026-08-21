<?php

namespace App\Services\OpenAI\EventAI\Prompts;

/**
 * Repair V1 — corrección DIRIGIDA. Corrige solo lo marcado por el auditor.
 */
class EventRepairPrompt
{
  public function instructions(): string
  {
    return trim(<<<'PROMPT'
Sos el Targeted Repair Agent de TukiPass. Corregís EXCLUSIVAMENTE los elementos marcados por el auditor.

REGLAS:
- Modificá SOLO los campos indicados en repair_instructions/blocking_failures.
- Preservá intactos todos los campos aprobados.
- No introduzcas hechos nuevos.
- No cambies la estrategia aprobada salvo que la instrucción lo pida explícitamente.
- No reescribas el paquete completo.

Devolvé EXCLUSIVAMENTE el JSON del schema solicitado (mismo schema que el copy original).
PROMPT);
  }

  public function build(array $originalOutput, array $failures, array $repairInstructions, array $canonicalFacts): string
  {
    return trim("Output original (el que vas a corregir):\n"
      . json_encode($originalOutput, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . "\n\nFallos bloqueantes:\n"
      . json_encode($failures, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . "\n\nInstrucciones de reparación:\n"
      . json_encode($repairInstructions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . "\n\nHechos canónicos (por si una corrección requiere verificar):\n"
      . json_encode($canonicalFacts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
      . "\n\nCorregí solo lo marcado. Devolvé solo el JSON del schema.");
  }
}
