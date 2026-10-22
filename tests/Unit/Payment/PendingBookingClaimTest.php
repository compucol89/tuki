<?php

namespace Tests\Unit\Payment;

use App\Http\Controllers\FrontEnd\PaymentGateway\MercadoPagoController;
use App\Models\PendingBooking;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;
use Tests\TestCase;

class PendingBookingClaimTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    Schema::create('pending_bookings', function (Blueprint $table) {
      $table->id();
      $table->string('token')->unique();
      $table->unsignedBigInteger('event_id')->nullable();
      $table->text('data')->nullable();
      $table->decimal('amount', 12, 2)->default(0);
      $table->string('status')->default('pending');
      $table->timestamp('expires_at')->nullable();
      $table->timestamps();
    });
  }

  protected function tearDown(): void
  {
    Schema::dropIfExists('pending_bookings');
    parent::tearDown();
  }

  public function test_expired_pending_booking_can_be_claimed_for_late_payment(): void
  {
    PendingBooking::create([
      'token' => 'late-token',
      'event_id' => 1,
      'amount' => 100,
      'status' => 'expired',
      'expires_at' => now()->subDay(),
    ]);

    $this->assertSame(1, $this->claim('late-token'));
    $this->assertSame('processing', PendingBooking::where('token', 'late-token')->value('status'));
  }

  public function test_completed_booking_is_not_reclaimed(): void
  {
    PendingBooking::create([
      'token' => 'done-token',
      'event_id' => 1,
      'amount' => 100,
      'status' => 'completed',
      'expires_at' => now()->subDay(),
    ]);

    $this->assertSame(0, $this->claim('done-token'));
  }

  public function test_stale_processing_booking_is_recovered(): void
  {
    $pending = PendingBooking::create([
      'token' => 'stale-token',
      'event_id' => 1,
      'amount' => 100,
      'status' => 'processing',
      'expires_at' => now()->subDay(),
    ]);
    $pending->updated_at = now()->subMinutes(45);
    $pending->save();

    $this->assertSame(1, $this->claim('stale-token'));
  }

  private function claim(string $token): int
  {
    $controller = (new ReflectionClass(MercadoPagoController::class))->newInstanceWithoutConstructor();
    $method = new \ReflectionMethod($controller, 'claimPendingBooking');
    $method->setAccessible(true);

    return (int) $method->invoke($controller, $token);
  }
}
