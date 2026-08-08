<?php

namespace App\Support;

use App\Models\BasicSettings\Basic;
use App\Models\BillingSetting;
use Illuminate\Support\Str;

final class EventRefundPolicy
{
  /**
   * Datos de identidad corporativa desde su fuente de verdad (DB settings),
   * con fallback único centralizado en config/tukipass.php.
   */
  public static function issuerName(): string
  {
    $billing = BillingSetting::current();

    return trim((string) ($billing?->issuer_name ?? '')) ?: config('tukipass.fiscal.issuer_name', 'TAYRONA GROUP SAS');
  }

  public static function issuerCuit(): string
  {
    $billing = BillingSetting::current();

    return trim((string) ($billing?->issuer_cuit ?? '')) ?: config('tukipass.fiscal.issuer_cuit', '30-71885087-4');
  }

  public static function supportEmail(): string
  {
    $basic = Basic::query()->first();
    $email = trim((string) ($basic?->email_address ?? ''));

    return $email !== '' ? $email : (string) config('tukipass.fiscal.support_email');
  }

  public static function policyUrl(): string
  {
    return url('/politica-de-reembolsos');
  }

  /**
   * Texto único mostrado en admin, organizador, detalle de evento y base de datos.
   */
  public static function canonicalPlainText(): string
  {
    $policyUrl = self::policyUrl();
    $domain = parse_url($policyUrl, PHP_URL_HOST) ?: config('app.url');

    return 'Tukipass (' . self::issuerName() . ', CUIT ' . self::issuerCuit() . ') presta un servicio tecnológico de venta online de entradas. '
      . 'No organiza ni produce los eventos publicados: el organizador o productor es el único responsable de la producción, realización e información publicada del evento. '
      . 'Los reembolsos, cancelaciones, reprogramaciones y el derecho de arrepentimiento —cuando corresponda conforme la Ley 24.240 de Defensa del Consumidor y la normativa aplicable— '
      . 'se rigen por la política general de Tukipass (' . $domain . '/politica-de-reembolsos), por las condiciones del organizador y por la ley argentina. '
      . 'Reclamos y consultas: ' . self::supportEmail() . '.';
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
