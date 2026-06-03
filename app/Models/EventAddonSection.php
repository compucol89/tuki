<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Organizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAddonSection extends Model
{
  use HasFactory;

  protected $table = 'event_addon_sections';

  protected $fillable = [
    'event_id',
    'organizer_id',
    'title',
    'slug',
    'description',
    'sort_order',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
    'sort_order' => 'integer',
  ];

  public function event()
  {
    return $this->belongsTo(Event::class);
  }

  public function organizer()
  {
    return $this->belongsTo(Organizer::class);
  }

  public function addons()
  {
    return $this->hasMany(EventAddon::class);
  }
}
