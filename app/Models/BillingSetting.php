<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingSetting extends Model
{
  use HasFactory;

  protected $fillable = [
    'enabled',
    'issuer_cuit',
    'issuer_iva_condition',
    'point_of_sale',
    'service_fee_percentage',
    'service_fee_tax_mode',
    'vat_percentage',
    'default_invoice_type',
    'environment',
  ];

  protected $casts = [
    'enabled' => 'boolean',
    'point_of_sale' => 'integer',
    'service_fee_percentage' => 'decimal:4',
    'vat_percentage' => 'decimal:4',
    'default_invoice_type' => 'integer',
  ];

  public static function current(): self
  {
    return self::query()->firstOrCreate([], [
      'enabled' => false,
      'environment' => 'testing',
      'service_fee_tax_mode' => 'no_vat_added',
      'service_fee_percentage' => 0,
      'vat_percentage' => 0,
    ]);
  }
}
