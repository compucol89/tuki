<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateAiImageJob;
use App\Models\Event;
use App\Models\Event\EventAiGeneration;
use App\Models\Event\EventImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class EventAiImageController extends Controller
{
    public function generate(Request $request, Event $event): JsonResponse
    {
        if (!$this->canManageEvent($event)) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        if (!config('features.ai_images_enabled', false)) {
            return response()->json([
                'error' => 'ai_images_disabled',
                'message' => 'La generación de imágenes IA no está habilitada.',
            ], 503);
        }

        if (empty(config('openai.api_key'))) {
            return response()->json([
                'error' => 'openai_not_configured',
                'message' => 'OPENAI_API_KEY no está configurado.',
            ], 503);
        }

        if (empty($event->thumbnail)) {
            return response()->json([
                'error' => 'thumbnail_required',
                'message' => 'Subí una imagen de portada primero.',
            ], 422);
        }

        if (empty($event->organizer_id)) {
            return response()->json([
                'error' => 'organizer_required',
                'message' => 'Asigná un organizador al evento antes de generar imágenes IA.',
            ], 422);
        }

        $active = EventAiGeneration::where('event_id', $event->id)
            ->whereIn('status', ['pending', 'running'])
            ->count();

        if ($active > 0) {
            return response()->json([
                'error' => 'generation_in_progress',
                'message' => 'Ya hay una generación de imágenes en curso para este evento.',
            ], 409);
        }

        $allFormats = ['square', 'gallery', 'og'];

        $jobs = [];
        foreach ($allFormats as $format) {
            $generation = EventAiGeneration::create([
                'event_id' => $event->id,
                'organizer_id' => $event->organizer_id,
                'format' => $format,
                'status' => 'pending',
            ]);

            $jobs[] = new GenerateAiImageJob($generation->id);
        }

        $batch = Bus::batch($jobs)
            ->name("ai-images-{$event->id}")
            ->dispatch();

        return response()->json([
            'status' => 'dispatched',
            'batch_id' => $batch->id,
            'formats' => $allFormats,
        ]);
    }

    public function status(Request $request, Event $event): JsonResponse
    {
        if (!$this->canManageEvent($event)) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $generations = EventAiGeneration::where('event_id', $event->id)
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('format')
            ->map(fn($g) => $g->first());

        $formats = [];
        $completed = 0;
        $failed = 0;

        foreach (['square', 'gallery', 'og'] as $format) {
            $g = $generations->get($format);
            if (!$g) {
                $formats[$format] = [
                    'status' => 'not_started',
                    'label' => $this->formatLabel($format),
                    'description' => $this->formatDescription($format),
                    'progress' => 0,
                    'can_apply' => false,
                ];
                continue;
            }
            $payload = [
                'id' => $g->id,
                'status' => $g->status,
                'label' => $this->formatLabel($format),
                'description' => $this->formatDescription($format),
                'progress' => $this->statusProgress($g->status),
                'can_apply' => $g->status === 'completed' && !empty($g->output_path),
            ];
            if ($g->status === 'completed' && $g->output_path) {
                $payload['url'] = asset($g->output_path);
                $completed++;
            } elseif ($g->status === 'failed') {
                $failed++;
                $payload['error'] = $g->error_message;
            }
            $formats[$format] = $payload;
        }

        return response()->json([
            'total' => 3,
            'completed' => $completed,
            'failed' => $failed,
            'formats' => $formats,
        ]);
    }

    public function apply(Request $request, Event $event): JsonResponse
    {
        if (!$this->canManageEvent($event)) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $ids = collect($request->input('generation_ids', []))
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return response()->json([
                'error' => 'empty_selection',
                'message' => 'Seleccioná al menos una imagen generada para aplicar.',
            ], 422);
        }

        $generations = EventAiGeneration::where('event_id', $event->id)
            ->whereIn('id', $ids)
            ->where('status', 'completed')
            ->get();

        if ($generations->isEmpty()) {
            return response()->json([
                'error' => 'nothing_to_apply',
                'message' => 'No hay imágenes completadas para aplicar.',
            ], 422);
        }

        DB::transaction(function () use ($event, $generations) {
            foreach ($generations as $generation) {
                if (empty($generation->output_path)) {
                    continue;
                }

                $this->applyGenerationToEvent($event, $generation);
            }
        });

        return response()->json([
            'status' => 'applied',
            'applied' => $generations->count(),
        ]);
    }

    public function regenerate(Request $request, Event $event, string $format): JsonResponse
    {
        if (!$this->canManageEvent($event)) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        if (!in_array($format, ['square', 'gallery', 'og'], true)) {
            return response()->json(['error' => 'invalid_format'], 422);
        }

        if (!config('features.ai_images_enabled', false)) {
            return response()->json(['error' => 'ai_images_disabled'], 503);
        }

        if (empty(config('openai.api_key'))) {
            return response()->json([
                'error' => 'openai_not_configured',
                'message' => 'OPENAI_API_KEY no está configurado.',
            ], 503);
        }

        if (empty($event->organizer_id)) {
            return response()->json([
                'error' => 'organizer_required',
                'message' => 'Asigná un organizador al evento antes de generar imágenes IA.',
            ], 422);
        }

        $active = EventAiGeneration::where('event_id', $event->id)
            ->where('format', $format)
            ->whereIn('status', ['pending', 'running'])
            ->exists();

        if ($active) {
            return response()->json([
                'error' => 'generation_in_progress',
                'message' => 'Ese formato ya se está generando.',
            ], 409);
        }

        $generation = EventAiGeneration::create([
            'event_id' => $event->id,
            'organizer_id' => $event->organizer_id,
            'format' => $format,
            'status' => 'pending',
        ]);

        GenerateAiImageJob::dispatch($generation->id);

        return response()->json([
            'status' => 'dispatched',
            'format' => $format,
            'generation_id' => $generation->id,
        ]);
    }

    public function retry(Request $request, Event $event, string $format): JsonResponse
    {
        if (!$this->canManageEvent($event)) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        if (!in_array($format, ['square', 'gallery', 'og'], true)) {
            return response()->json(['error' => 'invalid_format'], 422);
        }

        if (!config('features.ai_images_enabled', false)) {
            return response()->json(['error' => 'ai_images_disabled'], 503);
        }

        $generation = EventAiGeneration::where('event_id', $event->id)
            ->where('format', $format)
            ->where('status', 'failed')
            ->orderBy('id', 'desc')
            ->first();

        if (!$generation) {
            return response()->json([
                'error' => 'no_failed_generation',
                'message' => 'No hay generación fallida para reintentar.',
            ], 404);
        }

        $generation->update(['status' => 'pending', 'error_message' => null]);
        GenerateAiImageJob::dispatch($generation->id);

        return response()->json(['status' => 'dispatched', 'format' => $format]);
    }

    private function canManageEvent(Event $event): bool
    {
        if (auth('admin')->check()) {
            return true;
        }

        $organizer = auth('organizer')->user();

        return $organizer && (int) $event->organizer_id === (int) $organizer->id;
    }

    private function applyGenerationToEvent(Event $event, EventAiGeneration $generation): void
    {
        match ($generation->format) {
            'square', 'gallery' => $this->replaceEventImage($event->id, $generation->format, $generation->output_path),
            'og' => $event->update(['og_image' => basename($generation->output_path)]),
            default => null,
        };
    }

    private function replaceEventImage(int $eventId, string $format, string $outputPath): void
    {
        $source = public_path($outputPath);
        if (!is_file($source)) {
            return;
        }

        $galleryDir = public_path('assets/admin/img/event-gallery/');
        File::ensureDirectoryExists($galleryDir);

        $existing = EventImage::where('event_id', $eventId)->where('format', $format)->get();
        foreach ($existing as $image) {
            @unlink($galleryDir . $image->image);
            $image->delete();
        }

        $filename = 'ai_' . $format . '_' . $eventId . '_' . uniqid() . '.png';
        File::copy($source, $galleryDir . $filename);

        EventImage::create([
            'event_id' => $eventId,
            'image' => $filename,
            'format' => $format,
        ]);
    }

    private function formatLabel(string $format): string
    {
        return [
            'square' => 'Cover / home',
            'gallery' => 'Galería',
            'og' => 'Redes sociales',
        ][$format] ?? $format;
    }

    private function formatDescription(string $format): string
    {
        return [
            'square' => 'Imagen cuadrada para tarjetas del evento.',
            'gallery' => 'Imagen horizontal para galería y vista del evento.',
            'og' => 'Preview para WhatsApp, Facebook y redes.',
        ][$format] ?? '';
    }

    private function statusProgress(string $status): int
    {
        return [
            'pending' => 12,
            'running' => 68,
            'completed' => 100,
            'failed' => 100,
        ][$status] ?? 0;
    }
}
