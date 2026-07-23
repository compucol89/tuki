<?php

namespace App\Models\Event;

use App\Models\Event;
use App\Models\Organizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventAiAssistantReview extends Model
{
  use HasFactory;

  protected $fillable = [
    'run_id',
    'event_id',
    'organizer_id',
    'canonical_event_facts',
    'accepted_fields',
    'ignored_fields',
    'audience_payload',
    'tone',
    'intensity',
    'status',
    'reviewed_at',
  ];

  protected $casts = [
    'canonical_event_facts' => 'array',
    'accepted_fields' => 'array',
    'ignored_fields' => 'array',
    'audience_payload' => 'array',
    'reviewed_at' => 'datetime',
  ];

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

  public function drafts(): HasMany
  {
    return $this->hasMany(EventAiContentDraft::class, 'review_id');
  }
}
