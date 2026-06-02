<?php

namespace App\Models;

use App\Models\Event\Booking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Organizer extends Model implements AuthenticatableContract
{
  use HasFactory, Authenticatable;
  protected $fillable = [
    'photo',
    'email',
    'phone',
    'username',
    'password',
    'facebook',
    'twitter',
    'linkedin',
    'email_verification_token',
    'email_verification_sent_at'
  ];

  protected $hidden = ['password', 'remember_token', 'email_verification_token'];

  protected $casts = [
    'email_verified_at' => 'datetime',
    'email_verification_sent_at' => 'datetime',
  ];

  //withdraw
  public function withdraws()
  {
    return $this->hasMany(Withdraw::class);
  }

  //organizer info
  public function organizer_info()
  {
    return $this->hasOne(OrganizerInfo::class);
  }
}
