<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Event\Booking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerFiscalProfile extends Model
{
    use HasFactory;

    public const DOCUMENT_TYPES = [
        'DNI',
        'CUIT',
        'CUIL',
        'PASAPORTE',
        'LE',
        'LC',
    ];

    public const IVA_CONDITIONS = [
        'consumidor_final',
        'responsable_inscripto',
        'monotributo',
        'exento',
        'no_responsable',
    ];

    protected $fillable = [
        'customer_id',
        'booking_id',
        'full_name',
        'document_type',
        'document_number',
        'iva_condition',
        'fiscal_address',
        'fiscal_email',
    ];

    public static function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', Rule::in(self::DOCUMENT_TYPES)],
            'document_number' => ['required', 'string', 'max:20'],
            'iva_condition' => ['required', Rule::in(self::IVA_CONDITIONS)],
            'fiscal_address' => ['nullable', 'string', 'max:255'],
            'fiscal_email' => ['nullable', 'email', 'max:255'],
        ];
    }

    public static function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $validator = Validator::make($data, self::rules());

        $validator->sometimes('document_number', ['digits:11'], function ($input): bool {
            return in_array($input->document_type ?? null, ['CUIT', 'CUIL'], true);
        });

        return $validator;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
