<?php

namespace App\Support;

use Illuminate\Support\Str;

final class EventRefundPolicy
{
  /**
   * Texto único mostrado en admin, organizador, detalle de evento y base de datos.
   */
  public static function canonicalPlainText(): string
  {
    return 'Tukipass (TAYRONA GROUP SAS, CUIT 30-71885087-4) presta un servicio tecnológico de venta online de entradas. '
      . 'No organiza ni produce los eventos publicados: el organizador o productor es el único responsable de la producción, realización e información publicada del evento. '
      . 'Los reembolsos, cancelaciones, reprogramaciones y el derecho de arrepentimiento —cuando corresponda conforme la Ley 24.240 de Defensa del Consumidor y la normativa aplicable— '
      . 'se rigen por la política general de Tukipass (www.tukipass.com/politica-de-reembolsos), por las condiciones del organizador y por la ley argentina. '
      . 'Reclamos y consultas: soporte@tukipass.com.';
  }

  public static function matchesCanonical(?string $value): bool
  {
    $text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    $canonical = trim(preg_replace('/\s+/u', ' ', self::canonicalPlainText()));

    return $text === $canonical;
  }

  public const DEMO_PATTERNS = [
    'lorem ipsum',
    'pseudo-latin text',
    'placeholder text',
    'asesoría legal',
    'asesoria legal',
    'revisado por asesoría',
    'publicación definitiva',
    'no refunds',
    'all sales are final',
    'non-refundable',
    'non refundable',
    'tickets are final',
    'no refund will be',
    'demo policy',
    'sample refund',
    'example refund',
  ];

  public static function isValid(?string $value): bool
  {
    if (self::matchesCanonical($value)) {
      return true;
    }

    $text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

    if ($text === '' || mb_strlen($text) < 20) {
      return false;
    }

    return !Str::contains(Str::lower($text), self::DEMO_PATTERNS);
  }

  public static function defaultSummaryHtml(): string
  {
    $policyUrl = url('/politica-de-reembolsos');

    return e(self::canonicalPlainText())
      . ' <a href="' . e($policyUrl) . '">' . e(__('Ver política completa')) . '</a>.';
  }

  public static function displayPlainText(?string $stored): string
  {
    return self::matchesCanonical($stored) || !self::isValid($stored)
      ? self::canonicalPlainText()
      : trim((string) $stored);
  }
}
