<?php

namespace Tests\Unit\Payment;

use App\Http\Controllers\FrontEnd\PaymentGateway\MercadoPagoController;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

class MercadoPagoWebhookLookupTest extends TestCase
{
  public function test_not_found_payment_is_permanent_and_not_retried(): void
  {
    $this->assertTrue($this->isPermanent(404));
  }

  public function test_server_errors_are_transient_and_retried(): void
  {
    $this->assertFalse($this->isPermanent(500));
    $this->assertFalse($this->isPermanent(502));
    $this->assertFalse($this->isPermanent(503));
  }

  public function test_successful_lookup_is_not_a_failure(): void
  {
    $this->assertFalse($this->isPermanent(200));
  }

  private function isPermanent(int $status): bool
  {
    $controller = (new ReflectionClass(MercadoPagoController::class))->newInstanceWithoutConstructor();
    $method = new ReflectionMethod($controller, 'isPermanentPaymentLookupFailure');
    $method->setAccessible(true);

    return (bool) $method->invoke($controller, $status);
  }
}
