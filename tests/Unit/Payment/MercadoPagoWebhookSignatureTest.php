<?php

namespace Tests\Unit\Payment;

use App\Http\Controllers\FrontEnd\PaymentGateway\MercadoPagoController;
use Illuminate\Http\Request;
use ReflectionClass;
use Tests\TestCase;

class MercadoPagoWebhookSignatureTest extends TestCase
{
  public function test_missing_secret_is_rejected_in_production(): void
  {
    $this->app->detectEnvironment(fn () => 'production');
    config(['services.mercadopago.webhook_secret' => null]);

    $this->assertFalse($this->invokeSignatureCheck(new Request()));
  }

  public function test_missing_secret_is_allowed_only_outside_production(): void
  {
    $this->app->detectEnvironment(fn () => 'local');
    config(['services.mercadopago.webhook_secret' => null]);

    $this->assertTrue($this->invokeSignatureCheck(new Request()));
  }

  public function test_signature_is_rejected_when_secret_exists_but_headers_are_missing(): void
  {
    $this->app->detectEnvironment(fn () => 'production');
    config(['services.mercadopago.webhook_secret' => 'super-secret']);

    $this->assertFalse($this->invokeSignatureCheck(new Request()));
  }

  private function invokeSignatureCheck(Request $request): bool
  {
    $controller = (new ReflectionClass(MercadoPagoController::class))->newInstanceWithoutConstructor();
    $method = new \ReflectionMethod($controller, 'hasValidWebhookSignature');
    $method->setAccessible(true);

    return (bool) $method->invoke($controller, $request, '123456');
  }
}
