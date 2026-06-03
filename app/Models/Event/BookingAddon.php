<?php

namespace App\Models\Event;

use App\Models\Event;
use App\Models\EventAddon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingAddon extends Model
{
  use HasFactory;

  protected $fillable = [
    'booking_id',
    'event_id',
    'event_addon_id',
    'title',
    'description',
    'unit_price',
    'quantity',
    'subtotal',
    'requires_age_verification',
    'redeemable_only_at_event',
    'non_refundable',
    'redeemed',
    'redeemed_at',
  ];

  protected $casts = [
    'unit_price' => 'decimal:2',
    'subtotal' => 'decimal:2',
    'quantity' => 'integer',
    'requires_age_verification' => 'boolean',
    'redeemable_only_at_event' => 'boolean',
    'non_refundable' => 'boolean',
    'redeemed' => 'boolean',
    'redeemed_at' => 'datetime',
  ];

  public function booking()
  {
    return $this->belongsTo(Booking::class);
  }

  public function event()
  {
    return $this->belongsTo(Event::class);
  }

  public function addon()
  {
    return $this->belongsTo(EventAddon::class, 'event_addon_id');
  }
}
