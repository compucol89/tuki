<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'name',
        'price',
        'stock',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
