<?php

namespace Tests\Unit\Services;

use App\Models\Event\Booking;
use App\Services\EventTicketSalesSummaryService;
use Tests\TestCase;

class EventTicketSalesSummaryServiceTest extends TestCase
{
  public function test_it_groups_sales_by_event_and_ticket_type(): void
  {
    $bookings = collect([
      new Booking([
        'event_id' => 1,
        'organizer_id' => 7,
        'paymentStatus' => 'completed',
        'quantity' => 2,
        'tax' => 3000,
        'commission' => 1000,
        'variation' => json_encode([
          ['ticket_id' => 10, 'name' => 'VIP', 'qty' => 1, 'price' => 10000, 'early_bird_dicount' => 0, 'unique_id' => 'vip-1'],
          ['ticket_id' => 10, 'name' => 'VIP', 'qty' => 1, 'price' => 10000, 'early_bird_dicount' => 0, 'unique_id' => 'vip-2'],
        ]),
        'scanned_tickets' => json_encode(['vip-1']),
      ]),
      new Booking([
        'event_id' => 1,
        'organizer_id' => 7,
        'paymentStatus' => 'pending',
        'quantity' => 1,
        'variation' => json_encode([
          ['ticket_id' => 10, 'name' => 'VIP', 'qty' => 1, 'price' => 10000, 'early_bird_dicount' => 0, 'unique_id' => 'vip-3'],
        ]),
      ]),
      new Booking([
        'event_id' => 1,
        'organizer_id' => 7,
        'paymentStatus' => 'free',
        'quantity' => 2,
        'variation' => json_encode([
          ['ticket_id' => 11, 'name' => 'FREEPASS', 'qty' => 1, 'price' => 0, 'early_bird_dicount' => 0, 'unique_id' => 'free-1'],
          ['ticket_id' => 11, 'name' => 'FREEPASS', 'qty' => 1, 'price' => 0, 'early_bird_dicount' => 0, 'unique_id' => 'free-2'],
        ]),
      ]),
    ]);

    $summary = (new EventTicketSalesSummaryService())->summarize($bookings, [
      1 => (object) ['title' => 'Fiesta test'],
    ]);

    $this->assertSame('Fiesta test', $summary[0]['event_title']);
    $this->assertSame('FREEPASS', $summary[0]['ticket_name']);
    $this->assertSame(2, $summary[0]['sold']);
    $this->assertSame(0, $summary[0]['pending']);
    $this->assertSame(0.0, $summary[0]['revenue']);
    $this->assertSame(0.0, $summary[0]['charged_amount']);
    $this->assertSame(0.0, $summary[0]['organizer_amount']);
    $this->assertSame(0.0, $summary[0]['system_amount']);

    $this->assertSame('VIP', $summary[1]['ticket_name']);
    $this->assertSame(2, $summary[1]['sold']);
    $this->assertSame(1, $summary[1]['pending']);
    $this->assertSame(1, $summary[1]['scanned']);
    $this->assertSame(20000.0, $summary[1]['revenue']);
    $this->assertSame(23000.0, $summary[1]['charged_amount']);
    $this->assertSame(19000.0, $summary[1]['organizer_amount']);
    $this->assertSame(4000.0, $summary[1]['system_amount']);
  }
}
