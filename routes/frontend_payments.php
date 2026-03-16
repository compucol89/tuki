<?php

use Illuminate\Support\Facades\Route;

Route::prefix('event-booking')->group(function () {
  Route::get('/paypal/notify', 'FrontEnd\PaymentGateway\PayPalController@notify')->name('event_booking.paypal.notify');
  Route::get('/paypal/cancel', 'FrontEnd\PaymentGateway\PayPalController@cancel')->name('event_booking.cancel');
  Route::post('/apply-coupon', 'FrontEnd\EventController@applyCoupon')->name('apply-coupon');
  Route::get('/instamojo/notify', 'FrontEnd\PaymentGateway\InstamojoController@notify')->name('event_booking.instamojo.notify');
  Route::get('/paystack/notify', 'FrontEnd\PaymentGateway\PaystackController@notify')->name('event_booking.paystack.notify');
  Route::post('/flutterwave/notify', 'FrontEnd\PaymentGateway\FlutterwaveController@notify')->name('event_booking.flutterwave.notify');
  Route::post('/razorpay/notify', 'FrontEnd\PaymentGateway\RazorpayController@notify')->name('event_booking.razorpay.notify');
  Route::get('/mercadopago/notify', 'FrontEnd\PaymentGateway\MercadoPagoController@notify')->name('event_booking.mercadopago.notify');
  Route::get('/mollie/notify', 'FrontEnd\PaymentGateway\MollieController@notify')->name('event_booking.mollie.notify');
  Route::post('/paytm/notify', 'FrontEnd\PaymentGateway\PaytmController@notify')->name('event_booking.paytm.notify');
  Route::get('/midtrans/make-payment', 'FrontEnd\PaymentGateway\MidtransController@makePayment')->name('makePayment');
  Route::get('/midtrans/notify/{orderId}?', 'FrontEnd\PaymentGateway\MidtransController@ccNotify')->name('event.midtrans.notify');
  Route::get('/midtrans/bank-notify', 'FrontEnd\PaymentGateway\MidtransController@bankNotify')->name('bank.notify');
  Route::get('/iyzico/make-payment', 'FrontEnd\PaymentGateway\IyzipayController@makePayment')->name('event_booking.iyzico.makePayment');
  Route::post('/iyzico/notify', 'FrontEnd\PaymentGateway\IyzipayController@notify')->name('event_booking.iyzico.notify');
  Route::get('/toyyibpay/notify', 'FrontEnd\PaymentGateway\ToyyibpayController@notify')->name('event_booking.toyyibpay.notify');
  Route::get('/paytabs/make-payment', 'FrontEnd\PaymentGateway\PaytabsController@makePayment')->name('event_booking.paytabs.makePayment');
  Route::post('/paytabs/notify', 'FrontEnd\PaymentGateway\PaytabsController@notify')->name('event_booking.paytabs.notify');
  Route::any('/phonepe/notify', 'FrontEnd\PaymentGateway\PhonepeController@notify')->name('event_booking.phonepe.notify');
  Route::get('/yoco/notify', 'FrontEnd\PaymentGateway\YocoController@notify')->name('event_booking.yoco.notify');
  Route::get('/xendit/notify', 'FrontEnd\PaymentGateway\XenditController@notify')->name('event_booking.xindit.notify');
  Route::get('/perfect-money/notify', 'FrontEnd\PaymentGateway\PerfectMoneyController@notify')->name('event_booking.perfect-money.notify');
  Route::get('/perfect-money/cancel', 'FrontEnd\PaymentGateway\PerfectMoneyController@cancel')->name('event_booking.perfect-money.cancel');
});

Route::post('/push-notification/store-endpoint', 'FrontEnd\PushNotificationController@store');
