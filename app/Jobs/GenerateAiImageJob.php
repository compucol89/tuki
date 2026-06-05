<?php

namespace App\Jobs;

use App\Exceptions\OpenAiNonRetryableException;
use App\Models\Event\EventAiGeneration;
use App\Services\ImageGeneration\BlurExtendService;
use App\Services\ImageGeneration\SmartFlyerPlacementService;
use App\Services\ImageValidation\ImageSimilarityService;
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

    public function handle(BlurExtendService $blurExtendService, ImageGenerationService $imageGenerationService): void
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
            $costEstimate = 0.0;
            $validationScore = null;
            $imageBytes = null;

            if (!config('openai.smart_crop_mode', true) && config('openai.hybrid_mode', false)) {
                try {
                    $imageBytes = $imageGenerationService->extendBackground($refPath, $size);
                    $validationScore = $this->hybridValidationScore($imageBytes, $refPath, $size);
                    if ($validationScore >= (float) config('openai.ssim_threshold', 0.99)) {
                        $costEstimate = $this->estimateCost($size);
                    } else {
                        Log::warning('ai.image.hybrid_validation_failed_falling_back_to_blur_extend', [
                            'generation_id' => $generation->id,
                        ]);
                        $imageBytes = null;
                    }
                } catch (Throwable $e) {
                    Log::warning('ai.image.hybrid_failed_falling_back_to_blur_extend', [
                        'generation_id' => $generation->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if ($imageBytes === null) {
                $imageBytes = $blurExtendService->render($refPath, $size);
            }

            $outputPath = $this->saveImage($event->id, $generation->format, $imageBytes);

            $durationMs = (int) ((microtime(true) - $start) * 1000);

            $generation->markCompleted($durationMs, $outputPath, $costEstimate, $validationScore);

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

    private function hybridValidationScore(string $imageBytes, string $refPath, string $size): float
    {
        [$targetWidth, $targetHeight] = $this->parseSize($size);
        $refInfo = getimagesize($refPath);
        if ($refInfo === false) {
            return 0.0;
        }

        [$sourceWidth, $sourceHeight] = $refInfo;
        [$dstX, $dstY, $newWidth, $newHeight] = app(SmartFlyerPlacementService::class)
            ->placement($sourceWidth, $sourceHeight, $targetWidth, $targetHeight);

        $outputPath = tempnam(sys_get_temp_dir(), 'ai_hybrid_output_') . '.png';
        $cropPath = tempnam(sys_get_temp_dir(), 'ai_hybrid_crop_') . '.png';
        file_put_contents($outputPath, $imageBytes);

        $output = imagecreatefrompng($outputPath);
        if (!$output instanceof \GdImage) {
            @unlink($outputPath);
            @unlink($cropPath);
            return 0.0;
        }

        $crop = imagecreatetruecolor($newWidth, $newHeight);
        imagecopy($crop, $output, 0, 0, $dstX, $dstY, $newWidth, $newHeight);
        imagepng($crop, $cropPath);

        imagedestroy($output);
        imagedestroy($crop);

        $score = app(ImageSimilarityService::class)->score(
            $refPath,
            $cropPath
        );

        @unlink($outputPath);
        @unlink($cropPath);

        return $score;
    }

    private function parseSize(string $size): array
    {
        if (!preg_match('/^(\d+)x(\d+)$/', $size, $matches)) {
            throw new OpenAiNonRetryableException("Invalid image size: {$size}");
        }

        return [(int) $matches[1], (int) $matches[2]];
    }

    private function estimateCost(string $size): float
    {
        return match (true) {
            str_contains($size, '1024x1024') => 0.04,
            str_contains($size, '1536x1024') => 0.12,
            default => 0.08,
        };
    }
}
