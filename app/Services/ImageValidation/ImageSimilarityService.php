<?php

namespace App\Services\ImageValidation;

class ImageSimilarityService
{
    public function passes(string $pathA, string $pathB, float $threshold): bool
    {
        return $this->score($pathA, $pathB) >= $threshold;
    }

    public function score(string $pathA, string $pathB): float
    {
        if (!is_file($pathA) || !is_file($pathB)) {
            return 0.0;
        }

        $imageA = $this->loadImage($pathA);
        $imageB = $this->loadImage($pathB);
        if (!$imageA || !$imageB) {
            return 0.0;
        }

        $sampleA = $this->sample($imageA);
        $sampleB = $this->sample($imageB);

        imagedestroy($imageA);
        imagedestroy($imageB);

        $sum = 0.0;
        $count = count($sampleA);
        for ($i = 0; $i < $count; $i++) {
            $diff = $sampleA[$i] - $sampleB[$i];
            $sum += $diff * $diff;
        }

        $mse = $sum / max(1, $count);
        $score = 1 - ($mse / (255 * 255));

        return max(0.0, min(1.0, round($score, 6)));
    }

    private function sample(\GdImage $image): array
    {
        $width = 64;
        $height = 64;
        $thumb = imagecreatetruecolor($width, $height);
        imagecopyresampled(
            $thumb,
            $image,
            0,
            0,
            0,
            0,
            $width,
            $height,
            imagesx($image),
            imagesy($image)
        );

        $values = [];
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $color = imagecolorat($thumb, $x, $y);
                $r = ($color >> 16) & 0xFF;
                $g = ($color >> 8) & 0xFF;
                $b = $color & 0xFF;
                $values[] = (int) round(($r * 0.299) + ($g * 0.587) + ($b * 0.114));
            }
        }

        imagedestroy($thumb);

        return $values;
    }

    private function loadImage(string $path): \GdImage|false
    {
        $imageInfo = @getimagesize($path);
        if ($imageInfo === false) {
            return false;
        }

        return match ($imageInfo[2] ?? null) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };
    }
}
