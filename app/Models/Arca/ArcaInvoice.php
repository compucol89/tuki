<?php

declare(strict_types=1);

namespace App\Models\Arca;

use App\Models\Customer;
use App\Models\Event\Booking;
use App\Models\Organizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArcaInvoice extends Model
{
    protected $fillable = [
        'booking_id',
        'organizer_id',
        'customer_id',
        'environment',
        'status',
        'invoice_model',
        'currency',
        'point_of_sale',
        'cbte_tipo',
        'cbte_nro',
        'concept',
        'doc_tipo',
        'doc_nro',
        'recipient_name',
        'recipient_tax_condition',
        'recipient_tax_id',
        'recipient_address',
        'service_from',
        'service_to',
        'due_date',
        'net_amount',
        'vat_amount',
        'exempt_amount',
        'non_taxed_amount',
        'total_amount',
        'commission_rate',
        'commission_base_amount',
        'commission_amount',
        'cae',
        'cae_due_date',
        'arca_request',
        'arca_response',
        'error_code',
        'error_message',
        'issued_at',
        'created_by_type',
        'created_by_id',
    ];

    protected $casts = [
        'arca_request' => 'array',
        'arca_response' => 'array',
        'issued_at' => 'datetime',
        'cae_due_date' => 'date',
        'service_from' => 'date',
        'service_to' => 'date',
        'due_date' => 'date',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ArcaInvoiceItem::class);
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canBeIssued(): bool
    {
        return $this->status === 'ready' && !$this->isApproved() && empty($this->cae);
    }
}
