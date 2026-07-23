<?php

namespace App\Models\Event;

use App\Models\Event;
use App\Models\Organizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAiContentDraft extends Model
{
  use HasFactory;

  protected $fillable = [
    'review_id',
    'run_id',
    'event_id',
    'organizer_id',
    'status',
    'generated_payload',
    'audit_payload',
    'audit_status',
    'needs_human_review',
    'applied_at',
  ];

  protected $casts = [
    'generated_payload' => 'array',
    'audit_payload' => 'array',
    'needs_human_review' => 'boolean',
    'applied_at' => 'datetime',
  ];

  public function review(): BelongsTo
  {
    return $this->belongsTo(EventAiAssistantReview::class, 'review_id');
  }

  public function run(): BelongsTo
  {
    return $this->belongsTo(EventAiAssistantRun::class, 'run_id');
  }

  public function event(): BelongsTo
  {
    return $this->belongsTo(Event::class);
  }

  public function organizer(): BelongsTo
  {
    return $this->belongsTo(Organizer::class);
  }
}
