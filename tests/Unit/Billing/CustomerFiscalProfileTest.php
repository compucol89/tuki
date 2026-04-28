<?php

namespace Tests\Unit\Billing;

use App\Models\CustomerFiscalProfile;
use Tests\TestCase;

class CustomerFiscalProfileTest extends TestCase
{
    public function test_accepts_valid_customer_fiscal_profile_data(): void
    {
        $validator = CustomerFiscalProfile::validator([
            'full_name' => 'Cliente Demo',
            'document_type' => 'DNI',
            'document_number' => '12345678',
            'iva_condition' => 'consumidor_final',
            'fiscal_address' => 'Av. Demo 123, CABA',
            'fiscal_email' => 'cliente@example.com',
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_rejects_invalid_document_type(): void
    {
        $validator = CustomerFiscalProfile::validator([
            'full_name' => 'Cliente Demo',
            'document_type' => 'OTRO',
            'document_number' => '12345678',
            'iva_condition' => 'consumidor_final',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('document_type', $validator->errors()->toArray());
    }

    public function test_requires_eleven_digits_for_cuit_or_cuil(): void
    {
        $validator = CustomerFiscalProfile::validator([
            'full_name' => 'Empresa Demo SRL',
            'document_type' => 'CUIT',
            'document_number' => '30-70000000-1',
            'iva_condition' => 'responsable_inscripto',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('document_number', $validator->errors()->toArray());
    }
}
