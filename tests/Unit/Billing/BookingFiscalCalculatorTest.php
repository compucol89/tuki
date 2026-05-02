<?php

namespace Tests\Unit\Billing;

use App\Models\BillingSetting;
use App\Models\Customer;
use App\Models\CustomerFiscalProfile;
use App\Models\Event;
use App\Models\Event\Booking;
use App\Models\Event\Ticket;
use App\Models\Organizer;
use App\Services\Billing\BookingFiscalCalculator;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookingFiscalCalculatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('billing_settings')) {
            $this->artisan('migrate', [
                '--path' => 'database/migrations/2026_05_02_090359_create_billing_settings_table.php',
                '--force' => true,
            ]);
        }

        BillingSetting::query()->delete();
        BillingSetting::create([
            'enabled' => false,
            'environment' => 'testing',
            'issuer_cuit' => null,
            'issuer_iva_condition' => null,
            'point_of_sale' => null,
            'service_fee_percentage' => 10,
            'service_fee_tax_mode' => 'no_vat_added',
            'vat_percentage' => 0,
            'default_invoice_type' => null,
        ]);
    }

    public function test_calculates_default_ten_percent_commission_preview(): void
    {
        $booking = $this->booking([
            'price' => 100000,
            'quantity' => 1,
            'paymentStatus' => 'completed',
        ]);

        $preview = (new BookingFiscalCalculator())->calculate($booking);

        $this->assertSame(100000.0, $preview['ticket_amount']);
        $this->assertSame(1.0, $preview['quantity']);
        $this->assertSame(100000.0, $preview['organizer_gross_amount']);
        $this->assertSame(0.10, $preview['platform_commission_rate']);
        $this->assertSame(10000.0, $preview['platform_commission_amount']);
        $this->assertSame(110000.0, $preview['buyer_total_estimated']);
        $this->assertContains('Comisión no persistida; usando porcentaje de billing_settings', $preview['warnings']);
        $this->assertSame('no_vat_added', $preview['service_fee_tax_mode_used']);
        $this->assertSame(10.0, $preview['service_fee_percentage_used']);
    }

    public function test_does_not_modify_booking_attributes(): void
    {
        $booking = $this->booking([
            'price' => 100000,
            'quantity' => 1,
            'paymentStatus' => 'completed',
        ]);

        $before = $booking->getAttributes();

        (new BookingFiscalCalculator())->calculate($booking);

        $this->assertSame($before, $booking->getAttributes());
    }

    public function test_blocks_preview_when_booking_is_not_paid(): void
    {
        $preview = (new BookingFiscalCalculator())->calculate($this->booking([
            'price' => 100000,
            'quantity' => 1,
            'paymentStatus' => 'pending',
        ]));

        $this->assertContains('La reserva no está pagada', $preview['blocked_reasons']);
    }

    public function test_blocks_preview_when_customer_fiscal_data_is_missing(): void
    {
        $preview = (new BookingFiscalCalculator())->calculate($this->booking([
            'price' => 100000,
            'quantity' => 1,
            'paymentStatus' => 'completed',
        ]));

        $this->assertContains('Falta tipo de documento del cliente', $preview['blocked_reasons']);
        $this->assertContains('Falta número de documento del cliente', $preview['blocked_reasons']);
        $this->assertContains('Falta condición IVA del cliente', $preview['blocked_reasons']);
    }

    public function test_preview_is_ready_when_customer_fiscal_profile_is_complete(): void
    {
        $booking = $this->booking([
            'price' => 100000,
            'commission' => 10000,
            'commission_percentage' => 10,
            'quantity' => 1,
            'paymentStatus' => 'completed',
        ]);

        $booking->customerInfo->setRelation('fiscalProfile', new CustomerFiscalProfile([
            'full_name' => 'Cliente Demo',
            'document_type' => 'DNI',
            'document_number' => '12345678',
            'iva_condition' => 'consumidor_final',
        ]));

        $preview = (new BookingFiscalCalculator())->calculate($booking);

        $this->assertSame([], $preview['blocked_reasons']);
        $this->assertSame('12345678', $preview['recipient']['document_number']);
        $this->assertSame('Cliente Demo', $preview['recipient']['name']);
    }

    public function test_preview_is_ready_for_guest_booking_with_booking_fiscal_profile(): void
    {
        $booking = $this->booking([
            'price' => 100000,
            'commission' => 10000,
            'commission_percentage' => 10,
            'quantity' => 1,
            'paymentStatus' => 'completed',
            'customer_id' => null,
        ]);

        $booking->setRelation('fiscalProfile', new CustomerFiscalProfile([
            'full_name' => 'Invitado Demo',
            'document_type' => 'DNI',
            'document_number' => '87654321',
            'iva_condition' => 'consumidor_final',
        ]));

        $preview = (new BookingFiscalCalculator())->calculate($booking);

        $this->assertSame([], $preview['blocked_reasons']);
        $this->assertSame('87654321', $preview['recipient']['document_number']);
    }

    public function test_uses_persisted_commission_when_available(): void
    {
        $preview = (new BookingFiscalCalculator())->calculate($this->booking([
            'price' => 100000,
            'commission' => 7500,
            'commission_percentage' => 7.5,
            'quantity' => 1,
            'paymentStatus' => 'completed',
        ]));

        $this->assertSame(0.075, $preview['platform_commission_rate']);
        $this->assertSame(7500.0, $preview['platform_commission_amount']);
        $this->assertNotContains('Comisión no persistida; usando porcentaje de billing_settings', $preview['warnings']);
        $this->assertSame(7.5, $preview['service_fee_percentage_used']);
    }

    public function test_vat_added_mode(): void
    {
        BillingSetting::query()->delete();
        BillingSetting::create([
            'enabled' => false,
            'environment' => 'testing',
            'issuer_cuit' => null,
            'issuer_iva_condition' => null,
            'point_of_sale' => null,
            'service_fee_percentage' => 10,
            'service_fee_tax_mode' => 'vat_added',
            'vat_percentage' => 21,
            'default_invoice_type' => null,
        ]);

        $booking = $this->booking([
            'price' => 100000,
            'quantity' => 1,
            'paymentStatus' => 'completed',
        ]);
        $booking->customerInfo->setRelation('fiscalProfile', new CustomerFiscalProfile([
            'full_name' => 'Cliente Demo',
            'document_type' => 'DNI',
            'document_number' => '12345678',
            'iva_condition' => 'consumidor_final',
        ]));

        $preview = (new BookingFiscalCalculator())->calculate($booking);

        $this->assertSame(10000.0, $preview['platform_commission_amount']);
        $this->assertSame(10000.0, $preview['taxable_amount_for_tukipass']);
        $this->assertSame(2100.0, $preview['vat_amount']);
        $this->assertSame(12100.0, $preview['invoice_total']);
        $this->assertSame(112100.0, $preview['buyer_total_estimated']);
        $this->assertSame('vat_added', $preview['service_fee_tax_mode_used']);
        $this->assertSame(0.21, $preview['vat_rate']);
    }

    public function test_vat_included_mode(): void
    {
        BillingSetting::query()->delete();
        BillingSetting::create([
            'enabled' => false,
            'environment' => 'testing',
            'issuer_cuit' => null,
            'issuer_iva_condition' => null,
            'point_of_sale' => null,
            'service_fee_percentage' => 10,
            'service_fee_tax_mode' => 'vat_included',
            'vat_percentage' => 21,
            'default_invoice_type' => null,
        ]);

        $booking = $this->booking([
            'price' => 100000,
            'quantity' => 1,
            'paymentStatus' => 'completed',
        ]);
        $booking->customerInfo->setRelation('fiscalProfile', new CustomerFiscalProfile([
            'full_name' => 'Cliente Demo',
            'document_type' => 'DNI',
            'document_number' => '12345678',
            'iva_condition' => 'consumidor_final',
        ]));

        $preview = (new BookingFiscalCalculator())->calculate($booking);

        $this->assertSame(10000.0, $preview['platform_commission_amount']);
        $this->assertSame(8264.46, $preview['taxable_amount_for_tukipass']);
        $this->assertSame(1735.54, $preview['vat_amount']);
        $this->assertSame(10000.0, $preview['invoice_total']);
        $this->assertSame(110000.0, $preview['buyer_total_estimated']);
        $this->assertSame('vat_included', $preview['service_fee_tax_mode_used']);
    }

    private function booking(array $attributes): Booking
    {
        $booking = new Booking(array_merge([
            'id' => 123,
            'booking_id' => 'TEST-123',
            'event_id' => 10,
            'organizer_id' => 20,
            'customer_id' => 30,
            'ticket_id' => 40,
        ], $attributes));

        $booking->setRelation('evnt', new Event(['id' => 10, 'organizer_id' => 20]));
        $booking->setRelation('organizer', new Organizer(['id' => 20, 'email' => 'organizer@example.test']));
        $booking->setRelation('customerInfo', new Customer(['id' => 30, 'email' => 'buyer@example.test']));
        $booking->setRelation('ticket', new Ticket(['id' => 40, 'price' => $attributes['price'] ?? 0]));

        return $booking;
    }
}
