<?php

namespace Tests\Feature;

use App\Models\Event\Booking;
use App\Services\TicketScanService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TicketScanServiceTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    Schema::create('bookings', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('event_id')->nullable();
      $table->unsignedBigInteger('organizer_id')->nullable();
      $table->text('variation')->nullable();
      $table->integer('quantity')->default(1);
      $table->string('paymentStatus')->nullable();
      $table->text('scanned_tickets')->nullable();
      $table->timestamps();
    });

    Schema::create('ticket_scans', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('booking_id');
      $table->unsignedBigInteger('event_id')->nullable();
      $table->unsignedBigInteger('organizer_id')->nullable();
      $table->string('unique_id', 64);
      $table->string('scanner_type', 20);
      $table->unsignedBigInteger('scanner_id')->nullable();
      $table->string('ip', 45)->nullable();
      $table->string('user_agent', 255)->nullable();
      $table->timestamp('scanned_at');
      $table->timestamps();
      $table->unique(['booking_id', 'unique_id']);
    });
  }

  protected function tearDown(): void
  {
    Schema::dropIfExists('ticket_scans');
    Schema::dropIfExists('bookings');
    parent::tearDown();
  }

  public function test_scan_is_idempotent_and_writes_one_audit_row(): void
  {
    $booking = $this->booking('completed', 5);

    $service = new TicketScanService();

    $this->assertSame(TicketScanService::VERIFIED, $service->scan($booking, 'uuid-1', 'organizer', 5, 5));
    $this->assertSame(TicketScanService::ALREADY_SCANNED, $service->scan($booking, 'uuid-1', 'organizer', 5, 5));
    $this->assertSame(1, \DB::table('ticket_scans')->count());
    $this->assertSame(['uuid-1'], $booking->fresh()->scannedTicketIds());
  }

  public function test_scan_rejects_another_organizer(): void
  {
    $booking = $this->booking('completed', 5);

    $this->assertSame(TicketScanService::NO_PERMISSION, (new TicketScanService())->scan($booking, 'uuid-1', 'organizer', 9, 9));
    $this->assertSame(0, \DB::table('ticket_scans')->count());
  }

  public function test_scan_rejects_refunded_booking(): void
  {
    $booking = $this->booking('refunded', 5);

    $this->assertSame(TicketScanService::PAYMENT_REJECTED, (new TicketScanService())->scan($booking, 'uuid-1', 'organizer', 5, 5));
    $this->assertSame(0, \DB::table('ticket_scans')->count());
  }

  public function test_scan_rejects_unknown_ticket_id(): void
  {
    $booking = $this->booking('completed', 5);

    $this->assertSame(TicketScanService::UNVERIFIED, (new TicketScanService())->scan($booking, 'uuid-unknown', 'organizer', 5, 5));
    $this->assertSame(0, \DB::table('ticket_scans')->count());
  }

  private function booking(string $status, int $organizerId): Booking
  {
    return Booking::create([
      'event_id' => 10,
      'organizer_id' => $organizerId,
      'quantity' => 1,
      'paymentStatus' => $status,
      'variation' => json_encode([[
        'ticket_id' => 1,
        'name' => 'General',
        'qty' => 1,
        'price' => 100,
        'unique_id' => 'uuid-1',
      ]]),
    ]);
  }
}
