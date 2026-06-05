<?php

namespace Tests\Unit\Services\ImageGeneration;

use App\Services\ImageGeneration\FlyerBlendService;
use Tests\TestCase;

class FlyerBlendServiceTest extends TestCase
{
    public function test_composite_keeps_center_pixel_unchanged(): void
    {
        $canvas = $this->solidImage(220, 220, [10, 10, 10]);
        $source = $this->solidImage(100, 100, [240, 80, 20]);

        try {
            app(FlyerBlendService::class)->composite($canvas, $source, 60, 60, 100, 100, 100, 100);

            $this->assertSame([240, 80, 20], $this->pixelRgb($canvas, 110, 110));
        } finally {
            imagedestroy($canvas);
            imagedestroy($source);
        }
    }

    public function test_composite_softens_outer_edge_with_source_color(): void
    {
        $canvas = $this->solidImage(220, 220, [5, 5, 5]);
        $source = $this->solidImage(100, 100, [240, 80, 20]);

        try {
            app(FlyerBlendService::class)->composite($canvas, $source, 60, 60, 100, 100, 100, 100);

            $this->assertNotSame([5, 5, 5], $this->pixelRgb($canvas, 58, 110));
            $this->assertNotSame([240, 80, 20], $this->pixelRgb($canvas, 58, 110));
        } finally {
            imagedestroy($canvas);
            imagedestroy($source);
        }
    }

    public function test_composite_does_not_change_distant_background(): void
    {
        $canvas = $this->solidImage(220, 220, [5, 5, 5]);
        $source = $this->solidImage(100, 100, [240, 80, 20]);

        try {
            app(FlyerBlendService::class)->composite($canvas, $source, 60, 60, 100, 100, 100, 100);

            $this->assertSame([5, 5, 5], $this->pixelRgb($canvas, 8, 8));
        } finally {
            imagedestroy($canvas);
            imagedestroy($source);
        }
    }

    private function solidImage(int $width, int $height, array $rgb): \GdImage
    {
        $image = imagecreatetruecolor($width, $height);
        $color = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
        imagefill($image, 0, 0, $color);

        return $image;
    }

    private function pixelRgb(\GdImage $image, int $x, int $y): array
    {
        $color = imagecolorat($image, $x, $y);

        return [
            ($color >> 16) & 0xFF,
            ($color >> 8) & 0xFF,
            $color & 0xFF,
        ];
    }
}
