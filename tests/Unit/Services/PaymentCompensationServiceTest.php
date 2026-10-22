<?php

namespace Tests\Unit\Services;

use App\Models\PaymentIncident;
use App\Services\PaymentCompensationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PaymentCompensationServiceTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    Schema::create('payment_incidents', function (Blueprint $table) {
      $table->id();
      $table->string('gateway', 40);
      $table->string('payment_id', 120);
      $table->unsignedBigInteger('booking_id')->nullable();
      $table->unsignedBigInteger('event_id')->nullable();
      $table->decimal('amount', 12, 2)->nullable();
      $table->string('currency', 10)->nullable();
      $table->string('reason', 80);
      $table->string('status', 30)->default('manual_review');
      $table->json('context')->nullable();
      $table->timestamps();
    });
  }

  protected function tearDown(): void
  {
    Schema::dropIfExists('payment_incidents');
    parent::tearDown();
  }

  public function test_successful_refund_is_recorded_as_refunded(): void
  {
    Http::fake([
      'api.mercadopago.com/v1/payments/123/refunds' => Http::response(['id' => 999], 201),
    ]);

    $incident = (new PaymentCompensationService())->compensateMercadoPago(
      '123',
      'token',
      ['event_id' => 7, 'amount' => 1500, 'currency' => 'ARS']
    );

    $this->assertSame('refunded', $incident->status);
    $this->assertSame('123', $incident->payment_id);
    $this->assertDatabaseCount('payment_incidents', 1);
    Http::assertSent(fn ($request) => $request->hasHeader('X-Idempotency-Key', 'tukipass-refund-123'));
  }

  public function test_failed_refund_is_recorded_for_manual_review(): void
  {
    Http::fake([
      'api.mercadopago.com/v1/payments/456/refunds' => Http::response(['message' => 'error'], 500),
    ]);

    $incident = (new PaymentCompensationService())->compensateMercadoPago('456', 'token');

    $this->assertSame('refund_failed', $incident->status);
    $this->assertSame(500, $incident->context['refund_http_status']);
  }

  public function test_missing_token_skips_http_and_requires_manual_review(): void
  {
    Http::fake();

    $incident = (new PaymentCompensationService())->compensateMercadoPago('789', '');

    $this->assertSame('manual_review', $incident->status);
    Http::assertNothingSent();
  }
}
