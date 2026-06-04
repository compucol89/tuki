<?php

namespace Tests\Unit\Services\ImageValidation;

use App\Services\ImageValidation\ImageSimilarityService;
use Tests\TestCase;

class ImageSimilarityServiceTest extends TestCase
{
    private array $cleanupFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->cleanupFiles as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    public function test_identical_images_return_one(): void
    {
        $path = $this->makeSolidPng([200, 30, 30]);

        $this->assertSame(1.0, app(ImageSimilarityService::class)->score($path, $path));
    }

    public function test_different_images_return_low_score(): void
    {
        $pathA = $this->makeSolidPng([255, 255, 255]);
        $pathB = $this->makeSolidPng([0, 0, 0]);

        $this->assertLessThan(0.2, app(ImageSimilarityService::class)->score($pathA, $pathB));
    }

    public function test_threshold_is_configurable(): void
    {
        $pathA = $this->makeSolidPng([120, 120, 120]);
        $pathB = $this->makeSolidPng([122, 122, 122]);

        $service = app(ImageSimilarityService::class);

        $this->assertTrue($service->passes($pathA, $pathB, 0.95));
        $this->assertFalse($service->passes($pathA, $pathB, 0.99999));
    }

    public function test_missing_file_returns_zero(): void
    {
        $path = $this->makeSolidPng([120, 120, 120]);

        $this->assertSame(0.0, app(ImageSimilarityService::class)->score($path, '/tmp/missing-image.png'));
    }

    private function makeSolidPng(array $rgb): string
    {
        $path = tempnam(sys_get_temp_dir(), 'sim_') . '.png';
        $img = imagecreatetruecolor(64, 64);
        $color = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
        imagefill($img, 0, 0, $color);
        imagepng($img, $path);
        imagedestroy($img);

        $this->cleanupFiles[] = $path;

        return $path;
    }
}
