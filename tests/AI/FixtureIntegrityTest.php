<?php

namespace Tests\AI;

use Tests\TestCase;

/**
 * Integridad estructural del dataset de evals (offline).
 * El runner live (ai:eval-run) usa los mismos fixtures con API key.
 */
class FixtureIntegrityTest extends TestCase
{
  private function fixtures(): array
  {
    return require __DIR__ . '/fixtures/event_fixtures.php';
  }

  public function test_fixtures_have_unique_ids(): void
  {
    $ids = array_map(fn ($f) => $f['id'], $this->fixtures());

    $this->assertCount(count(array_unique($ids)), $ids, 'IDs duplicados en fixtures.');
  }

  public function test_each_fixture_has_required_structure(): void
  {
    foreach ($this->fixtures() as $fixture) {
      $this->assertArrayHasKey('id', $fixture);
      $this->assertArrayHasKey('input', $fixture);
      $this->assertArrayHasKey('constraints', $fixture);
      $this->assertArrayHasKey('forbidden_claims', $fixture);
      $this->assertArrayHasKey('expected_critical_fields', $fixture);
      $this->assertNotEmpty($fixture['input']['form_facts'] ?? [], 'form_facts vacíos en ' . $fixture['id']);
    }
  }

  public function test_forbidden_claims_never_empty(): void
  {
    foreach ($this->fixtures() as $fixture) {
      $this->assertNotEmpty($fixture['forbidden_claims'], 'forbidden_claims vacíos en ' . $fixture['id']);
    }
  }

  public function test_no_placeholder_values(): void
  {
    $haystack = json_encode($this->fixtures(), JSON_UNESCAPED_UNICODE);

    $this->assertStringNotContainsString('TBD', $haystack);
    $this->assertStringNotContainsString('TODO:', $haystack);
    $this->assertStringNotContainsString('lorem ipsum', strtolower($haystack));
    $this->assertStringNotContainsString('XXX', $haystack);
  }

  public function test_minimum_fixture_count(): void
  {
    $this->assertGreaterThanOrEqual(16, count($this->fixtures()), 'Se esperan al menos 16 fixtures.');
  }

  public function test_archetype_coverage(): void
  {
    $ids = array_map(fn ($f) => $f['id'], $this->fixtures());

    foreach (['venue_nightlife', 'online_', 'free_', 'sports_', 'theatre_', 'conference_', 'prompt_injection', 'form_flyer_conflict'] as $required) {
      $this->assertTrue(
        (bool) array_filter($ids, fn ($id) => str_starts_with($id, $required)),
        "Falta fixture con prefijo {$required}"
      );
    }
  }
}
