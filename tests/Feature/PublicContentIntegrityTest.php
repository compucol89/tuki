<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * F-001 / F-002 / F-010 — integridad de contenido público.
 * Fallo si una fuente de copy (lang o vista pública) contiene patrones de
 * contenido interno de desarrollo. No escanea la DB (provenance de datos
 * se cubre con PublicBusinessMetricsServiceTest y production-data-policy).
 */
class PublicContentIntegrityTest extends TestCase
{
  private const FORBIDDEN_PATTERNS = [
    'reemplazá',
    'referencia operativa',
    'referencia visual',
    'orientativa',
    'tendencia ilustrativa',
    'visualización orientativa',
    'valores placeholder',
    'lorem ipsum',
    'AltokeTicket',
    'example.com',
  ];

  /** Sólo como palabra completa: en español "MÉTODO"/"todos" contienen "todo". */
  private const FORBIDDEN_CASE_SENSITIVE_PATTERNS = [
    '/\bTODO\b/u',
    '/\bFIXME\b/u',
  ];

  /**
   * Exenciones por línea (código funcional, no copy visible):
   * - '$placeholderPatterns': detección de descripciones lorem/placeholder para
   *   meta description en event-details.blade.php (lógica, no texto al cliente).
   */
  private function isExemptLine(string $line, string $pattern): bool
  {
    if ($pattern === 'lorem ipsum' && str_contains($line, '$placeholderPatterns')) {
      return true;
    }

    return false;
  }

  private const LANG_FILES = [
    'lang/es.json',
    'resources/lang/es.json',
  ];

  public function test_lang_files_do_not_contain_internal_development_copy(): void
  {
    foreach (self::LANG_FILES as $file) {
      $this->assertTrue(File::exists(base_path($file)), "Falta archivo de idioma: {$file}");

      $content = (string) File::get(base_path($file));

      foreach (self::FORBIDDEN_PATTERNS as $pattern) {
        $this->assertStringNotContainsStringIgnoringCase(
          $pattern,
          $content,
          "Patrón interno '{$pattern}' encontrado en {$file}"
        );
      }

      foreach (self::FORBIDDEN_CASE_SENSITIVE_PATTERNS as $pattern) {
        $this->assertDoesNotMatchRegularExpression(
          $pattern,
          $content,
          "Patrón interno '{$pattern}' encontrado en {$file}"
        );
      }
    }
  }

  public function test_frontend_views_do_not_contain_internal_development_copy(): void
  {
    $views = File::allFiles(base_path('resources/views/frontend'));

    $this->assertNotEmpty($views);

    foreach ($views as $view) {
      $content = (string) File::get($view->getPathname());

      foreach (self::FORBIDDEN_PATTERNS as $pattern) {
        foreach (preg_split('/\R/', $content) as $line) {
          if ($this->isExemptLine($line, $pattern)) {
            continue;
          }

          $this->assertStringNotContainsStringIgnoringCase(
            $pattern,
            $line,
            "Patrón interno '{$pattern}' encontrado en {$view->getRelativePathname()}"
          );
        }
      }

      foreach (self::FORBIDDEN_CASE_SENSITIVE_PATTERNS as $pattern) {
        $this->assertDoesNotMatchRegularExpression(
          $pattern,
          $content,
          "Patrón interno '{$pattern}' encontrado en {$view->getRelativePathname()}"
        );
      }
    }
  }

  public function test_placeholder_metrics_config_no_longer_exists(): void
  {
    $this->assertFalse(
      File::exists(base_path('config/about_metrics.php')),
      'config/about_metrics.php (valores placeholder) debe eliminarse; las métricas vienen de PublicBusinessMetricsService'
    );
  }
}
