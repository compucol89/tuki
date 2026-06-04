<?php

namespace App\Services\OpenAI;

use App\Exceptions\OpenAiNonRetryableException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ImageGenerationService
{
    private array $tempFiles = [];

    public function __destruct()
    {
        foreach ($this->tempFiles as $file) {
            @unlink($file);
        }
    }

    public function generateEdit(string $referenceImagePath, string $prompt, string $size): string
    {
        $apiKey = config('openai.api_key');
        if (empty($apiKey)) {
            throw new OpenAiNonRetryableException('OPENAI_API_KEY is not configured');
        }

        $imageStream = fopen($referenceImagePath, 'r');
        if ($imageStream === false) {
            throw new OpenAiNonRetryableException('Reference image is not readable');
        }

        $response = Http::withToken($apiKey)
            ->timeout(config('openai.timeout', 60))
            ->attach(
                'image[]',
                $imageStream,
                basename($referenceImagePath)
            )
            ->post(config('openai.base_url') . '/images/edits', [
                'model' => config('openai.model'),
                'prompt' => $prompt,
                'size' => $size,
                'n' => 1,
                'output_format' => 'png',
            ]);

        if ($response->failed()) {
            $status = $response->status();
            $error = $response->json('error.message') ?? $response->body();
            Log::error('OpenAI image generation failed', [
                'status' => $status,
                'error' => $error,
            ]);

            if ($status >= 500 || $status === 429) {
                throw new RuntimeException("OpenAI API error ({$status}): {$error}");
            }

            throw new OpenAiNonRetryableException("OpenAI API error ({$status}): {$error}");
        }

        $b64 = $response->json('data.0.b64_json');
        if (empty($b64)) {
            throw new OpenAiNonRetryableException('OpenAI returned empty response');
        }

        return $b64;
    }

    public function extendBackground(string $referenceImagePath, string $size): string
    {
        $apiKey = config('openai.api_key');
        if (empty($apiKey)) {
            throw new OpenAiNonRetryableException('OPENAI_API_KEY is not configured');
        }

        [$canvasPath, $maskPath] = $this->makeExtensionInputFiles($referenceImagePath, $size);

        $canvasStream = fopen($canvasPath, 'r');
        $maskStream = fopen($maskPath, 'r');
        if ($canvasStream === false || $maskStream === false) {
            throw new OpenAiNonRetryableException('Extension input image is not readable');
        }

        $response = Http::withToken($apiKey)
            ->timeout(config('openai.timeout', 60))
            ->attach('image[]', $canvasStream, basename($canvasPath))
            ->attach('mask', $maskStream, basename($maskPath))
            ->post(config('openai.base_url') . '/images/edits', [
                'model' => config('openai.model'),
                'prompt' => $this->backgroundExtensionPrompt(),
                'size' => $size,
                'n' => 1,
                'output_format' => 'png',
                'input_fidelity' => 'high',
            ]);

        if ($response->failed()) {
            $status = $response->status();
            $error = $response->json('error.message') ?? $response->body();
            Log::error('OpenAI background extension failed', [
                'status' => $status,
                'error' => $error,
            ]);

            if ($status >= 500 || $status === 429) {
                throw new RuntimeException("OpenAI API error ({$status}): {$error}");
            }

            throw new OpenAiNonRetryableException("OpenAI API error ({$status}): {$error}");
        }

        $b64 = $response->json('data.0.b64_json');
        if (empty($b64)) {
            throw new OpenAiNonRetryableException('OpenAI returned empty response');
        }

        return $this->compositeOriginalOnTop(base64_decode($b64), $referenceImagePath, $size);
    }

    private function makeExtensionInputFiles(string $referenceImagePath, string $size): array
    {
        [$targetWidth, $targetHeight] = $this->parseSize($size);
        $source = $this->loadImage($referenceImagePath);
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        [$dstX, $dstY, $newWidth, $newHeight] = $this->containedPlacement($sourceWidth, $sourceHeight, $targetWidth, $targetHeight);

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        $background = imagecolorallocate($canvas, 0, 0, 0);
        imagefill($canvas, 0, 0, $background);
        imagecopyresampled($canvas, $source, $dstX, $dstY, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

        $mask = imagecreatetruecolor($targetWidth, $targetHeight);
        imagefill($mask, 0, 0, imagecolorallocate($mask, 255, 255, 255));
        imagefilledrectangle($mask, $dstX, $dstY, $dstX + $newWidth - 1, $dstY + $newHeight - 1, imagecolorallocate($mask, 0, 0, 0));

        $canvasPath = $this->tempPngPath();
        $maskPath = $this->tempPngPath();
        imagepng($canvas, $canvasPath);
        imagepng($mask, $maskPath);

        imagedestroy($source);
        imagedestroy($canvas);
        imagedestroy($mask);

        return [$canvasPath, $maskPath];
    }

    private function compositeOriginalOnTop(string $generatedBytes, string $referenceImagePath, string $size): string
    {
        [$targetWidth, $targetHeight] = $this->parseSize($size);
        $generatedPath = $this->tempPngPath();
        file_put_contents($generatedPath, $generatedBytes);

        $canvas = $this->loadImage($generatedPath);
        $source = $this->loadImage($referenceImagePath);
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        [$dstX, $dstY, $newWidth, $newHeight] = $this->containedPlacement($sourceWidth, $sourceHeight, $targetWidth, $targetHeight);

        imagecopyresampled($canvas, $source, $dstX, $dstY, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

        ob_start();
        imagepng($canvas, null, 6);
        $bytes = ob_get_clean();

        imagedestroy($canvas);
        imagedestroy($source);

        if ($bytes === false) {
            throw new OpenAiNonRetryableException('Could not compose OpenAI background extension output');
        }

        return $bytes;
    }

    private function backgroundExtensionPrompt(): string
    {
        return 'Extend only the empty masked background areas to match the original flyer style. '
            . 'Do not create text, logos, badges, addresses, dates, people, symbols, UI elements, or new objects. '
            . 'Generate only abstract/background continuation outside the protected original flyer area.';
    }

    private function containedPlacement(int $sourceWidth, int $sourceHeight, int $targetWidth, int $targetHeight): array
    {
        $scale = min($targetWidth / $sourceWidth, $targetHeight / $sourceHeight);
        $newWidth = (int) floor($sourceWidth * $scale);
        $newHeight = (int) floor($sourceHeight * $scale);

        return [
            (int) floor(($targetWidth - $newWidth) / 2),
            (int) floor(($targetHeight - $newHeight) / 2),
            $newWidth,
            $newHeight,
        ];
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
            throw new OpenAiNonRetryableException('Image is not readable');
        }

        $image = match ($imageInfo[2] ?? null) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        if (!$image instanceof \GdImage) {
            throw new OpenAiNonRetryableException('Image format is not supported');
        }

        return $image;
    }

    private function tempPngPath(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'openai_extend_') . '.png';
        $this->tempFiles[] = $path;

        return $path;
    }
}
