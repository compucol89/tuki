<?php

namespace Tests\Unit\Billing;

use App\Services\Billing\CommissionInvoiceBuilder;
use Tests\TestCase;

class CommissionInvoiceBuilderTest extends TestCase
{
    public function test_builds_blocked_invoice_preview_when_fiscal_data_is_missing(): void
    {
        $calculation = [
            'booking_id' => 123,
            'event_id' => 10,
            'organizer_id' => 20,
            'customer_id' => 30,
            'payment_status' => 'completed',
            'ticket_amount' => 100000.0,
            'quantity' => 1.0,
            'organizer_gross_amount' => 100000.0,
            'platform_commission_rate' => 0.10,
            'platform_commission_amount' => 10000.0,
            'buyer_total_estimated' => 110000.0,
            'taxable_amount_for_tukipass' => 10000.0,
            'vat_rate' => 0.0,
            'vat_amount' => 0.0,
            'invoice_total' => 10000.0,
            'warnings' => ['Comisión no persistida; usando default de preview'],
            'blocked_reasons' => ['Falta número de documento del cliente'],
            'recipient' => [
                'name' => null,
                'tax_condition' => null,
                'tax_id' => null,
                'document_type' => null,
                'document_number' => null,
                'address' => null,
            ],
        ];

        $invoice = (new CommissionInvoiceBuilder())->buildPreview($calculation);

        $this->assertSame('blocked', $invoice->status);
        $this->assertSame('customer_service_fee_invoice', $invoice->invoice_model);
        $this->assertSame(10000.0, (float) $invoice->net_amount);
        $this->assertSame(10000.0, (float) $invoice->total_amount);
        $this->assertCount(1, $invoice->items);
        $this->assertSame('Comisión TukiPass por venta de entradas', $invoice->items->first()->description);
    }
}
