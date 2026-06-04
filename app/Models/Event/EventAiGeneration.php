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
        'validation_ssim_score',
        'error_message',
        'output_path',
    ];

    protected $casts = [
        'duration_ms' => 'integer',
        'cost_estimate' => 'decimal:4',
        'validation_ssim_score' => 'float',
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

    public function markCompleted(int $durationMs, string $outputPath, float $costEstimate, ?float $validationScore = null): void
    {
        $this->update([
            'status' => 'completed',
            'duration_ms' => $durationMs,
            'output_path' => $outputPath,
            'cost_estimate' => $costEstimate,
            'validation_ssim_score' => $validationScore,
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
