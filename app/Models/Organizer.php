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
    'cover_photo',
    'email',
    'phone',
    'username',
    'password',
    'facebook',
    'twitter',
    'linkedin',
    'website',
    'instagram',
    'tiktok',
    'meta_pixel_id',
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

  //events del organizador
  public function events()
  {
    return $this->hasMany(Event::class);
  }

  protected static function booted()
  {
    static::saved(fn () => \App\Services\PublicBusinessMetricsService::forgetCache());
    static::deleted(fn () => \App\Services\PublicBusinessMetricsService::forgetCache());
  }

  /**
   * Organizadores elegibles para el directorio público (/organizadores):
   * perfil completo (foto, portada, nombre, descripción >= 80, ubicación,
   * redes, email verificado) y correo de operación válido.
   * Decisión 2026-08-21 (F-005): ya NO se exige evento publicado y realizado.
   */
  public function scopeListable($query)
  {
    return $query->whereNotNull('photo')
      ->whereNotNull('cover_photo')
      ->whereNotNull('email_verified_at')
      ->where(function ($email) {
        $email->where('email', 'not like', '%@test.%')
          ->where('email', 'not like', '%@example.%');
      })
      ->where(function ($social) {
        $social->whereNotNull('website')
          ->orWhereNotNull('instagram')
          ->orWhereNotNull('tiktok')
          ->orWhereNotNull('facebook')
          ->orWhereNotNull('twitter')
          ->orWhereNotNull('linkedin');
      })
      ->whereHas('organizer_info', function ($info) {
        $info->whereNotNull('name')
          ->whereRaw("organizer_infos.name != organizers.username")
          ->whereRaw("CHAR_LENGTH(COALESCE(details, '')) >= 80")
          ->where(function ($loc) {
            $loc->whereNotNull('city')
              ->orWhereNotNull('state')
              ->orWhereNotNull('country')
              ->orWhereNotNull('address');
          });
      });
  }
}
