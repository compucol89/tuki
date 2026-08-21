<?php

namespace App\Models\HomePage;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
  use HasFactory;

  protected $table = 'testimonials';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'language_id',
    'image',
    'name',
    'occupation',
    'comment',
    'serial_number',
    'rating',
    'published',
    'verified',
    'consent',
    'verified_at',
    'verified_by',
    'source',
    'original_text',
  ];

  protected $casts = [
    'published' => 'boolean',
    'verified' => 'boolean',
    'consent' => 'boolean',
    'verified_at' => 'datetime',
  ];

  public function scopePubliclyVisible($query)
  {
    return $query
      ->where('published', true)
      ->where('verified', true)
      ->where('consent', true);
  }

  public function language()
  {
    return $this->belongsTo(Language::class, 'language_id', 'id');
  }
}
