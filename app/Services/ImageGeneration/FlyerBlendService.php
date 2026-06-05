<?php

namespace App\Services\ImageGeneration;

class FlyerBlendService
{
    public function composite(
        \GdImage $canvas,
        \GdImage $source,
        int $dstX,
        int $dstY,
        int $newWidth,
        int $newHeight,
        int $sourceWidth,
        int $sourceHeight
    ): void {
        $feather = max(8, min(22, (int) floor(min($newWidth, $newHeight) * 0.025)));

        $this->paintOuterTransition($canvas, $source, $dstX, $dstY, $newWidth, $newHeight, $sourceWidth, $sourceHeight, $feather);

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
    }

    private function paintOuterTransition(
        \GdImage $canvas,
        \GdImage $source,
        int $dstX,
        int $dstY,
        int $newWidth,
        int $newHeight,
        int $sourceWidth,
        int $sourceHeight,
        int $feather
    ): void {
        $canvasWidth = imagesx($canvas);
        $canvasHeight = imagesy($canvas);

        $left = $this->edgeColor($source, $sourceWidth, $sourceHeight, 'left');
        $right = $this->edgeColor($source, $sourceWidth, $sourceHeight, 'right');
        $top = $this->edgeColor($source, $sourceWidth, $sourceHeight, 'top');
        $bottom = $this->edgeColor($source, $sourceWidth, $sourceHeight, 'bottom');

        for ($i = $feather; $i >= 1; $i--) {
            $alpha = 127 - (int) round((($feather - $i + 1) / $feather) * 38);

            $this->fillRect($canvas, $dstX - $i, $dstY, 1, $newHeight, $left, $alpha, $canvasWidth, $canvasHeight);
            $this->fillRect($canvas, $dstX + $newWidth + $i - 1, $dstY, 1, $newHeight, $right, $alpha, $canvasWidth, $canvasHeight);
            $this->fillRect($canvas, $dstX, $dstY - $i, $newWidth, 1, $top, $alpha, $canvasWidth, $canvasHeight);
            $this->fillRect($canvas, $dstX, $dstY + $newHeight + $i - 1, $newWidth, 1, $bottom, $alpha, $canvasWidth, $canvasHeight);
        }

        $shadow = [0, 0, 0];
        for ($i = 1; $i <= $feather; $i++) {
            $alpha = 120 + (int) round(($i / $feather) * 7);
            $this->fillRect($canvas, $dstX - $i, $dstY - $i, $newWidth + ($i * 2), 1, $shadow, $alpha, $canvasWidth, $canvasHeight);
            $this->fillRect($canvas, $dstX - $i, $dstY + $newHeight + $i - 1, $newWidth + ($i * 2), 1, $shadow, $alpha, $canvasWidth, $canvasHeight);
            $this->fillRect($canvas, $dstX - $i, $dstY - $i, 1, $newHeight + ($i * 2), $shadow, $alpha, $canvasWidth, $canvasHeight);
            $this->fillRect($canvas, $dstX + $newWidth + $i - 1, $dstY - $i, 1, $newHeight + ($i * 2), $shadow, $alpha, $canvasWidth, $canvasHeight);
        }
    }

    private function edgeColor(\GdImage $source, int $width, int $height, string $edge): array
    {
        $samples = 24;
        $r = 0;
        $g = 0;
        $b = 0;

        for ($i = 0; $i < $samples; $i++) {
            $ratio = $samples === 1 ? 0 : $i / ($samples - 1);
            $x = match ($edge) {
                'left' => 0,
                'right' => $width - 1,
                default => (int) round($ratio * ($width - 1)),
            };
            $y = match ($edge) {
                'top' => 0,
                'bottom' => $height - 1,
                default => (int) round($ratio * ($height - 1)),
            };

            $color = imagecolorat($source, $x, $y);
            $r += ($color >> 16) & 0xFF;
            $g += ($color >> 8) & 0xFF;
            $b += $color & 0xFF;
        }

        return [
            (int) round($r / $samples),
            (int) round($g / $samples),
            (int) round($b / $samples),
        ];
    }

    private function fillRect(
        \GdImage $canvas,
        int $x,
        int $y,
        int $width,
        int $height,
        array $rgb,
        int $alpha,
        int $canvasWidth,
        int $canvasHeight
    ): void {
        $left = max(0, $x);
        $top = max(0, $y);
        $right = min($canvasWidth - 1, $x + $width - 1);
        $bottom = min($canvasHeight - 1, $y + $height - 1);

        if ($left > $right || $top > $bottom) {
            return;
        }

        $color = imagecolorallocatealpha($canvas, $rgb[0], $rgb[1], $rgb[2], max(0, min(127, $alpha)));
        imagefilledrectangle($canvas, $left, $top, $right, $bottom, $color);
    }
}
