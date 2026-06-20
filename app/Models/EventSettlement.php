<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSettlement extends Model
{
  protected $fillable = [
    'event_id',
    'organizer_id',
    'admin_id',
    'amount_option',
    'paid_amount',
    'covered_organizer_amount',
    'balance_debited_amount',
    'charged_amount_snapshot',
    'organizer_net_amount_snapshot',
    'platform_amount_snapshot',
    'paid_bookings_count',
    'paid_at',
    'reference',
    'note',
  ];

  protected $casts = [
    'paid_amount' => 'float',
    'covered_organizer_amount' => 'float',
    'balance_debited_amount' => 'float',
    'charged_amount_snapshot' => 'float',
    'organizer_net_amount_snapshot' => 'float',
    'platform_amount_snapshot' => 'float',
    'paid_at' => 'date',
  ];

  public function event()
  {
    return $this->belongsTo(Event::class);
  }

  public function organizer()
  {
    return $this->belongsTo(Organizer::class);
  }

  public function admin()
  {
    return $this->belongsTo(Admin::class);
  }
}
