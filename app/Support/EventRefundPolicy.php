<?php

namespace App\Support;

use Illuminate\Support\Str;

final class EventRefundPolicy
{
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
    $text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

    if ($text === '' || mb_strlen($text) < 20) {
      return false;
    }

    return !Str::contains(Str::lower($text), self::DEMO_PATTERNS);
  }

  public static function defaultSummaryHtml(): string
  {
    $policyUrl = url('/politica-de-reembolsos');

    return 'Las devoluciones, cancelaciones y arrepentimiento de este evento se rigen por la '
      . '<a href="' . e($policyUrl) . '">política general de reembolsos de Tukipass</a>'
      . ' y por las condiciones que defina el organizador del evento. '
      . 'Tukipass actúa como plataforma tecnológica; la realización del evento y las reglas comerciales específicas son responsabilidad del organizador. '
      . 'Para reclamos escribí a <a href="mailto:soporte@tukipass.com">soporte@tukipass.com</a>.';
  }
}
