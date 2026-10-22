<?php

namespace Tests\Unit\Support;

use App\Support\DemoEventExclusion;
use Tests\TestCase;

class DemoEventExclusionTest extends TestCase
{
  public function test_legacy_demo_id_is_excluded(): void
  {
    $this->assertTrue(DemoEventExclusion::isDemo(104, 'cualquier-slug'));
  }

  public function test_slug_ending_in_demo_is_excluded(): void
  {
    $this->assertTrue(DemoEventExclusion::isDemo(999, 'rooftop-networking-night-demo'));
  }

  public function test_slug_ending_in_prueba_is_excluded(): void
  {
    $this->assertTrue(DemoEventExclusion::isDemo(133, 'fiesta-de-cachengue-noche-de-club-prueba'));
  }

  public function test_real_event_is_not_excluded(): void
  {
    $this->assertFalse(DemoEventExclusion::isDemo(134, 'halloween-en-la-troja'));
  }
}
