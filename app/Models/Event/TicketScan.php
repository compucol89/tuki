<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketScan extends Model
{
  use HasFactory;

  protected $fillable = [
    'booking_id',
    'event_id',
    'organizer_id',
    'unique_id',
    'scanner_type',
    'scanner_id',
    'ip',
    'user_agent',
    'scanned_at',
  ];

  protected $casts = [
    'scanned_at' => 'datetime',
  ];

  public function booking()
  {
    return $this->belongsTo(Booking::class);
  }
}
