<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAiGeneration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'organizer_id',
        'format',
        'status',
        'model',
        'prompt',
        'duration_ms',
        'cost_estimate',
        'error_message',
        'output_path',
    ];

    protected $casts = [
        'duration_ms' => 'integer',
        'cost_estimate' => 'decimal:4',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Event::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Organizer::class);
    }

    public function markRunning(): void
    {
        $this->update(['status' => 'running']);
    }

    public function markCompleted(int $durationMs, string $outputPath, float $costEstimate): void
    {
        $this->update([
            'status' => 'completed',
            'duration_ms' => $durationMs,
            'output_path' => $outputPath,
            'cost_estimate' => $costEstimate,
        ]);
    }

    public function markFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => substr($errorMessage, 0, 1000),
        ]);
    }
}
