<?php

namespace App\Support;

final class LegalPageFooter
{
  public const DEMO_DISCLAIMER_HTML = '<p><strong>Este documento debe ser revisado por asesoría legal antes de su publicación definitiva.</strong></p>';

  public static function publishedFooterHtml(): string
  {
    return <<<'HTML'
<p><strong>Importante:</strong> Tukipass no organiza ni produce los eventos publicados, salvo indicación expresa. Tukipass presta un servicio tecnológico de publicación, gestión y venta online de entradas. La realización, calidad, accesos, horarios, cambios, cancelaciones, reembolsos y condiciones particulares del evento son responsabilidad exclusiva del organizador.</p>
<p>Al utilizar este sitio o comprar una entrada, aceptás los Términos y Condiciones de Tukipass y las políticas aplicables de cada evento.</p>
HTML;
  }

  public static function stripDemoDisclaimer(string $content): string
  {
    $normalized = str_replace(
      [self::DEMO_DISCLAIMER_HTML, strip_tags(self::DEMO_DISCLAIMER_HTML, '<strong>')],
      '',
      $content
    );

    if (stripos($normalized, 'asesoría legal') !== false || stripos($normalized, 'asesoria legal') !== false) {
      $normalized = preg_replace(
        '/<p>\s*<strong>\s*Este documento debe ser revisado por asesor[ií]a legal[^<]*<\/strong>\s*<\/p>\s*/iu',
        '',
        $normalized
      ) ?? $normalized;
    }

    return trim($normalized);
  }
}
