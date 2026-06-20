<?php

namespace Tests\Unit\Services;

use App\Models\Event\Booking;
use App\Models\EventSettlement;
use App\Services\EventSettlementService;
use Tests\TestCase;

class EventSettlementServiceTest extends TestCase
{
  public function test_it_summarizes_event_money_and_pending_settlement_amount(): void
  {
    $bookings = collect([
      new Booking([
        'paymentStatus' => 'completed',
        'quantity' => 2,
        'price' => 100000,
        'tax' => 15000,
        'commission' => 15000,
      ]),
      new Booking([
        'paymentStatus' => 'free',
        'quantity' => 3,
        'price' => 0,
        'tax' => 0,
        'commission' => 0,
      ]),
      new Booking([
        'paymentStatus' => 'pending',
        'quantity' => 1,
        'price' => 50000,
        'tax' => 7500,
        'commission' => 7500,
      ]),
    ]);

    $settlements = collect([
      new EventSettlement([
        'paid_amount' => 20000,
        'covered_organizer_amount' => 20000,
        'balance_debited_amount' => 20000,
      ]),
    ]);

    $summary = (new EventSettlementService())->summarize($bookings, $settlements);

    $this->assertSame(115000.0, $summary['charged_amount']);
    $this->assertSame(85000.0, $summary['organizer_net_amount']);
    $this->assertSame(30000.0, $summary['platform_amount']);
    $this->assertSame(20000.0, $summary['settled_amount']);
    $this->assertSame(20000.0, $summary['covered_organizer_amount']);
    $this->assertSame(65000.0, $summary['pending_organizer_amount']);
    $this->assertSame('partial', $summary['status']);
    $this->assertSame(1, $summary['paid_bookings_count']);
    $this->assertSame(5, $summary['issued_tickets_count']);
  }

  public function test_it_resolves_amount_options_against_the_current_pending_amount(): void
  {
    $service = new EventSettlementService();
    $summary = [
      'charged_amount' => 151800.0,
      'settled_amount' => 0.0,
      'pending_organizer_amount' => 132000.0,
    ];

    $this->assertSame(132000.0, $service->resolvePaidAmount(EventSettlementService::AMOUNT_ORGANIZER_NET, $summary));
    $this->assertSame(151800.0, $service->resolvePaidAmount(EventSettlementService::AMOUNT_CHARGED_TOTAL, $summary));
    $this->assertSame(42000.0, $service->resolvePaidAmount(EventSettlementService::AMOUNT_CUSTOM, $summary, 42000));
  }
}
