<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Guardrail "Zero Hardcoded Data".
 *
 * Detecta reintroducciones de datos de negocio/entorno/productivos escritos
 * como literales en zonas productivas (app/, resources/, routes/, config/,
 * database/). NO bloquea literales estructurales (routing, booleans, UI copy
 * con @lang, placeholders, fixtures de tests, librerías third-party).
 *
 * USO:
 *   php artisan audit:hardcoded            # reporta hallazgos (exit 0 si hay)
 *   php artisan audit:hardcoded --fail     # exit 1 si hay hallazgos (CI)
 */
class AuditHardcodedData extends Command
{
  protected $signature = 'audit:hardcoded {--fail : exit 1 si se detectan hallazgos}';

  protected $description = 'Escanea reintroducciones de datos hardcodeados (Zero Hardcoded Data policy)';

  /** Patrones de alto riesgo: URLs productivas, emails, moneda, fiscales, dominios. */
  private const SCAN_PATTERNS = [
    'url_productiva' => '/https?:\/\/www\.tukipass\.com/',
    'dominio_propio' => '/[\'"`]https?:\/\/(tukipass\.com|www\.tukipass\.com)/',
    'email_soporte' => '/[\'"`](soporte|hola|info|facturacion|support)@tukipass\.com/',
    'cuit_corporativo' => '/30-71885087-4|30718850874/',
    'razon_social' => '/TAYRONA GROUP SAS/',
    'moneda_fija' => '/[\'"`](?:priceCurrency|currency|currency_id)\s*[=:>]\s*[\'"`]?(ARS|USD)/',
    'direccion_fija' => '/Honduras 5535|Pueyrredón 1357/',
    'dominio_host' => '/getHost\(\)\s*!==\s*[\'"`][a-z0-9.-]+\.(com|net|org)/',
  ];

  /** Directorios a excluir (third-party, generados, fixture). */
  private const EXCLUDE_DIRS = [
    'app/Libraries/I18N',
    'vendor',
    'node_modules',
    'storage',
    'public/assets/admin',
    'public/assets/front/js/vendor',
    'database/factories',
    'tests',
    'docs',
  ];

  private const ALLOWED_PATTERNS = [
    'config/tukipass.php',              // fuente única centralizada de fallbacks
    'config/cors.php',                  // dominios CORS con override env (env() wrappers)
    'config/arca.php',                  // URLs AFIP públicas + parámetros ARCA (env-driven)
    'app/Console/Commands/AuditHardcodedData.php', // auto-referencia (define los patrones)
    'app/Console/Commands/SeedColombiaWorldCupEvents.php', // TEST FIXTURE: seed demo manual explícito
    'database/migrations',              // snapshots históricos (no re-editar)
    'database/seeders/LegalPagesContentSeeder.php', // lee config; se mantiene como referencia
  ];

  public function handle(): int
  {
    $findings = [];
    $scannedFiles = 0;

    $files = $this->collectFiles();

    foreach ($files as $file) {
      if ($this->isAllowed($file)) {
        continue;
      }

      $scannedFiles++;
      $content = File::get($file);

      foreach (self::SCAN_PATTERNS as $name => $pattern) {
        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
          foreach ($matches[0] as $match) {
            $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
            $snippet = trim($match[0]);
            $findings[] = sprintf('%s:%d [%s] %s', $file, $line, $name, $snippet);
          }
        }
      }
    }

    $this->info("Archivos escaneados: {$scannedFiles}");

    if (empty($findings)) {
      $this->info('✅ ZERO HARDCODED DATA — sin reintroducciones.');

      return 0;
    }

    $this->warn('⚠️  Posibles datos hardcodeados reintroducidos:');

    foreach ($findings as $finding) {
      $this->line('  ' . $finding);
    }

    $this->line('');
    $this->line('Regla: los datos de negocio/entorno/productivos deben provenir de DB (basic_settings,');
    $this->line('billing_settings, online_gateways), config/env o config/tukipass.php (fallback único).');
    $this->line('Exclusiones justificadas: ver config/tukipass.php (HARDCODE-ALLOW) y docs/audits/hardcoded-data-audit.md');

    return $this->option('fail') ? 1 : 0;
  }

  private function collectFiles(): array
  {
    $dirs = [app_path(), resource_path('views'), resource_path('lang'), base_path('routes'), config_path(), database_path('migrations'), database_path('seeders')];

    $files = [];

    foreach ($dirs as $dir) {
      foreach (File::allFiles($dir) as $file) {
        $path = $file->getPathname();

        if (!preg_match('/\.(php|blade\.php|js)$/', $path)) {
          continue;
        }

        $relative = str_replace(base_path() . '/', '', $path);

        foreach (self::EXCLUDE_DIRS as $exclude) {
          if (str_contains($relative, $exclude)) {
            continue 2;
          }
        }

        $files[] = $relative;
      }
    }

    sort($files);

    return $files;
  }

  private function isAllowed(string $file): bool
  {
    foreach (self::ALLOWED_PATTERNS as $allowed) {
      if (str_starts_with($file, $allowed)) {
        return true;
      }
    }

    return false;
  }
}
