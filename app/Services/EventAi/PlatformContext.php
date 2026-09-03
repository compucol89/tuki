<?php

namespace App\Services\EventAi;

use App\Models\BasicSettings\Basic;
use App\Support\EventRefundPolicy;

/**
 * Contexto estable de plataforma. Restricciones para la IA, no boilerplate.
 */
final class PlatformContext
{
  public const BRAND = 'TukiPass';
  public const MARKET = 'Argentina';
  public const ROLE = 'ticketing_platform';

  /**
   * Reglas que la IA DEBE respetar, pero NO imprimir en cada descripción
   * salvo que la UI ya lo presente de otra forma.
   */
  public const LEGAL_CONSTRAINTS = [
    'Tukipass no organiza ni produce los eventos; el organizador es responsable de realización, horarios, accesos y condiciones.',
    'No atribuir la organización del evento a TukiPass.',
    'No inventar políticas de reembolso ni prometer devoluciones.',
    'No prometer condiciones de acceso, seguridad ni beneficios no confirmados.',
  ];

  public function toArray(): array
  {
    return [
      'brand' => self::BRAND,
      'operator' => $this->operator(),
      'market' => self::MARKET,
      'role' => self::ROLE,
      'currency' => $this->currency(),
      'refund_policy_source' => $this->refundPolicy(),
      'legal_constraints' => self::LEGAL_CONSTRAINTS,
    ];
  }

  private function currency(): ?string
  {
    try {
      return optional(Basic::select('base_currency_text')->first())->base_currency_text;
    } catch (\Throwable $e) {
      return null;
    }
  }

  private function operator(): ?string
  {
    return config('tukipass.fiscal.issuer_name');
  }

  private function refundPolicy(): ?string
  {
    try {
      return EventRefundPolicy::canonicalPlainText();
    } catch (\Throwable $e) {
      return null;
    }
  }
}
