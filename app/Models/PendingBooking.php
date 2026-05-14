<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'event_id',
        'data',
        'amount',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'data' => 'array',
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
}