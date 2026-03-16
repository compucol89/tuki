<?php

use Illuminate\Support\Facades\Route;

Route::middleware('change.lang')->group(function () {
  Route::post('/check-out2', 'FrontEnd\CheckOutController@checkout2')->name('check-out2');
  Route::get('/checkout', 'FrontEnd\CheckOutController@checkout')->name('check-out');

  Route::prefix('shop')->group(function () {
    Route::get('/', 'FrontEnd\Shop\ShopController@index')->name('shop');
    Route::get('/details/{slug}/{id}', 'FrontEnd\Shop\ShopController@details')->name('shop.details');
    Route::post('review-submit', 'FrontEnd\Shop\ShopController@review')->name('product.review.submit');
    Route::get('add-to-cart/{id}', 'FrontEnd\Shop\ShopController@addToCart')->name('add.cart');
    Route::get('add-to-cart-2/{id}/{qty?}', 'FrontEnd\Shop\ShopController@addToCart2')->name('add.cart2');
    Route::post('order-now', 'FrontEnd\Shop\ShopController@orderNow')->name('order-now');
    Route::get('cart/', 'FrontEnd\Shop\ShopController@cart')->name('shopping.cart');
    Route::get('cart/item/remove/{id}', 'FrontEnd\Shop\ShopController@cartitemremove')->name('cart.item.remove');
    Route::post('cart/update', 'FrontEnd\Shop\ShopController@updatecart')->name('cart.update');
    Route::get('checkout', 'FrontEnd\Shop\ShopController@checkout')->name('shop.checkout');
    Route::post('apply-coupon/', 'FrontEnd\Shop\ShopController@applyCoupon')->name('shop.apply-coupon');
    Route::post('buy/', 'FrontEnd\Shop\OrderController@enrol')->name('shop.buy');
  });

  Route::get('/product-order/{id}/cancel', 'FrontEnd\Shop\OrderController@cancel')->name('product_order.cancel');
  Route::get('/product-order-complete/complete/{via?}', 'FrontEnd\Shop\OrderController@complete')->name('product_order.complete');
});

Route::prefix('product-order')->group(function () {
  Route::get('paypal/notify', 'FrontEnd\Shop\PaymentGateway\PaypalController@notify')->name('product_order.paypal.notify');
  Route::get('paypal/cancel', 'FrontEnd\Shop\PaymentGateway\PaypalController@cancel')->name('product_order.cancel');
  Route::get('paystack/notify', 'FrontEnd\Shop\PaymentGateway\PaystackController@notify')->name('product_order.paystack.notify');
  Route::get('instamojo/notify', 'FrontEnd\Shop\PaymentGateway\InstamojoController@notify')->name('product_order.instamojo.notify');
  Route::post('razorpay/notify', 'FrontEnd\Shop\PaymentGateway\RazorpayController@notify')->name('product_order.razorpay.notify');
  Route::get('mercadopago/notify', 'FrontEnd\Shop\PaymentGateway\MercadoPagoController@notify')->name('product_order.mercadopago.notify');
  Route::get('mollie/notify', 'FrontEnd\Shop\PaymentGateway\MollieController@notify')->name('product_order.mollie.notify');
  Route::post('paytm/notify', 'FrontEnd\Shop\PaymentGateway\PaytmController@notify')->name('product_order.paytm.notify');
  Route::post('flutterwave/notify', 'FrontEnd\Shop\PaymentGateway\FlutterwaveController@notify')->name('product_order.flutterwave.notify');
  Route::get('/midtrans/make-payment', 'FrontEnd\Shop\PaymentGateway\MidtransController@makePayment')->name('shop.makePayment');
  Route::get('/midtrans/notify/{orderId}', 'FrontEnd\Shop\PaymentGateway\MidtransController@ccNotify')->name('shop.midtrans.notify');
  Route::get('/midtrans/bank-notify', 'FrontEnd\Shop\PaymentGateway\MidtransController@bankNotify')->name('shop.bank.notify');
  Route::get('/paytabs/make-payment', 'FrontEnd\Shop\PaymentGateway\PaytabsController@makePayment')->name('shop.paytabs.makePayment');
  Route::post('/paytabs/notify', 'FrontEnd\Shop\PaymentGateway\PaytabsController@notify')->name('shop.paytabs.notify');
  Route::get('/toyyibpay/notify', 'FrontEnd\Shop\PaymentGateway\ToyyibpayController@notify')->name('shop.toyyibpay.notify');
  Route::any('/phonepe/notify', 'FrontEnd\Shop\PaymentGateway\PhonepeController@notify')->name('shop.phonepe.notify');
  Route::get('/yoco/notify', 'FrontEnd\Shop\PaymentGateway\YocoController@notify')->name('shop.yoco.notify');
  Route::get('/xendit/notify', 'FrontEnd\Shop\PaymentGateway\XenditController@notify')->name('shop.xendit.notify');
  Route::post('/iyzico/notify', 'FrontEnd\Shop\PaymentGateway\IyzipayController@notify')->name('shop.iyzico.notify');
  Route::get('/perfect-money/notify', 'FrontEnd\Shop\PaymentGateway\PerfectMoneyController@notify')->name('shop.perfect-money.notify');
  Route::get('/perfect-money/cancel', 'FrontEnd\Shop\PaymentGateway\PerfectMoneyController@cancel')->name('shop.perfect-money.cancel');
});
