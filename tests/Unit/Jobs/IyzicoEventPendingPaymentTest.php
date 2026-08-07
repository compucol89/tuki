<?php

namespace Tests\Unit\Jobs;

use App\Jobs\IyzicoEventPendingPayment;
use App\Models\Earning;
use App\Models\Event\Booking;
use App\Services\OrganizerBalanceService;
use App\Services\TransactionService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class IyzicoEventPendingPaymentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('event_id')->nullable();
                $table->unsignedBigInteger('organizer_id')->nullable();
                $table->decimal('price', 12, 2)->default(0);
                $table->decimal('tax', 12, 2)->default(0);
                $table->decimal('commission', 12, 2)->default(0);
                $table->integer('quantity')->default(1);
                $table->string('paymentMethod')->nullable();
                $table->string('paymentStatus')->nullable();
                $table->string('conversation_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('earnings')) {
            Schema::create('earnings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->decimal('total_revenue', 12, 2)->default(0);
                $table->decimal('total_earning', 12, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    private function setUpFakes(): void
    {
        Queue::fake();
        $this->mock(TransactionService::class)->shouldIgnoreMissing();
        $this->mock(OrganizerBalanceService::class)->shouldIgnoreMissing();
        Artisan::shouldReceive('call')->andReturn(0);
    }

    public function test_credit_completed_booking_is_idempotent(): void
    {
        $this->setUpFakes();

        $earning = Earning::create(['total_revenue' => 0, 'total_earning' => 0]);

        $booking = Booking::forceCreate([
            'event_id' => 1,
            'organizer_id' => 20,
            'price' => 100,
            'tax' => 10,
            'commission' => 5,
            'quantity' => 1,
            'paymentMethod' => 'Iyzico',
            'paymentStatus' => 'completed',
        ]);

        $job = new IyzicoEventPendingPayment($booking->id);

        // Un retry del job sobre un booking ya acreditado debe ser no-op
        $this->assertFalse($job->creditCompletedBooking($booking->fresh()));
        $this->assertSame('completed', $booking->fresh()->paymentStatus);
        $this->assertSame(0.0, (float) $earning->fresh()->total_revenue);
    }

    public function test_credit_completed_booking_claims_once_and_marks_completed(): void
    {
        $this->setUpFakes();

        Earning::create(['total_revenue' => 0, 'total_earning' => 0]);

        $booking = Booking::forceCreate([
            'event_id' => 1,
            'organizer_id' => 20,
            'price' => 100,
            'tax' => 10,
            'commission' => 5,
            'quantity' => 1,
            'paymentMethod' => 'Iyzico',
            'paymentStatus' => 'pending',
        ]);

        $job = new IyzicoEventPendingPayment($booking->id);

        $this->assertTrue($job->creditCompletedBooking($booking->fresh()));
        $this->assertSame('completed', $booking->fresh()->paymentStatus);

        // Segundo intento (retry): no-op
        $this->assertFalse($job->creditCompletedBooking($booking->fresh()));
    }
}
