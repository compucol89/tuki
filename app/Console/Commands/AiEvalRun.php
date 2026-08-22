<?php

namespace App\Console\Commands;

use App\Services\EventAi\CanonicalEventFacts;
use App\Services\EventAi\CanonicalEventFactsBuilder;
use App\Services\EventAi\EventAiDraftPostProcessor;
use App\Services\OpenAI\EventAI\EventAiOrchestrator;
use App\Services\OpenAI\EventAI\Quality\EventContentGenericnessGate;
use App\Services\OpenAI\EventAI\Quality\EventContentPolicyGate;
use App\Services\OpenAI\EventAI\Quality\EventContentQualityGate;
use App\Services\OpenAI\EventAiAssistantService;
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
  protected $signature = 'ai:eval-run
    {--capture : Captura outputs live con OPENAI_API_KEY}
    {--grade : Califica outputs capturados offline}
    {--limit= : Limita cantidad de fixtures}
    {--pipeline=v3 : Pipeline a capturar: v2 o v3}
    {--bundle= : Bundle a capturar o calificar}
    {--baseline= : Bundle baseline para comparación}
    {--candidate= : Bundle candidato para comparación}';

  protected $description = 'Corre el dataset de evals del AI Event Assistant';

  public function handle(): int
  {
    $fixtures = require base_path('tests/AI/fixtures/event_fixtures.php');
    $bundle = $this->option('bundle') ?: config('openai.event_assistant.v3.bundle_version', '2026-08-21-v3');
    $pipeline = (string) ($this->option('pipeline') ?: 'v3');

    if ($this->option('limit')) {
      $fixtures = array_slice($fixtures, 0, (int) $this->option('limit'));
    }

    if (!in_array($pipeline, ['v2', 'v3'], true)) {
      $this->error('Pipeline inválido. Usá --pipeline=v2 o --pipeline=v3.');

      return Command::FAILURE;
    }

    if ($this->option('capture')) {
      return $this->capture($fixtures, (string) $bundle, $pipeline);
    }

    if ($this->option('baseline') || $this->option('candidate')) {
      if (!$this->option('baseline') || !$this->option('candidate')) {
        $this->error('Para comparar necesitás --baseline y --candidate.');

        return Command::FAILURE;
      }

      return $this->gradeComparison($fixtures, (string) $this->option('baseline'), (string) $this->option('candidate'));
    }

    return $this->grade($fixtures, (string) $bundle);
  }

  private function capture(array $fixtures, string $bundle, string $pipeline): int
  {
    if (empty(config('openai.api_key'))) {
      $this->warn('NOT VERIFIED: OPENAI_API_KEY no está configurada. No se ejecutó ninguna llamada live.');

      return Command::SUCCESS;
    }

    $dir = base_path("tests/AI/baseline/{$bundle}");
    File::ensureDirectoryExists($dir);

    foreach ($fixtures as $fixture) {
      $this->info("Capturando {$fixture['id']} ({$pipeline}) …");
      try {
        $started = microtime(true);
        $output = $pipeline === 'v2'
          ? $this->captureV2($fixture)
          : $this->captureV3($fixture);

        File::put("{$dir}/{$fixture['id']}.json", json_encode([
          'meta' => [
            'fixture' => $fixture['id'],
            'bundle' => $bundle,
            'pipeline' => $pipeline,
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

  private function captureV2(array $fixture): array
  {
    $canonicalFacts = $this->legacyCanonicalFacts($fixture);
    $output = app(EventAiAssistantService::class)->generateContent(
      $canonicalFacts,
      $fixture['input']['preferences'] ?? [],
    );

    return app(EventAiDraftPostProcessor::class)->sanitize($output, $canonicalFacts);
  }

  private function captureV3(array $fixture): array
  {
    return app(EventAiOrchestrator::class)->generate(
      $this->canonicalFacts($fixture),
      $fixture['input']['preferences'] ?? [],
      $fixture['input']['form_facts']['category'] ?? '',
      $fixture['input']['event_type'] ?? 'venue',
    );
  }

  private function grade(array $fixtures, string $bundle): int
  {
    $stats = $this->gradeBundle($fixtures, $bundle, true);

    if ($stats['graded'] === 0) {
      $this->warn('No hay outputs capturados para calificar. Corré primero ai:eval-run --capture (requiere API key).');

      return Command::SUCCESS;
    }

    $this->info("Grading terminado: {$stats['graded']} evaluados, {$stats['hard_failures']} fallos.");
    if ($stats['hard_failures'] > 0) {
      return Command::FAILURE;
    }

    $this->info('PASS: ningún hard gate offline falló.');

    return Command::SUCCESS;
  }

  private function gradeComparison(array $fixtures, string $baseline, string $candidate): int
  {
    $baselineStats = $this->gradeBundle($fixtures, $baseline, false);
    $candidateStats = $this->gradeBundle($fixtures, $candidate, false);

    if ($baselineStats['graded'] === 0 || $candidateStats['graded'] === 0) {
      $this->warn('No hay suficientes outputs para comparar. Capturá ambos bundles antes de --grade --baseline --candidate.');

      return Command::SUCCESS;
    }

    $this->table(
      ['Métrica', $baseline, $candidate, 'Delta'],
      [
        ['Fixtures evaluados', $baselineStats['graded'], $candidateStats['graded'], $candidateStats['graded'] - $baselineStats['graded']],
        ['Hard failures', $baselineStats['hard_failures'], $candidateStats['hard_failures'], $candidateStats['hard_failures'] - $baselineStats['hard_failures']],
        ['Forbidden claims', $baselineStats['forbidden_claims'], $candidateStats['forbidden_claims'], $candidateStats['forbidden_claims'] - $baselineStats['forbidden_claims']],
        ['Quality gate failures', $baselineStats['quality_failures'], $candidateStats['quality_failures'], $candidateStats['quality_failures'] - $baselineStats['quality_failures']],
        ['Policy gate failures', $baselineStats['policy_failures'], $candidateStats['policy_failures'], $candidateStats['policy_failures'] - $baselineStats['policy_failures']],
        ['Genericness promedio', $baselineStats['avg_genericness'], $candidateStats['avg_genericness'], round($candidateStats['avg_genericness'] - $baselineStats['avg_genericness'], 2)],
        ['Latencia p95 ms', $baselineStats['latency_p95_ms'], $candidateStats['latency_p95_ms'], $candidateStats['latency_p95_ms'] - $baselineStats['latency_p95_ms']],
      ],
    );

    if ($candidateStats['hard_failures'] > 0) {
      $this->error('FAIL: el candidato tiene hard failures offline.');

      return Command::FAILURE;
    }

    if ($candidateStats['avg_genericness'] > $baselineStats['avg_genericness']) {
      $this->error('FAIL: el candidato sube la genericidad promedio.');

      return Command::FAILURE;
    }

    $this->info('PASS: candidato sin hard failures y con genericidad igual o menor al baseline.');

    return Command::SUCCESS;
  }

  private function gradeBundle(array $fixtures, string $bundle, bool $verbose): array
  {
    $dir = base_path("tests/AI/baseline/{$bundle}");
    $stats = [
      'graded' => 0,
      'hard_failures' => 0,
      'forbidden_claims' => 0,
      'quality_failures' => 0,
      'policy_failures' => 0,
      'genericness_failures' => 0,
      'genericness_total' => 0.0,
      'avg_genericness' => 0.0,
      'latencies' => [],
      'latency_p95_ms' => 0,
    ];

    foreach ($fixtures as $fixture) {
      $path = "{$dir}/{$fixture['id']}.json";
      if (!File::exists($path)) {
        if ($verbose) {
          $this->line("SKIP {$fixture['id']} (sin output capturado)");
        }

        continue;
      }

      $stats['graded']++;
      $capture = json_decode(File::get($path), true) ?: [];
      $output = $capture['output'] ?? [];
      $facts = $this->canonicalFacts($fixture);
      $text = mb_strtolower((string) json_encode($output, JSON_UNESCAPED_UNICODE));

      if (isset($capture['meta']['latency_ms'])) {
        $stats['latencies'][] = (int) $capture['meta']['latency_ms'];
      }

      foreach (($fixture['forbidden_claims'] ?? []) as $claim) {
        if ($this->claimPresent($text, $claim)) {
          $stats['forbidden_claims']++;
          $stats['hard_failures']++;
          if ($verbose) {
            $this->error("FAIL {$fixture['id']}: forbidden claim presente → {$claim}");
          }
        }
      }

      $qualityFailures = app(EventContentQualityGate::class)->failures($output, $facts);
      if ($qualityFailures !== []) {
        $stats['quality_failures'] += count($qualityFailures);
        $stats['hard_failures'] += count($qualityFailures);
        if ($verbose) {
          $this->error("FAIL {$fixture['id']}: quality gate → " . implode(' ', $qualityFailures));
        }
      }

      $policyFailures = app(EventContentPolicyGate::class)->failures($output, $facts);
      if ($policyFailures !== []) {
        $stats['policy_failures'] += count($policyFailures);
        $stats['hard_failures'] += count($policyFailures);
        if ($verbose) {
          $this->error("FAIL {$fixture['id']}: policy gate → " . implode(' ', $policyFailures));
        }
      }

      $genericness = app(EventContentGenericnessGate::class)->score($output, $facts);
      $score = (float) ($genericness['score'] ?? 0);
      $stats['genericness_total'] += $score;

      if ($score > 4) {
        $stats['genericness_failures']++;
        $stats['hard_failures']++;
        if ($verbose) {
          $this->error("FAIL {$fixture['id']}: genericness {$score}/10 → " . implode(' ', $genericness['signals'] ?? []));
        }
      }
    }

    if ($stats['graded'] > 0) {
      $stats['avg_genericness'] = round($stats['genericness_total'] / $stats['graded'], 2);
      $stats['latency_p95_ms'] = $this->percentile($stats['latencies'], 95);
    }

    return $stats;
  }

  private function canonicalFacts(array $fixture): CanonicalEventFacts
  {
    return app(CanonicalEventFactsBuilder::class)->buildFromTemporary(
      $fixture['input']['form_facts'],
      $fixture['input']['flyer_evidence'] ? ['image_analysis' => $fixture['input']['flyer_evidence']] : null,
      $fixture['input']['ticket_facts'] ?? [],
    );
  }

  private function legacyCanonicalFacts(array $fixture): array
  {
    return [
      'form_facts' => $fixture['input']['form_facts'] ?? [],
      'image_analysis' => $fixture['input']['flyer_evidence'] ?? [],
      'ticket_facts' => $fixture['input']['ticket_facts'] ?? [],
    ];
  }

  private function percentile(array $values, int $percentile): int
  {
    if ($values === []) {
      return 0;
    }

    sort($values);
    $index = (int) ceil(($percentile / 100) * count($values)) - 1;

    return $values[max(0, min($index, count($values) - 1))];
  }

  private function claimPresent(string $text, string $claim): bool
  {
    $tokens = preg_split('/[\s,;:()]+/u', strtolower($claim), -1, PREG_SPLIT_NO_EMPTY) ?: [];

    return count(array_intersect($tokens, preg_split('/[\s,;:()"\[\]{}.,]+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [])) === count($tokens);
  }
}
