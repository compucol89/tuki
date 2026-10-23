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
    $this->assertFalse(DemoEventExclusion::isHidden(134, 'halloween-en-la-troja'));
  }

  public function test_showcase_demo_is_visible_but_still_demo(): void
  {
    $slug = 'fiesta-de-cachengue-noche-de-club-prueba';

    $this->assertTrue(DemoEventExclusion::isDemo(133, $slug));
    $this->assertFalse(DemoEventExclusion::isHidden(133, $slug));
  }

  public function test_generic_demo_suffix_is_hidden(): void
  {
    $this->assertTrue(DemoEventExclusion::isHidden(999, 'evento-generico-demo'));
  }

  public function test_legacy_demo_id_is_hidden(): void
  {
    $this->assertTrue(DemoEventExclusion::isHidden(104, 'cualquier-slug'));
  }
}
