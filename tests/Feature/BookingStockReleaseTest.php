<?php

namespace Tests\Feature;

use App\Models\Event\Booking;
use App\Models\Event\Ticket;
use App\Services\BookingStockService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookingStockReleaseTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    Schema::create('events', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('organizer_id')->nullable();
      $table->string('event_type')->nullable();
      $table->timestamps();
    });

    Schema::create('tickets', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('event_id')->nullable();
      $table->string('title')->nullable();
      $table->string('pricing_type')->nullable();
      $table->string('ticket_available_type')->nullable();
      $table->integer('ticket_available')->nullable();
      $table->text('variations')->nullable();
      $table->timestamps();
    });

    Schema::create('bookings', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('event_id')->nullable();
      $table->unsignedBigInteger('organizer_id')->nullable();
      $table->text('variation')->nullable();
      $table->integer('quantity')->nullable();
      $table->timestamp('stock_released_at')->nullable();
      $table->timestamps();
    });

    Schema::create('event_addons', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('event_id')->nullable();
      $table->string('title')->nullable();
      $table->integer('stock')->nullable();
      $table->timestamps();
    });

    Schema::create('booking_addons', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('booking_id');
      $table->unsignedBigInteger('event_addon_id')->nullable();
      $table->integer('quantity')->default(1);
      $table->boolean('redeemed')->default(false);
      $table->timestamps();
    });
  }

  protected function tearDown(): void
  {
    Schema::dropIfExists('booking_addons');
    Schema::dropIfExists('event_addons');
    Schema::dropIfExists('bookings');
    Schema::dropIfExists('tickets');
    Schema::dropIfExists('events');

    parent::tearDown();
  }

  public function test_release_restores_variation_stock_only_once(): void
  {
    $eventId = DB::table('events')->insertGetId([
      'organizer_id' => 1,
      'event_type' => 'venue',
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    $ticket = Ticket::create([
      'event_id' => $eventId,
      'title' => 'Entrada',
      'pricing_type' => 'variation',
      'ticket_available_type' => 'limited',
      'ticket_available' => null,
      'variations' => json_encode([[
        'name' => 'General',
        'price' => 100,
        'ticket_available_type' => 'limited',
        'ticket_available' => 3,
        'max_ticket_buy_type' => 'unlimited',
        'v_max_ticket_buy' => null,
      ]]),
    ]);

    $booking = Booking::create([
      'event_id' => $eventId,
      'organizer_id' => 1,
      'variation' => json_encode([
        ['ticket_id' => $ticket->id, 'name' => 'General', 'qty' => 1, 'price' => 100],
        ['ticket_id' => $ticket->id, 'name' => 'General', 'qty' => 1, 'price' => 100],
      ]),
      'quantity' => 2,
    ]);

    $service = new BookingStockService();

    $this->assertTrue($service->release($booking));
    $this->assertSame(5, (int) json_decode($ticket->fresh()->variations, true)[0]['ticket_available']);
    $this->assertNotNull($booking->fresh()->stock_released_at);

    $this->assertFalse($service->release($booking->fresh()));
    $this->assertSame(5, (int) json_decode($ticket->fresh()->variations, true)[0]['ticket_available']);
  }

  public function test_release_restores_single_ticket_stock(): void
  {
    $eventId = DB::table('events')->insertGetId([
      'organizer_id' => 1,
      'event_type' => 'online',
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    $ticket = Ticket::create([
      'event_id' => $eventId,
      'title' => 'General',
      'pricing_type' => 'normal',
      'ticket_available_type' => 'limited',
      'ticket_available' => 3,
    ]);

    $booking = Booking::create([
      'event_id' => $eventId,
      'organizer_id' => 1,
      'variation' => null,
      'quantity' => 2,
    ]);

    $this->assertTrue((new BookingStockService())->release($booking));
    $this->assertSame(5, (int) $ticket->fresh()->ticket_available);
  }

  public function test_release_restores_addon_stock(): void
  {
    $eventId = DB::table('events')->insertGetId([
      'organizer_id' => 1,
      'event_type' => 'venue',
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    $addonId = DB::table('event_addons')->insertGetId([
      'event_id' => $eventId,
      'title' => 'Drink',
      'stock' => 8,
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    $booking = Booking::create([
      'event_id' => $eventId,
      'organizer_id' => 1,
      'variation' => null,
      'quantity' => 1,
    ]);

    DB::table('booking_addons')->insert([
      'booking_id' => $booking->id,
      'event_addon_id' => $addonId,
      'quantity' => 2,
      'redeemed' => false,
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    $this->assertTrue((new BookingStockService())->release($booking));
    $this->assertSame(10, (int) DB::table('event_addons')->where('id', $addonId)->value('stock'));
  }
}
