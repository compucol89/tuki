<?php

namespace Tests\Unit;

use App\Models\Event\Booking;
use App\Models\Event\BookingAddon;
use App\Models\Event\TicketContent;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EventBookingInvoiceFileTest extends TestCase
{
  public function test_invoice_file_is_not_available_when_database_value_points_to_missing_pdf(): void
  {
    $booking = new Booking([
      'invoice' => 'missing-booking-invoice.pdf',
    ]);

    $this->assertFalse($booking->hasInvoiceFile());
  }

  public function test_ticket_breakdown_groups_variations_and_tracks_scan_progress_by_type(): void
  {
    $booking = new Booking([
      'quantity' => 3,
      'variation' => json_encode([
        [
          'ticket_id' => 10,
          'name' => 'VIP',
          'qty' => 1,
          'price' => 10000,
          'early_bird_dicount' => 1000,
          'unique_id' => 'vip-1',
        ],
        [
          'ticket_id' => 10,
          'name' => 'VIP',
          'qty' => 1,
          'price' => 10000,
          'early_bird_dicount' => 1000,
          'unique_id' => 'vip-2',
        ],
        [
          'ticket_id' => 11,
          'name' => 'General',
          'qty' => 1,
          'price' => 5000,
          'early_bird_dicount' => 0,
          'unique_id' => 'general-1',
        ],
      ]),
      'scanned_tickets' => json_encode(['vip-1']),
    ]);

    $breakdown = $booking->ticketBreakdown();

    $this->assertCount(2, $breakdown);
    $this->assertSame('VIP', $breakdown[0]['name']);
    $this->assertSame(2, $breakdown[0]['quantity']);
    $this->assertSame(20000.0, $breakdown[0]['price']);
    $this->assertSame(2000.0, $breakdown[0]['discount']);
    $this->assertSame(18000.0, $breakdown[0]['subtotal']);
    $this->assertSame(1, $breakdown[0]['scanned']);
    $this->assertSame(1, $breakdown[0]['pending']);
    $this->assertSame(50, $breakdown[0]['scan_percent']);
    $this->assertSame('General', $breakdown[1]['name']);
    $this->assertSame(0, $breakdown[1]['scanned']);
  }

  public function test_ticket_display_name_uses_selected_ticket_name_for_normal_entries(): void
  {
    $this->createTicketContentsTable();

    TicketContent::create([
      'ticket_id' => 193,
      'language_id' => 1,
      'title' => 'Mesa VIP 4 personas',
    ]);

    $name = Booking::displayTicketName(193, 'Mesa VIP 4 personas', 'normal', 1);

    $this->assertSame('Mesa VIP 4 personas', $name);
  }

  public function test_ticket_display_name_falls_back_to_ticket_title_when_variation_name_is_empty(): void
  {
    $this->createTicketContentsTable();

    TicketContent::create([
      'ticket_id' => 203,
      'language_id' => 1,
      'title' => 'Entrada general 2x1',
    ]);

    $name = Booking::displayTicketName(203, null, 'variation', 1);

    $this->assertSame('Entrada general 2x1', $name);
  }

  public function test_addon_breakdown_uses_loaded_addons_without_database_queries(): void
  {
    $booking = new Booking();
    $booking->setRelation('addons', collect([
      new BookingAddon([
        'title' => 'Estacionamiento',
        'unit_price' => 1500,
        'quantity' => 2,
        'subtotal' => 3000,
        'redeemed' => true,
      ]),
    ]));

    $breakdown = $booking->addonBreakdown();

    $this->assertSame([
      [
        'title' => 'Estacionamiento',
        'unit_price' => 1500.0,
        'quantity' => 2,
        'subtotal' => 3000.0,
        'redeemed' => true,
      ],
    ], $breakdown);
  }

  private function createTicketContentsTable(): void
  {
    Schema::dropIfExists('ticket_contents');
    Schema::create('ticket_contents', function (Blueprint $table): void {
      $table->id();
      $table->unsignedBigInteger('language_id')->nullable();
      $table->unsignedBigInteger('ticket_id');
      $table->string('title')->nullable();
      $table->text('description')->nullable();
      $table->timestamps();
    });
  }
}
