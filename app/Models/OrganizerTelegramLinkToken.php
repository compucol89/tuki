<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizerTelegramLinkToken extends Model
{
  use HasFactory;

  protected $fillable = [
    'organizer_id',
    'token_hash',
    'expires_at',
    'used_at',
  ];

  protected $casts = [
    'expires_at' => 'datetime',
    'used_at' => 'datetime',
  ];

  public function organizer()
  {
    return $this->belongsTo(Organizer::class);
  }
}
