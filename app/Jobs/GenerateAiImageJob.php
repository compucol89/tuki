<?php

namespace App\Jobs;

use App\Exceptions\OpenAiNonRetryableException;
use App\Models\Event\EventAiGeneration;
use App\Models\Event\EventImage;
use App\Services\OpenAI\EventImagePromptBuilder;
use App\Services\OpenAI\ImageGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class GenerateAiImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

            $prompt = app(EventImagePromptBuilder::class)
                ->build($generation->format, $event);

            $size = config("openai.formats.{$generation->format}.size");
            $b64 = $service->generateEdit($refPath, $prompt, $size);

            $outputPath = $this->saveImage($event->id, $generation->format, $b64);

            $durationMs = (int) ((microtime(true) - $start) * 1000);
            $costEstimate = $this->estimateCost($size);

            $generation->markCompleted($durationMs, $outputPath, $costEstimate);

            DB::transaction(function () use ($event, $generation, $outputPath) {
                $this->assignToSlot($event, $generation->format, $outputPath);
            });

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

    private function saveImage(int $eventId, string $format, string $base64): string
    {
        $dir = public_path("assets/admin/img/event-ai/{$eventId}");
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = config("openai.formats.{$format}.output_filename", "{$format}.png");
        $fullPath = "{$dir}/{$filename}";
        file_put_contents($fullPath, base64_decode($base64));

        return "assets/admin/img/event-ai/{$eventId}/{$filename}";
    }

    private function assignToSlot($event, string $format, string $outputPath): void
    {
        match ($format) {
            'square' => $this->createEventImage($event->id, $format, $outputPath),
            'og' => $event->update(['og_image' => basename($outputPath)]),
            'gallery' => $this->createEventImage($event->id, $format, $outputPath),
        };
    }

    private function createEventImage(int $eventId, string $format, string $outputPath): void
    {
        $source = public_path($outputPath);
        $galleryDir = public_path('assets/admin/img/event-gallery/');
        File::ensureDirectoryExists($galleryDir);

        $filename = 'ai_' . $format . '_' . $eventId . '_' . uniqid() . '.png';
        File::copy($source, $galleryDir . $filename);

        EventImage::create([
            'event_id' => $eventId,
            'image' => $filename,
            'format' => $format,
        ]);
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
