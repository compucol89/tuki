<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ArcaInvoiceIssuingJob;
use App\Models\Arca\ArcaInvoice;
use App\Models\BillingSetting;
use App\Models\Event\Booking;
use App\Services\Arca\ArcaInvoiceIssuer;
use App\Services\Arca\WsfeClient;
use App\Services\Billing\BookingFiscalCalculator;
use App\Services\Billing\CommissionInvoiceBuilder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ArcaInvoiceIssuingJobIdempotencyTest extends TestCase
{
    private int $bookingId;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        config([
            'arca.enable_issuing'   => true,
            'arca.environment'      => 'homologation',
            'arca.tipo_comprobante' => 6,
            'arca.invoice_model'    => 'customer_service_fee_invoice',
            'arca.concepto'         => 2,
            'arca.punto_venta'      => 1,
        ]);

        $this->setUpTables();

        BillingSetting::query()->delete();
        BillingSetting::create([
            'enabled'                 => true,
            'environment'             => 'homologation',
            'issuer_cuit'             => null,
            'issuer_iva_condition'    => null,
            'point_of_sale'           => null,
            'service_fee_percentage'  => 10,
            'service_fee_tax_mode'    => 'no_vat_added',
            'vat_percentage'          => 0,
            'default_invoice_type'    => null,
            'send_arca_invoice_email' => false,
        ]);

        $this->bookingId = $this->insertBooking('completed');
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('arca_invoice_items');
        Schema::dropIfExists('arca_invoices');
        Schema::dropIfExists('billing_settings');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('events');

        parent::tearDown();
    }

    // -----------------------------------------------------------------------
    // Test 1 — Idempotencia: factura ya aprobada → job no re-emite
    // -----------------------------------------------------------------------
    public function test_job_skips_when_invoice_already_approved(): void
    {
        $this->insertInvoice([
            'booking_id' => $this->bookingId,
            'status'     => 'approved',
            'cae'        => 'ORIGINAL_CAE_000',
            'cbte_nro'   => 42,
        ]);

        $wsfe = $this->makeWsfe();
        $wsfe->method('getLastComprobante')->willReturn(42);
        $wsfe->expects($this->never())->method('recuperarSiYaEmitido');

        $issuer = $this->createMock(ArcaInvoiceIssuer::class);
        $issuer->expects($this->never())->method('issue');

        [$calculator, $builder] = $this->makeCalculatorAndBuilder();

        $this->runJob($calculator, $builder, $issuer, $wsfe);

        $invoice = ArcaInvoice::where('booking_id', $this->bookingId)->first();
        $this->assertSame('approved', $invoice->status);
        $this->assertSame('ORIGINAL_CAE_000', $invoice->cae);
    }

    // -----------------------------------------------------------------------
    // Test 2 — Pago pendiente → job bloquea, nunca llama ARCA
    // -----------------------------------------------------------------------
    public function test_job_blocks_when_booking_not_paid(): void
    {
        $this->updateBookingStatus('pending');

        $wsfe = $this->makeWsfe();
        $wsfe->expects($this->never())->method('getLastComprobante');
        $wsfe->expects($this->never())->method('recuperarSiYaEmitido');

        $issuer = $this->createMock(ArcaInvoiceIssuer::class);
        $issuer->expects($this->never())->method('issue');

        $calculator = $this->createMock(BookingFiscalCalculator::class);
        $calculator->expects($this->never())->method('calculate');

        $builder = $this->createMock(CommissionInvoiceBuilder::class);
        $builder->expects($this->never())->method('buildPreview');

        $this->runJob($calculator, $builder, $issuer, $wsfe);

        $invoice = ArcaInvoice::where('booking_id', $this->bookingId)->first();
        $this->assertNotNull($invoice);
        $this->assertSame('blocked', $invoice->status);
    }

    // -----------------------------------------------------------------------
    // Test 3 — Recuperación: cbte_nro reservado → usa recuperarSiYaEmitido,
    //           NO llama autorizarComprobante
    // -----------------------------------------------------------------------
    public function test_job_recovers_cae_when_cbte_nro_was_reserved(): void
    {
        $this->insertInvoice([
            'booking_id' => $this->bookingId,
            'status'     => 'issuing',
            'cbte_nro'   => 42,
        ]);

        $recovered = $this->arcaResponse('RECOVERED_CAE_042', 42, recovered: true);

        $wsfe = $this->makeWsfe();
        $wsfe->method('getLastComprobante')->willReturn(41);
        $wsfe->expects($this->once())
             ->method('recuperarSiYaEmitido')
             ->with(6, 42)
             ->willReturn($recovered);

        $issuer = $this->createMock(ArcaInvoiceIssuer::class);
        $issuer->expects($this->never())->method('issue');

        [$calculator, $builder] = $this->makeCalculatorAndBuilder();

        $this->runJob($calculator, $builder, $issuer, $wsfe);

        $invoice = ArcaInvoice::where('booking_id', $this->bookingId)->first();
        $this->assertSame('approved', $invoice->status);
        $this->assertSame('RECOVERED_CAE_042', $invoice->cae);
        $this->assertSame(42, (int) $invoice->cbte_nro);
    }

    // -----------------------------------------------------------------------
    // Test 4 — Flujo normal: reserva cbte_nro como 'issuing', emite, persiste
    // -----------------------------------------------------------------------
    public function test_job_issues_and_persists_cae_in_normal_flow(): void
    {
        $wsfe = $this->makeWsfe();
        $wsfe->method('getLastComprobante')->willReturn(41);
        $wsfe->method('recuperarSiYaEmitido')->willReturn(null);

        $issuer = $this->createMock(ArcaInvoiceIssuer::class);
        $issuer->expects($this->once())
               ->method('issue')
               ->willReturn($this->arcaResponse('NEW_CAE_00042', 42));

        [$calculator, $builder] = $this->makeCalculatorAndBuilder();

        $this->runJob($calculator, $builder, $issuer, $wsfe);

        $invoice = ArcaInvoice::where('booking_id', $this->bookingId)->first();
        $this->assertSame('approved', $invoice->status);
        $this->assertSame('NEW_CAE_00042', $invoice->cae);
        $this->assertSame(42, (int) $invoice->cbte_nro);
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function makeWsfe(): WsfeClient
    {
        return $this->getMockBuilder(WsfeClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLastComprobante', 'recuperarSiYaEmitido'])
            ->getMock();
    }

    private function makeCalculatorAndBuilder(): array
    {
        $preview = new ArcaInvoice();
        $preview->forceFill([
            'booking_id'    => $this->bookingId,
            'status'        => 'ready',
            'total_amount'  => 1210.0,
            'net_amount'    => 1000.0,
            'vat_amount'    => 210.0,
            'environment'   => 'homologation',
            'invoice_model' => 'customer_service_fee_invoice',
            'currency'      => 'ARS',
            'organizer_id'  => null,
            'customer_id'   => null,
        ]);

        $calculator = $this->createMock(BookingFiscalCalculator::class);
        $calculator->method('calculate')->willReturn(['preview' => true]);

        $builder = $this->createMock(CommissionInvoiceBuilder::class);
        $builder->method('buildPreview')->willReturn($preview);

        return [$calculator, $builder];
    }

    private function arcaResponse(string $cae, int $cbteNro, bool $recovered = false): array
    {
        return [
            'cae'             => $cae,
            'cae_vencimiento' => '20260520',
            'resultado'       => 'A',
            'cbte_tipo'       => 6,
            'cbte_desde'      => $cbteNro,
            'cbte_hasta'      => $cbteNro,
            'cbte_nro'        => $cbteNro,
            'punto_venta'     => 1,
            'observaciones'   => [],
            'recovered'       => $recovered,
        ];
    }

    private function runJob(
        BookingFiscalCalculator $calculator,
        CommissionInvoiceBuilder $builder,
        ArcaInvoiceIssuer $issuer,
        WsfeClient $wsfe,
    ): void {
        (new ArcaInvoiceIssuingJob($this->bookingId))
            ->handle($calculator, $builder, $issuer, $wsfe);
    }

    private function insertBooking(string $paymentStatus): int
    {
        $booking = new Booking();
        $booking->forceFill([
            'event_id'               => 0,
            'booking_id'             => 'TEST-' . uniqid(),
            'paymentStatus'          => $paymentStatus,
            'price'                  => 1000,
            'commission'             => 100,
            'tax'                    => 0,
            'quantity'               => 1,
            'email'                  => 'test@example.test',
            'fname'                  => 'Test',
            'lname'                  => 'User',
        ]);
        $booking->save();

        return $booking->id;
    }

    private function insertInvoice(array $attributes): void
    {
        $invoice = new ArcaInvoice();
        $invoice->forceFill(array_merge([
            'environment'   => 'homologation',
            'invoice_model' => 'customer_service_fee_invoice',
            'currency'      => 'ARS',
        ], $attributes));
        $invoice->save();
    }

    private function updateBookingStatus(string $status): void
    {
        Booking::where('id', $this->bookingId)->update(['paymentStatus' => $status]);
    }

    private function setUpTables(): void
    {
        if (! Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();
            });
        }

        // bookings: no tiene migración propia, se crea el schema mínimo necesario
        if (! Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('event_id')->default(0);
                $table->string('booking_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->unsignedBigInteger('organizer_id')->nullable();
                $table->string('paymentStatus')->default('pending');
                $table->string('email')->nullable();
                $table->string('fname')->nullable();
                $table->string('lname')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->decimal('tax', 10, 2)->default(0);
                $table->decimal('commission', 10, 2)->default(0);
                $table->integer('quantity')->default(1);
                $table->timestamps();
            });
        }

        foreach ([
            'database/migrations/2026_04_28_000001_create_arca_invoices_table.php',
            'database/migrations/2026_04_28_000002_create_arca_invoice_items_table.php',
            'database/migrations/2026_05_02_071759_add_unique_booking_id_to_arca_invoices.php',
            'database/migrations/2026_05_02_090359_create_billing_settings_table.php',
            'database/migrations/2026_05_09_000002_add_send_arca_invoice_email_to_billing_settings_table.php',
        ] as $path) {
            if (! $this->migrationAlreadyRan($path)) {
                $this->artisan('migrate', ['--path' => $path, '--force' => true]);
            }
        }
    }

    private function migrationAlreadyRan(string $path): bool
    {
        $name = pathinfo($path, PATHINFO_FILENAME);

        try {
            return \DB::table('migrations')->where('migration', $name)->exists();
        } catch (\Throwable) {
            return false;
        }
    }
}
