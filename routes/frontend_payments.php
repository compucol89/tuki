<?php

use Illuminate\Support\Facades\Route;

Route::prefix('event-booking')->group(function () {
  Route::post('/apply-coupon', 'FrontEnd\EventController@applyCoupon')->name('apply-coupon');
  Route::get('/mercadopago/notify', 'FrontEnd\PaymentGateway\MercadoPagoController@notify')->name('event_booking.mercadopago.notify');
});

Route::post('/push-notification/store-endpoint', 'FrontEnd\PushNotificationController@store');
