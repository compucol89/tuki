<?php

namespace Tests\Unit\Services\ImageGeneration;

use App\Services\ImageGeneration\SmartFlyerPlacementService;
use Tests\TestCase;

class SmartFlyerPlacementServiceTest extends TestCase
{
    public function test_landscape_outputs_use_more_original_flyer_and_less_generated_margin(): void
    {
        $placement = app(SmartFlyerPlacementService::class)->placement(600, 600, 1536, 1024);

        $this->assertSame([215, -41, 1105, 1105], $placement);
    }

    public function test_square_outputs_keep_contain_placement(): void
    {
        $placement = app(SmartFlyerPlacementService::class)->placement(600, 600, 1024, 1024);

        $this->assertSame([0, 0, 1024, 1024], $placement);
    }

    public function test_wide_source_is_not_upscaled_beyond_canvas_for_landscape_outputs(): void
    {
        $placement = app(SmartFlyerPlacementService::class)->placement(1200, 600, 1536, 1024);

        $this->assertSame([0, 128, 1536, 768], $placement);
    }
}
