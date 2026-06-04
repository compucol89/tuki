<?php

namespace App\Services\ImageGeneration;

use App\Exceptions\OpenAiNonRetryableException;

class BlurExtendService
{
    public function render(string $sourcePath, string $size): string
    {
        [$targetWidth, $targetHeight] = $this->parseSize($size);

        $source = $this->loadImage($sourcePath);
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        $background = $this->makeBlurredBackground($source, $sourceWidth, $sourceHeight, $targetWidth, $targetHeight);
        imagecopy($canvas, $background, 0, 0, 0, 0, $targetWidth, $targetHeight);

        $scale = min($targetWidth / $sourceWidth, $targetHeight / $sourceHeight);
        $newWidth = (int) floor($sourceWidth * $scale);
        $newHeight = (int) floor($sourceHeight * $scale);
        $dstX = (int) floor(($targetWidth - $newWidth) / 2);
        $dstY = (int) floor(($targetHeight - $newHeight) / 2);

        imagecopyresampled(
            $canvas,
            $source,
            $dstX,
            $dstY,
            0,
            0,
            $newWidth,
            $newHeight,
            $sourceWidth,
            $sourceHeight
        );

        ob_start();
        imagepng($canvas, null, 6);
        $bytes = ob_get_clean();

        imagedestroy($source);
        imagedestroy($background);
        imagedestroy($canvas);

        if ($bytes === false) {
            throw new OpenAiNonRetryableException('Could not render blur extend image variant');
        }

        return $bytes;
    }

    private function makeBlurredBackground(
        \GdImage $source,
        int $sourceWidth,
        int $sourceHeight,
        int $targetWidth,
        int $targetHeight
    ): \GdImage {
        $background = imagecreatetruecolor($targetWidth, $targetHeight);
        $scale = max($targetWidth / $sourceWidth, $targetHeight / $sourceHeight);
        $newWidth = (int) ceil($sourceWidth * $scale);
        $newHeight = (int) ceil($sourceHeight * $scale);
        $dstX = (int) floor(($targetWidth - $newWidth) / 2);
        $dstY = (int) floor(($targetHeight - $newHeight) / 2);

        imagecopyresampled(
            $background,
            $source,
            $dstX,
            $dstY,
            0,
            0,
            $newWidth,
            $newHeight,
            $sourceWidth,
            $sourceHeight
        );

        for ($i = 0; $i < 18; $i++) {
            imagefilter($background, IMG_FILTER_GAUSSIAN_BLUR);
        }

        imagefilter($background, IMG_FILTER_BRIGHTNESS, -18);

        return $background;
    }

    private function parseSize(string $size): array
    {
        if (!preg_match('/^(\d+)x(\d+)$/', $size, $matches)) {
            throw new OpenAiNonRetryableException("Invalid image size: {$size}");
        }

        return [(int) $matches[1], (int) $matches[2]];
    }

    private function loadImage(string $path): \GdImage
    {
        $imageInfo = @getimagesize($path);
        if ($imageInfo === false) {
            throw new OpenAiNonRetryableException('Reference image is not readable');
        }

        $image = match ($imageInfo[2] ?? null) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        if (!$image instanceof \GdImage) {
            throw new OpenAiNonRetryableException('Reference image format is not supported');
        }

        return $image;
    }
}
