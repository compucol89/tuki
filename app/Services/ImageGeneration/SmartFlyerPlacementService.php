<?php

namespace App\Services\ImageGeneration;

class SmartFlyerPlacementService
{
    private const LANDSCAPE_MAX_CROP_SCALE = 1.08;

    public function placement(int $sourceWidth, int $sourceHeight, int $targetWidth, int $targetHeight): array
    {
        $containScale = min($targetWidth / $sourceWidth, $targetHeight / $sourceHeight);
        $scale = $containScale;

        $sourceAspect = $sourceWidth / $sourceHeight;
        $targetAspect = $targetWidth / $targetHeight;

        if ($targetAspect > 1 && $sourceAspect < $targetAspect) {
            $coverScale = max($targetWidth / $sourceWidth, $targetHeight / $sourceHeight);
            $scale = min($coverScale, $containScale * self::LANDSCAPE_MAX_CROP_SCALE);
        }

        $newWidth = (int) floor($sourceWidth * $scale);
        $newHeight = (int) floor($sourceHeight * $scale);

        return [
            (int) floor(($targetWidth - $newWidth) / 2),
            (int) floor(($targetHeight - $newHeight) / 2),
            $newWidth,
            $newHeight,
        ];
    }
}
