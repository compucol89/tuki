<?php

namespace Tests\Unit;

use App\Models\Event\Booking;
use Tests\TestCase;

class EventBookingScanStatusTest extends TestCase
{
  public function test_booking_without_quantity_is_not_fully_scanned(): void
  {
    $booking = new Booking([
      'quantity' => null,
      'scanned_tickets' => null,
    ]);

    $this->assertFalse($booking->isFullyScanned());
  }

  public function test_booking_is_fully_scanned_only_when_quantity_is_positive_and_all_tickets_were_scanned(): void
  {
    $booking = new Booking([
      'quantity' => 2,
      'scanned_tickets' => json_encode(['a', 'b']),
    ]);

    $this->assertTrue($booking->isFullyScanned());
  }
}
