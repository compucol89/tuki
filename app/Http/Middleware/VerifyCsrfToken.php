<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
  /**
   * The URIs that should be excluded from CSRF verification.
   *
   * @var array
   */
  protected $except = [
    '*/flutterwave/notify',
    '*/razorpay/notify',
    // Paths exactos — evita wildcard */ que cubriría rutas no intencionadas
    'event-booking/mercadopago/notify',
    'event-booking/mercadopago/webhook',
    'product-order/mercadopago/notify',
    '*/paytm/notify',
    '*/iyzico/notify',
    '*/paytabs/notify/',
    '*/phonepe/notify',
    '/xendit/callback',
  ];
}
