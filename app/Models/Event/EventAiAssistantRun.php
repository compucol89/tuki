<?php

namespace App\Models\Event;

use App\Models\Event;
use App\Models\Organizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventAiAssistantRun extends Model
{
  use HasFactory;

  protected $fillable = [
    'event_id',
    'organizer_id',
    'type',
    'status',
    'model',
    'prompt_version',
    'source_image_path',
    'source_image_hash',
    'input_payload',
    'output_payload',
    'moderation_payload',
    'audit_payload',
    'duration_ms',
    'error_message',
  ];

  protected $casts = [
    'input_payload' => 'array',
    'output_payload' => 'array',
    'moderation_payload' => 'array',
    'audit_payload' => 'array',
    'duration_ms' => 'integer',
  ];

  public function event(): BelongsTo
  {
    return $this->belongsTo(Event::class);
  }

  public function organizer(): BelongsTo
  {
    return $this->belongsTo(Organizer::class);
  }

  public function reviews(): HasMany
  {
    return $this->hasMany(EventAiAssistantReview::class, 'run_id');
  }

  public function markRunning(): void
  {
    $this->update(['status' => 'running']);
  }

  public function markCompleted(array $outputPayload, int $durationMs, ?array $auditPayload = null): void
  {
    $this->update([
      'status' => 'completed',
      'output_payload' => $outputPayload,
      'audit_payload' => $auditPayload,
      'duration_ms' => $durationMs,
      'error_message' => null,
    ]);
  }

  public function markFailed(string $message): void
  {
    $this->update([
      'status' => 'failed',
      'error_message' => mb_substr($message, 0, 2000),
    ]);
  }
}
