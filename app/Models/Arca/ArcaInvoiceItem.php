<?php

declare(strict_types=1);

namespace App\Models\Arca;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArcaInvoiceItem extends Model
{
    protected $fillable = [
        'arca_invoice_id',
        'description',
        'quantity',
        'unit_price',
        'net_amount',
        'vat_rate',
        'vat_amount',
        'total_amount',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ArcaInvoice::class, 'arca_invoice_id');
    }
}
