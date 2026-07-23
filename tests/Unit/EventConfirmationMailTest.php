<?php

namespace Tests\Unit;

use App\Mail\EventConfirmationMail;
use App\Models\Event;
use App\Models\Event\Booking;
use App\Models\Event\EventContent;
use App\Models\Language;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EventConfirmationMailTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    $this->setUpSchema();
  }

  protected function tearDown(): void
  {
    @unlink(storage_path('app/invoices/5b472ddd-c2b3-4843-85bc-ef166ddf0d6c.pdf'));

    Schema::dropIfExists('billing_settings');
    Schema::dropIfExists('booking_addons');
    Schema::dropIfExists('bookings');
    Schema::dropIfExists('event_contents');
    Schema::dropIfExists('events');
    Schema::dropIfExists('languages');

    parent::tearDown();
  }

  public function test_confirmation_email_groups_identical_ticket_rows_and_shows_amount_charged(): void
  {
    Language::create([
      'name' => 'Spanish',
      'code' => 'es',
      'direction' => 'ltr',
      'is_default' => 1,
    ]);

    $event = Event::create([
      'organizer_id' => 29,
      'event_type' => 'venue',
      'date_type' => 'single',
      'start_date' => '2026-07-03',
      'start_time' => '21:00',
    ]);

    EventContent::create([
      'event_id' => $event->id,
      'language_id' => 1,
      'title' => 'Colombia vs. Ghana',
    ]);

    if (!is_dir(storage_path('app/invoices'))) {
      mkdir(storage_path('app/invoices'), 0775, true);
    }

    file_put_contents(storage_path('app/invoices/5b472ddd-c2b3-4843-85bc-ef166ddf0d6c.pdf'), 'test');

    $booking = Booking::create([
      'booking_id' => '5b472ddd-c2b3-4843-85bc-ef166ddf0d6c',
      'event_id' => $event->id,
      'organizer_id' => 29,
      'fname' => 'Maria carolina',
      'lname' => 'escalona',
      'email' => 'escalonamariacaro12@gmail.com',
      'phone' => '1126822463',
      'country' => 'Argentina',
      'state' => 'N/A',
      'city' => 'N/A',
      'zip_code' => '0000',
      'address' => 'N/A',
      'variation' => json_encode([
        [
          'ticket_id' => 210,
          'name' => 'Entrada General',
          'qty' => 1,
          'price' => 7000,
          'unique_id' => '18188fb3-93b3-4a59-8dd3-f6764af7950f',
        ],
        [
          'ticket_id' => 210,
          'name' => 'Entrada General',
          'qty' => 1,
          'price' => 7000,
          'unique_id' => '821de519-4ffc-48ce-a275-30726827bd65',
        ],
      ]),
      'price' => 14000,
      'tax' => 2100,
      'commission' => 0,
      'quantity' => 2,
      'discount' => 0,
      'early_bird_discount' => 0,
      'currencyText' => 'ARS',
      'currencyTextPosition' => 'left',
      'currencySymbol' => '$',
      'currencySymbolPosition' => 'left',
      'paymentMethod' => 'Mercadopago',
      'gatewayType' => 'online',
      'paymentStatus' => 'completed',
      'invoice' => '5b472ddd-c2b3-4843-85bc-ef166ddf0d6c.pdf',
      'event_date' => 'Fri, Jul 03, 2026 09:00pm',
    ]);

    $html = (new EventConfirmationMail($booking))->render();

    $this->assertSame(1, substr_count($html, '<td>Entrada General</td>'));
    $this->assertStringContainsString('<td class="text-right">2</td>', $html);
    $this->assertStringContainsString('Costo de servicio', $html);
    $this->assertStringContainsString('$2.100,00', $html);
    $this->assertStringContainsString('$16.100,00', $html);
  }

  private function setUpSchema(): void
  {
    Schema::create('languages', function (Blueprint $table) {
      $table->id();
      $table->string('name')->nullable();
      $table->string('code')->nullable();
      $table->string('direction')->nullable();
      $table->boolean('is_default')->default(false);
      $table->timestamps();
    });

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
      $table->string('address')->nullable();
      $table->string('country')->nullable();
      $table->string('state')->nullable();
      $table->string('city')->nullable();
      $table->string('zip_code')->nullable();
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
      $table->string('access_token')->nullable();
      $table->string('fiscal_invoice_token')->nullable();
      $table->timestamps();
    });

    Schema::create('booking_addons', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('booking_id')->index();
      $table->unsignedBigInteger('event_id')->index();
      $table->unsignedBigInteger('event_addon_id')->nullable()->index();
      $table->string('title');
      $table->text('description')->nullable();
      $table->decimal('unit_price', 10, 2);
      $table->unsignedInteger('quantity');
      $table->decimal('subtotal', 10, 2);
      $table->timestamps();
    });

    Schema::create('billing_settings', function (Blueprint $table) {
      $table->id();
      $table->boolean('enabled')->default(false);
      $table->string('environment')->default('testing');
      $table->string('service_fee_tax_mode')->default('no_vat_added');
      $table->decimal('service_fee_percentage', 8, 4)->default(0);
      $table->decimal('vat_percentage', 8, 4)->default(0);
      $table->string('issuer_name')->nullable();
      $table->string('issuer_cuit')->nullable();
      $table->string('issuer_address')->nullable();
      $table->string('issuer_iva_condition_text')->nullable();
      $table->timestamps();
    });
  }
}
