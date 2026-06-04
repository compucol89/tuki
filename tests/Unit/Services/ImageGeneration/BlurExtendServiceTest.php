<?php

namespace Tests\Unit\Services\ImageGeneration;

use App\Exceptions\OpenAiNonRetryableException;
use App\Services\ImageGeneration\BlurExtendService;
use Tests\TestCase;

class BlurExtendServiceTest extends TestCase
{
    private array $cleanupFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->cleanupFiles as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    public function test_render_creates_target_dimensions_with_background_from_source(): void
    {
        $sourcePath = $this->makeSolidPng([240, 180, 40]);

        $bytes = app(BlurExtendService::class)->render($sourcePath, '1536x1024');
        $outputPath = $this->writeTempPng($bytes);

        $this->assertSame([1536, 1024], array_slice(getimagesize($outputPath), 0, 2));
        $rgb = $this->pixelRgb($outputPath, 24, 512);
        $this->assertNotSame([8, 12, 24], $rgb);
    }

    public function test_render_preserves_center_content(): void
    {
        $sourcePath = $this->makeSolidPng([20, 200, 80]);

        $bytes = app(BlurExtendService::class)->render($sourcePath, '1536x1024');
        $outputPath = $this->writeTempPng($bytes);

        $this->assertSame([20, 200, 80], $this->pixelRgb($outputPath, 768, 512));
    }

    public function test_render_throws_for_invalid_size(): void
    {
        $sourcePath = $this->makeSolidPng([20, 200, 80]);

        $this->expectException(OpenAiNonRetryableException::class);
        $this->expectExceptionMessage('Invalid image size');

        app(BlurExtendService::class)->render($sourcePath, 'bad-size');
    }

    private function makeSolidPng(array $rgb): string
    {
        $path = tempnam(sys_get_temp_dir(), 'blur_src_') . '.png';
        $img = imagecreatetruecolor(600, 600);
        $color = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
        imagefill($img, 0, 0, $color);
        imagepng($img, $path);
        imagedestroy($img);

        $this->cleanupFiles[] = $path;

        return $path;
    }

    private function writeTempPng(string $bytes): string
    {
        $path = tempnam(sys_get_temp_dir(), 'blur_out_') . '.png';
        file_put_contents($path, $bytes);
        $this->cleanupFiles[] = $path;

        return $path;
    }

    private function pixelRgb(string $path, int $x, int $y): array
    {
        $image = imagecreatefrompng($path);
        $color = imagecolorat($image, $x, $y);
        imagedestroy($image);

        return [
            ($color >> 16) & 0xFF,
            ($color >> 8) & 0xFF,
            $color & 0xFF,
        ];
    }
}
