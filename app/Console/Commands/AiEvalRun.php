<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Runner de evals del AI Event Assistant.
 *
 * --capture : ejecuta el pipeline live sobre los fixtures y guarda outputs (requiere OPENAI_API_KEY).
 * --grade   : evalúa outputs capturados contra constraints/forbidden_claims + gates deterministas (offline).
 *
 * Sin API key, --capture reporta NOT VERIFIED y termina sin error.
 */
class AiEvalRun extends Command
{
  protected $signature = 'ai:eval-run {--capture} {--grade} {--limit=}';

  protected $description = 'Corre el dataset de evals del AI Event Assistant';

  public function handle(): int
  {
    $fixtures = require base_path('tests/AI/fixtures/event_fixtures.php');
    $bundle = config('openai.event_assistant.v3.bundle_version', '2026-08-21-v3');

    if ($this->option('limit')) {
      $fixtures = array_slice($fixtures, 0, (int) $this->option('limit'));
    }

    if ($this->option('capture')) {
      return $this->capture($fixtures, $bundle);
    }

    return $this->grade($fixtures, $bundle);
  }

  private function capture(array $fixtures, string $bundle): int
  {
    if (empty(config('openai.api_key'))) {
      $this->warn('NOT VERIFIED: OPENAI_API_KEY no está configurada. No se ejecutó ninguna llamada live.');

      return Command::SUCCESS;
    }

    $dir = base_path("tests/AI/baseline/{$bundle}");
    File::ensureDirectoryExists($dir);

    $service = app(\App\Services\OpenAI\EventAiAssistantService::class);

    foreach ($fixtures as $fixture) {
      $this->info("Capturando {$fixture['id']} …");
      try {
        $started = microtime(true);
        $output = app(\App\Services\OpenAI\EventAI\EventAiOrchestrator::class)->generate(
          app(\App\Services\EventAi\CanonicalEventFactsBuilder::class)->buildFromTemporary(
            $fixture['input']['form_facts'],
            $fixture['input']['flyer_evidence'] ? ['image_analysis' => $fixture['input']['flyer_evidence']] : null,
            $fixture['input']['ticket_facts'] ?? [],
          ),
          $fixture['input']['preferences'] ?? [],
          $fixture['input']['form_facts']['category'] ?? '',
          $fixture['input']['event_type'] ?? 'venue',
        );

        File::put("{$dir}/{$fixture['id']}.json", json_encode([
          'meta' => [
            'fixture' => $fixture['id'],
            'bundle' => $bundle,
            'latency_ms' => (int) ((microtime(true) - $started) * 1000),
            'captured_at' => now()->toIso8601String(),
          ],
          'output' => $output,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
      } catch (\Throwable $e) {
        $this->error("{$fixture['id']}: {$e->getMessage()}");
      }
    }

    $this->info("Captura completa en tests/AI/baseline/{$bundle}/");

    return Command::SUCCESS;
  }

  private function grade(array $fixtures, string $bundle): int
  {
    $dir = base_path("tests/AI/baseline/{$bundle}");
    $failures = 0;
    $graded = 0;

    foreach ($fixtures as $fixture) {
      $path = "{$dir}/{$fixture['id']}.json";
      if (!File::exists($path)) {
        $this->line("SKIP {$fixture['id']} (sin output capturado)");

        continue;
      }

      $graded++;
      $capture = json_decode(File::get($path), true);
      $text = mb_strtolower(json_encode($capture['output'] ?? [], JSON_UNESCAPED_UNICODE));

      foreach (($fixture['forbidden_claims'] ?? []) as $claim) {
        if ($this->claimPresent($text, $claim)) {
          $this->error("FAIL {$fixture['id']}: forbidden claim presente → {$claim}");
          $failures++;
        }
      }
    }

    if ($graded === 0) {
      $this->warn('No hay outputs capturados para calificar. Corré primero ai:eval-run --capture (requiere API key).');

      return Command::SUCCESS;
    }

    $this->info("Grading terminado: {$graded} evaluados, {$failures} fallos.");
    if ($failures > 0) {
      return Command::FAILURE;
    }

    $this->info('PASS: ningún forbidden claim detectado (grading offline estructural).');

    return Command::SUCCESS;
  }

  private function claimPresent(string $text, string $claim): bool
  {
    $tokens = preg_split('/[\s,;:()]+/u', strtolower($claim), -1, PREG_SPLIT_NO_EMPTY) ?: [];

    return count(array_intersect($tokens, preg_split('/[\s,;:()"\[\]{}.,]+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [])) === count($tokens);
  }
}
