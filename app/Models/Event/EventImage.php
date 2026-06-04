<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'image',
        'format',
        'generation_method',
        'source_image_hash',
        'validation_ssim_score',
    ];

    protected $casts = [
        'validation_ssim_score' => 'float',
    ];
}
