<?php

namespace App\Models;

use App\Models\Event;
use App\Models\ShopManagement\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAddon extends Model
{
  use HasFactory;

  protected $table = 'event_addons';

  protected $fillable = [
    'event_addon_section_id',
    'product_id',
    'event_id',
    'title',
    'description',
    'price',
    'previous_price',
    'image',
    'stock',
    'max_per_order',
    'is_active',
    'requires_age_verification',
    'redeemable_only_at_event',
    'non_refundable',
    'sort_order',
  ];

  protected $casts = [
    'price' => 'decimal:2',
    'previous_price' => 'decimal:2',
    'is_active' => 'boolean',
    'requires_age_verification' => 'boolean',
    'redeemable_only_at_event' => 'boolean',
    'non_refundable' => 'boolean',
    'stock' => 'integer',
    'max_per_order' => 'integer',
    'sort_order' => 'integer',
  ];

  public function section()
  {
    return $this->belongsTo(EventAddonSection::class, 'event_addon_section_id');
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function event()
  {
    return $this->belongsTo(Event::class);
  }

  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }

  public function scopeInStock($query)
  {
    return $query->where(function ($q) {
      $q->whereNull('stock')->orWhere('stock', '>', 0);
    });
  }
}
