<?php

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\ReconcileOnlinePendingBookings;
use App\Models\Event\Booking;
use App\Models\Event\Ticket;
use App\Models\PendingBooking;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReconcileOnlinePendingBookingsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('event_id')->nullable();
                $table->unsignedBigInteger('organizer_id')->nullable();
                $table->text('variation')->nullable();
                $table->decimal('price', 12, 2)->default(0);
                $table->decimal('tax', 12, 2)->default(0);
                $table->decimal('commission', 12, 2)->default(0);
                $table->integer('quantity')->default(0);
                $table->string('paymentMethod')->nullable();
                $table->string('paymentStatus')->nullable();
                $table->timestamp('stock_released_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('booking_addons')) {
            Schema::create('booking_addons', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('booking_id');
                $table->unsignedBigInteger('event_addon_id')->nullable();
                $table->integer('quantity')->default(1);
                $table->boolean('redeemed')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tickets')) {
            Schema::create('tickets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('event_id');
                $table->string('pricing_type')->default('normal');
                $table->string('ticket_available_type')->nullable();
                $table->integer('ticket_available')->default(0);
                $table->text('variations')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pending_bookings')) {
            Schema::create('pending_bookings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('token')->unique();
                $table->unsignedBigInteger('event_id')->nullable();
                $table->text('data')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('status')->default('pending');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_cancels_old_pending_iyzico_booking_and_restores_stock(): void
    {
        $ticket = Ticket::create([
            'event_id' => 1,
            'pricing_type' => 'normal',
            'ticket_available_type' => 'limited',
            'ticket_available' => 10,
        ]);

        $booking = Booking::forceCreate([
            'event_id' => 1,
            'organizer_id' => 20,
            'variation' => json_encode([
                [
                    'ticket_id' => $ticket->id,
                    'name' => 'General',
                    'qty' => 1,
                    'price' => 100,
                    'unique_id' => 'uuid-1',
                ],
                [
                    'ticket_id' => $ticket->id,
                    'name' => 'General',
                    'qty' => 1,
                    'price' => 100,
                    'unique_id' => 'uuid-2',
                ],
            ]),
            'price' => 200,
            'quantity' => 2,
            'paymentMethod' => 'Iyzico',
            'paymentStatus' => 'pending',
            'created_at' => Carbon::now()->subHours(6),
            'updated_at' => Carbon::now()->subHours(6),
        ]);

        $this->artisan('bookings:reconcile-online-pending')->assertSuccessful();

        $this->assertSame('rejected', $booking->fresh()->paymentStatus);
        $this->assertSame(12, $ticket->fresh()->ticket_available);
    }

    public function test_does_not_touch_offline_or_fresh_pending_bookings(): void
    {
        $ticket = Ticket::create([
            'event_id' => 1,
            'pricing_type' => 'normal',
            'ticket_available_type' => 'limited',
            'ticket_available' => 10,
        ]);

        $offline = Booking::forceCreate([
            'event_id' => 1,
            'organizer_id' => 20,
            'variation' => json_encode([['ticket_id' => $ticket->id, 'name' => 'General', 'qty' => 1, 'price' => 100, 'unique_id' => 'u1']]),
            'price' => 100,
            'quantity' => 1,
            'paymentMethod' => null,
            'paymentStatus' => 'pending',
            'created_at' => Carbon::now()->subHours(48),
            'updated_at' => Carbon::now()->subHours(48),
        ]);

        $fresh = Booking::forceCreate([
            'event_id' => 1,
            'organizer_id' => 20,
            'variation' => json_encode([['ticket_id' => $ticket->id, 'name' => 'General', 'qty' => 1, 'price' => 100, 'unique_id' => 'u2']]),
            'price' => 100,
            'quantity' => 1,
            'paymentMethod' => 'Iyzico',
            'paymentStatus' => 'pending',
            'created_at' => Carbon::now()->subHour(),
            'updated_at' => Carbon::now()->subHour(),
        ]);

        $this->artisan('bookings:reconcile-online-pending')->assertSuccessful();

        $this->assertSame('pending', $offline->fresh()->paymentStatus);
        $this->assertSame('pending', $fresh->fresh()->paymentStatus);
        $this->assertSame(10, $ticket->fresh()->ticket_available);
    }

    public function test_marks_expired_pending_bookings_and_retains_pii_for_seven_days(): void
    {
        $pending = PendingBooking::create([
            'token' => 'tok-expired-1',
            'event_id' => 1,
            'data' => json_encode(['fname' => 'Juan', 'dni' => '12345678', 'email' => 'juan@example.test']),
            'amount' => 500,
            'status' => 'pending',
            'expires_at' => Carbon::now()->subHours(3),
        ]);

        $this->artisan('bookings:reconcile-online-pending')->assertSuccessful();

        $pending->refresh();
        $this->assertSame('expired', $pending->status);
        $this->assertNotNull(PendingBooking::find($pending->id));
    }

    public function test_deletes_expired_pending_bookings_after_retention_window(): void
    {
        $pending = PendingBooking::create([
            'token' => 'tok-expired-old',
            'event_id' => 1,
            'data' => json_encode(['fname' => 'Juan']),
            'amount' => 500,
            'status' => 'expired',
            'expires_at' => Carbon::now()->subDays(9),
        ]);
        $pending->updated_at = Carbon::now()->subDays(8);
        $pending->save();

        $this->artisan('bookings:reconcile-online-pending')->assertSuccessful();

        $this->assertNull(PendingBooking::find($pending->id));
    }

    public function test_restores_stock_for_variation_type_tickets(): void
    {
        $ticket = Ticket::create([
            'event_id' => 1,
            'pricing_type' => 'variation',
            'variations' => json_encode([
                ['name' => 'General', 'price' => 100, 'ticket_available_type' => 'limited', 'ticket_available' => 5, 'max_ticket_buy_type' => null, 'v_max_ticket_buy' => null],
                ['name' => 'VIP', 'price' => 200, 'ticket_available_type' => 'limited', 'ticket_available' => 3, 'max_ticket_buy_type' => null, 'v_max_ticket_buy' => null],
            ]),
        ]);

        $booking = Booking::forceCreate([
            'event_id' => 1,
            'organizer_id' => 20,
            'variation' => json_encode([
                ['ticket_id' => $ticket->id, 'name' => 'General', 'qty' => 1, 'price' => 100, 'unique_id' => 'u1'],
                ['ticket_id' => $ticket->id, 'name' => 'General', 'qty' => 1, 'price' => 100, 'unique_id' => 'u2'],
            ]),
            'price' => 200,
            'quantity' => 2,
            'paymentMethod' => 'Iyzico',
            'paymentStatus' => 'pending',
            'created_at' => Carbon::now()->subHours(5),
            'updated_at' => Carbon::now()->subHours(5),
        ]);

        $this->artisan('bookings:reconcile-online-pending')->assertSuccessful();

        $this->assertSame('rejected', $booking->fresh()->paymentStatus);

        $variations = json_decode($ticket->fresh()->variations, true);
        $this->assertSame(7, $variations[0]['ticket_available']);
        $this->assertSame(3, $variations[1]['ticket_available']);
    }
}
