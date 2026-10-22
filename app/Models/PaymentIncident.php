<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentIncident extends Model
{
  use HasFactory;

  protected $fillable = [
    'gateway',
    'payment_id',
    'booking_id',
    'event_id',
    'amount',
    'currency',
    'reason',
    'status',
    'context',
  ];

  protected $casts = [
    'amount' => 'decimal:2',
    'context' => 'array',
  ];
}
