<?php

namespace Tests\Feature;

use Tests\TestCase;

class EventAddonRouteHelperTest extends TestCase
{
  public function test_addons_route_passes_nested_route_parameters(): void
  {
    $this->assertSame(
      url('/admin/event/123/addons/section/456'),
      addonsRoute('section.update', 123, 456)
    );

    $this->assertSame(
      url('/admin/event/123/addons/addon/789'),
      addonsRoute('addon.destroy', 123, 789)
    );
  }
}
