<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateAiImageJob;
use App\Models\Event;
use App\Models\Event\EventAiGeneration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

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

        $existing = EventAiGeneration::where('event_id', $event->id)
            ->where('status', 'completed')
            ->count();

        if ($existing >= 3) {
            return response()->json([
                'error' => 'limit_reached',
                'message' => 'Este evento ya tiene 3 imágenes IA generadas.',
            ], 422);
        }

        $generatedFormats = EventAiGeneration::where('event_id', $event->id)
            ->where('status', 'completed')
            ->pluck('format')
            ->toArray();

        $allFormats = ['square', 'gallery', 'og'];
        $formatsToGenerate = array_values(array_diff($allFormats, $generatedFormats));

        $jobs = [];
        foreach ($formatsToGenerate as $format) {
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
            'formats' => $formatsToGenerate,
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
                $formats[$format] = ['status' => 'pending'];
                continue;
            }
            $payload = ['status' => $g->status];
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
}
