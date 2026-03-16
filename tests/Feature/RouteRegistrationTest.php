<?php

namespace Tests\Feature;

use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteRegistrationTest extends TestCase
{
  public function test_critical_booking_and_payment_routes_keep_expected_actions(): void
  {
    $this->assertSame(
      'App\Http\Controllers\FrontEnd\Event\BookingController@index',
      Route::getRoutes()->getByName('ticket.booking')?->getActionName()
    );

    $this->assertSame(
      'App\Http\Controllers\FrontEnd\PaymentGateway\IyzipayController@notify',
      Route::getRoutes()->getByName('event_booking.iyzico.notify')?->getActionName()
    );

    $this->assertSame(
      'App\Http\Controllers\FrontEnd\PaymentGateway\XenditController@notify',
      Route::getRoutes()->getByName('event_booking.xindit.notify')?->getActionName()
    );

    $this->assertSame(
      'App\Http\Controllers\FrontEnd\Shop\PaymentGateway\MidtransController@makePayment',
      Route::getRoutes()->getByName('shop.makePayment')?->getActionName()
    );
  }

  public function test_critical_payment_entrypoints_use_unique_uris(): void
  {
    $criticalRoutes = collect(Route::getRoutes()->getRoutes())
      ->filter(function (IlluminateRoute $route) {
        return in_array($route->getName(), [
          'makePayment',
          'event_booking.iyzico.makePayment',
          'shop.makePayment',
          'event_booking.paytabs.makePayment',
          'shop.paytabs.makePayment',
        ], true);
      })
      ->map(fn (IlluminateRoute $route) => implode('|', $route->methods()) . ' ' . $route->uri());

    $this->assertCount($criticalRoutes->count(), $criticalRoutes->unique());
  }

  public function test_route_collection_has_no_duplicate_method_uri_pairs(): void
  {
    $duplicates = collect(Route::getRoutes()->getRoutes())
      ->map(function (IlluminateRoute $route) {
        $methods = collect($route->methods())->reject(fn (string $method) => $method === 'HEAD')->values()->implode('|');

        return $methods . ' ' . $route->uri();
      })
      ->countBy()
      ->filter(fn (int $count) => $count > 1)
      ->keys()
      ->values();

    $this->assertSame([], $duplicates->all(), 'Duplicate route registrations: ' . $duplicates->implode(', '));
  }
}
