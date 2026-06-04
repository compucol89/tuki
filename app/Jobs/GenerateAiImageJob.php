<?php

namespace App\Jobs;

use App\Exceptions\OpenAiNonRetryableException;
use App\Models\Event\EventAiGeneration;
use App\Services\OpenAI\ImageGenerationService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class GenerateAiImageJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $generationId)
    {
        $this->onQueue(config('openai.queue', 'ai-images'));
    }

    public function backoff(): array
    {
        return [30, 120, 480];
    }

    public function handle(ImageGenerationService $service): void
    {
        $generation = EventAiGeneration::findOrFail($this->generationId);
        $generation->markRunning();

        $start = microtime(true);

        try {
            $event = $generation->event()->with('information')->first();
            if (empty($event->thumbnail)) {
                throw new RuntimeException("Event {$event->id} thumbnail is empty");
            }

            $refPath = public_path('assets/admin/img/event/thumbnail/' . $event->thumbnail);
            if (!file_exists($refPath)) {
                throw new OpenAiNonRetryableException("Reference image not found: {$refPath}");
            }

            $this->validateReferenceImage($refPath);

            $size = config("openai.formats.{$generation->format}.size");
            $imageBytes = $this->createPreservedVariant($refPath, $size);

            $outputPath = $this->saveImage($event->id, $generation->format, $imageBytes);

            $durationMs = (int) ((microtime(true) - $start) * 1000);
            $costEstimate = 0.0;

            $generation->markCompleted($durationMs, $outputPath, $costEstimate);

            Log::info('ai.image.generated', [
                'event_id' => $event->id,
                'organizer_id' => $event->organizer_id,
                'format' => $generation->format,
                'duration_ms' => $durationMs,
                'cost_estimate' => $costEstimate,
            ]);
        } catch (Throwable $e) {
            $generation->markFailed($e->getMessage());
            Log::error('ai.image.failed', [
                'generation_id' => $generation->id,
                'error' => $e->getMessage(),
            ]);

            if ($e instanceof OpenAiNonRetryableException) {
                $this->fail($e);
                return;
            }

            throw $e;
        }
    }

    private function validateReferenceImage(string $refPath): void
    {
        $maxSizeKb = (int) config('openai.reference.max_size_kb', 10240);
        $minDim = (int) config('openai.reference.min_dimension', 512);

        $sizeBytes = filesize($refPath);
        if ($sizeBytes === false) {
            throw new OpenAiNonRetryableException("Reference image is not readable: {$refPath}");
        }
        if ($sizeBytes > $maxSizeKb * 1024) {
            throw new OpenAiNonRetryableException("Reference image exceeds max size ({$maxSizeKb}KB)");
        }

        $imageInfo = @getimagesize($refPath);
        if ($imageInfo === false) {
            throw new OpenAiNonRetryableException("Reference image is not a valid image file");
        }
        [$width, $height] = $imageInfo;
        if ($width < $minDim || $height < $minDim) {
            throw new OpenAiNonRetryableException("Reference image is too small (min {$minDim}px on each side)");
        }
    }

    private function createPreservedVariant(string $refPath, string $size): string
    {
        [$targetWidth, $targetHeight] = $this->parseSize($size);

        $source = $this->loadImage($refPath);
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        $scale = min($targetWidth / $sourceWidth, $targetHeight / $sourceHeight);
        $newWidth = (int) floor($sourceWidth * $scale);
        $newHeight = (int) floor($sourceHeight * $scale);
        $dstX = (int) floor(($targetWidth - $newWidth) / 2);
        $dstY = (int) floor(($targetHeight - $newHeight) / 2);

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        $background = imagecolorallocate($canvas, 8, 12, 24);
        imagefill($canvas, 0, 0, $background);

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
        imagedestroy($canvas);

        if ($bytes === false) {
            throw new OpenAiNonRetryableException('Could not render preserved image variant');
        }

        return $bytes;
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

    private function saveImage(int $eventId, string $format, string $imageBytes): string
    {
        $dir = public_path("assets/admin/img/event-ai/{$eventId}");
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = $format . '_' . $this->generationId . '.png';
        $fullPath = "{$dir}/{$filename}";
        file_put_contents($fullPath, $imageBytes);

        return "assets/admin/img/event-ai/{$eventId}/{$filename}";
    }
}
