<?php

namespace Tests\Feature;

use App\Http\Controllers\FrontEnd\Event\BookingController;
use App\Http\Controllers\BackEnd\AdminController;
use App\Models\Event\Booking;
use App\Models\Event\Ticket;
use App\Models\Event\TicketContent;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use RuntimeException;
use Tests\TestCase;

class EventBookingTicketSelectionValidationTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    $this->setUpSchema();
    Session::start();
  }

  protected function tearDown(): void
  {
    Session::flush();

    Schema::dropIfExists('event_contents');
    Schema::dropIfExists('ticket_contents');
    Schema::dropIfExists('bookings');
    Schema::dropIfExists('tickets');
    Schema::dropIfExists('events');
    Schema::dropIfExists('basic_settings');

    parent::tearDown();
  }

  public function test_store_data_rejects_selected_tickets_from_another_event(): void
  {
    $eventA = $this->createEvent();
    $eventB = $this->createEvent();

    $ticketFromEventA = Ticket::create([
      'event_id' => $eventA,
      'title' => 'General',
      'pricing_type' => 'normal',
      'ticket_available_type' => 'limited',
      'ticket_available' => 10,
      'price' => 100,
    ]);

    Session::put('selTickets', [
      [
        'ticket_id' => $ticketFromEventA->id,
        'name' => 'General',
        'qty' => 1,
        'price' => 100,
        'early_bird_dicount' => 0,
      ],
    ]);

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Las entradas seleccionadas no corresponden a este evento.');

    (new BookingController())->storeData($this->bookingInfo($eventB));
  }

  public function test_store_data_uses_pending_booking_ticket_selection_when_session_is_not_available(): void
  {
    $event = $this->createEvent(['event_type' => 'venue']);
    $ticket = Ticket::create([
      'event_id' => $event,
      'title' => 'Entrada',
      'pricing_type' => 'normal',
      'ticket_available_type' => 'limited',
      'ticket_available' => 10,
      'price' => 40000,
    ]);

    Session::forget('selTickets');
    Session::forget('event_date');

    $booking = (new BookingController())->storeData($this->bookingInfo($event, [
      'quantity' => 1,
      'price' => 40000,
      'tax' => 6000,
      'selTickets' => [[
        'ticket_id' => $ticket->id,
        'name' => 'Entrada',
        'qty' => 1,
        'price' => 40000,
        'early_bird_dicount' => 0,
      ]],
      'event_date' => 'Sat, Jun 27, 2026 06:30pm',
    ]));

    $this->assertSame(1, (int) $booking->quantity);
    $this->assertSame('Sat, Jun 27, 2026 06:30pm', $booking->event_date);
    $this->assertNotEmpty($booking->variation);
    $this->assertSame('Entrada', json_decode($booking->variation, true)[0]['name']);
  }

  public function test_store_data_uses_ticket_title_when_selected_variation_name_is_empty(): void
  {
    $event = $this->createEvent(['event_type' => 'venue']);
    $ticket = Ticket::create([
      'event_id' => $event,
      'title' => null,
      'pricing_type' => 'variation',
      'variations' => json_encode([[
        'name' => null,
        'price' => 14000,
        'ticket_available_type' => 'limited',
        'ticket_available' => 46,
        'max_ticket_buy_type' => 'unlimited',
        'v_max_ticket_buy' => null,
      ]]),
    ]);

    TicketContent::create([
      'ticket_id' => $ticket->id,
      'language_id' => 1,
      'title' => 'Entrada general 2x1',
    ]);

    Session::put('selTickets', [[
      'ticket_id' => $ticket->id,
      'name' => null,
      'qty' => 1,
      'price' => 14000,
      'early_bird_dicount' => 0,
    ]]);

    $booking = (new BookingController())->storeData($this->bookingInfo($event, [
      'quantity' => 1,
      'price' => 14000,
      'selTickets' => Session::get('selTickets'),
    ]));

    $variation = json_decode($booking->variation, true)[0];

    $this->assertSame('Entrada general 2x1', $variation['name']);
    $this->assertSame(45, json_decode($ticket->fresh()->variations, true)[0]['ticket_available']);
  }

  public function test_store_data_rejects_ticketed_venue_booking_without_selected_tickets(): void
  {
    $event = $this->createEvent(['event_type' => 'venue']);
    Ticket::create([
      'event_id' => $event,
      'title' => 'Entrada',
      'pricing_type' => 'normal',
      'ticket_available_type' => 'limited',
      'ticket_available' => 10,
      'price' => 40000,
    ]);

    Session::forget('selTickets');

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('No pudimos validar las entradas seleccionadas.');

    (new BookingController())->storeData($this->bookingInfo($event, [
      'quantity' => null,
      'selTickets' => null,
    ]));
  }

  public function test_store_data_rejects_selected_ticket_when_stock_is_exhausted(): void
  {
    $event = $this->createEvent(['event_type' => 'venue']);
    $ticket = Ticket::create([
      'event_id' => $event,
      'title' => 'Free Pass',
      'pricing_type' => 'free',
      'ticket_available_type' => 'limited',
      'ticket_available' => 0,
      'price' => 0,
    ]);

    Session::put('selTickets', [[
      'ticket_id' => $ticket->id,
      'name' => 'Free Pass',
      'qty' => 1,
      'price' => 0,
      'early_bird_dicount' => 0,
    ]]);

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('No hay stock disponible para la entrada seleccionada.');

    (new BookingController())->storeData($this->bookingInfo($event, [
      'quantity' => 1,
      'price' => 0,
      'selTickets' => Session::get('selTickets'),
    ]));
  }

  public function test_admin_scanner_rejects_code_when_booking_has_no_issued_ticket_ids(): void
  {
    Booking::create([
      'booking_id' => 'broken-booking',
      'event_id' => $this->createEvent(),
      'paymentStatus' => 'completed',
      'quantity' => null,
      'variation' => null,
      'scanned_tickets' => null,
    ]);

    $response = (new AdminController())->check_qrcode(Request::create('/admin/check-qrcode', 'POST', [
      'booking_id' => 'broken-booking__ghost-ticket',
    ]));

    $payload = $response->getData(true);

    $this->assertSame('error', $payload['alert_type']);
    $this->assertSame('Unverified', $payload['message']);
    $this->assertNull(Booking::where('booking_id', 'broken-booking')->first()->scanned_tickets);
  }

  public function test_admin_scanner_handles_empty_scanned_tickets_for_valid_ticket_id(): void
  {
    Booking::create([
      'booking_id' => 'valid-booking',
      'event_id' => $this->createEvent(),
      'paymentStatus' => 'completed',
      'quantity' => 1,
      'variation' => json_encode([[
        'ticket_id' => 1,
        'name' => 'Entrada',
        'qty' => 1,
        'price' => 40000,
        'unique_id' => 'valid-ticket',
      ]]),
      'scanned_tickets' => '',
    ]);

    $response = (new AdminController())->check_qrcode(Request::create('/admin/check-qrcode', 'POST', [
      'booking_id' => 'valid-booking__valid-ticket',
    ]));

    $payload = $response->getData(true);

    $this->assertSame('success', $payload['alert_type']);
    $this->assertSame(['valid-ticket'], Booking::where('booking_id', 'valid-booking')->first()->scannedTicketIds());
  }

  private function setUpSchema(): void
  {
    Schema::create('events', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('organizer_id')->nullable();
      $table->string('event_type')->nullable();
      $table->string('date_type')->nullable();
      $table->string('start_date')->nullable();
      $table->string('start_time')->nullable();
      $table->timestamps();
    });

    Schema::create('event_contents', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('event_id')->nullable();
      $table->unsignedBigInteger('language_id')->nullable();
      $table->string('title')->nullable();
      $table->string('slug')->nullable();
      $table->timestamps();
    });

    Schema::create('ticket_contents', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('ticket_id')->nullable();
      $table->unsignedBigInteger('language_id')->nullable();
      $table->string('title')->nullable();
      $table->text('description')->nullable();
      $table->timestamps();
    });

    Schema::create('tickets', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('event_id')->nullable();
      $table->string('title')->nullable();
      $table->string('pricing_type')->nullable();
      $table->string('ticket_available_type')->nullable();
      $table->integer('ticket_available')->nullable();
      $table->decimal('price', 10, 2)->nullable();
      $table->text('variations')->nullable();
      $table->timestamps();
    });

    Schema::create('basic_settings', function (Blueprint $table) {
      $table->id();
      $table->integer('uniqid')->default(12345);
      $table->decimal('tax', 10, 2)->nullable();
      $table->decimal('commission', 10, 2)->nullable();
      $table->timestamps();
    });

    Schema::create('bookings', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('customer_id')->nullable();
      $table->string('booking_id')->nullable();
      $table->unsignedBigInteger('event_id')->nullable();
      $table->unsignedBigInteger('organizer_id')->nullable();
      $table->string('fname')->nullable();
      $table->string('lname')->nullable();
      $table->string('email')->nullable();
      $table->string('phone')->nullable();
      $table->string('country')->nullable();
      $table->string('state')->nullable();
      $table->string('city')->nullable();
      $table->string('zip_code')->nullable();
      $table->string('address')->nullable();
      $table->text('variation')->nullable();
      $table->decimal('price', 10, 2)->nullable();
      $table->decimal('tax', 10, 2)->nullable();
      $table->decimal('commission', 10, 2)->nullable();
      $table->decimal('tax_percentage', 10, 2)->nullable();
      $table->decimal('commission_percentage', 10, 2)->nullable();
      $table->integer('quantity')->nullable();
      $table->decimal('discount', 10, 2)->nullable();
      $table->decimal('early_bird_discount', 10, 2)->nullable();
      $table->string('currencyText')->nullable();
      $table->string('currencyTextPosition')->nullable();
      $table->string('currencySymbol')->nullable();
      $table->string('currencySymbolPosition')->nullable();
      $table->string('paymentMethod')->nullable();
      $table->string('gatewayType')->nullable();
      $table->string('paymentStatus')->nullable();
      $table->string('invoice')->nullable();
      $table->string('attachmentFile')->nullable();
      $table->string('event_date')->nullable();
      $table->string('scanned_tickets')->nullable();
      $table->string('conversation_id')->nullable();
      $table->string('access_token')->nullable();
      $table->timestamp('token_legacy_expires_at')->nullable();
      $table->string('fiscal_invoice_token')->nullable();
      $table->timestamps();
    });

    DB::table('basic_settings')->insert([
      'uniqid' => 12345,
      'tax' => 0,
      'commission' => 0,
      'created_at' => now(),
      'updated_at' => now(),
    ]);
  }

  private function createEvent(array $attributes = []): int
  {
    return DB::table('events')->insertGetId(array_merge([
      'organizer_id' => 1,
      'event_type' => 'venue',
      'date_type' => 'single',
      'start_date' => '2026-06-27',
      'start_time' => '18:30',
      'created_at' => now(),
      'updated_at' => now(),
    ], $attributes));
  }

  private function bookingInfo(int $eventId, array $overrides = []): array
  {
    return array_merge([
      'fname' => 'Juan',
      'lname' => 'Perez',
      'email' => 'juan@example.test',
      'phone' => '11111111',
      'country' => '',
      'state' => '',
      'city' => '',
      'zip_code' => '',
      'address' => '',
      'event_id' => $eventId,
      'price' => 100,
      'tax' => 0,
      'commission' => 0,
      'quantity' => 1,
      'discount' => 0,
      'total_early_bird_dicount' => 0,
      'currencyText' => 'ARS',
      'currencyTextPosition' => 'right',
      'currencySymbol' => '$',
      'currencySymbolPosition' => 'left',
      'paymentMethod' => 'free',
      'gatewayType' => 'free',
      'paymentStatus' => 'completed',
      'cart_addons' => [],
    ], $overrides);
  }
}
