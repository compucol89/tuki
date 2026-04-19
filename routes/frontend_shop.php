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
  Route::get('mercadopago/notify', 'FrontEnd\Shop\PaymentGateway\MercadoPagoController@notify')->name('product_order.mercadopago.notify');
});
