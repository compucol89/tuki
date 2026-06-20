<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizerTelegramAccount extends Model
{
  use HasFactory;

  protected $fillable = [
    'organizer_id',
    'telegram_user_id',
    'telegram_chat_id',
    'username',
    'first_name',
    'last_name',
    'is_active',
    'linked_at',
  ];

  protected $casts = [
    'is_active' => 'boolean',
    'linked_at' => 'datetime',
  ];

  public function organizer()
  {
    return $this->belongsTo(Organizer::class);
  }
}
