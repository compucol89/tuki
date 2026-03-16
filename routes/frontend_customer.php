<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/customer')->middleware('auth:customer', 'Deactive:customer', 'change.lang', 'EmailStatus:customer')->group(function () {
  Route::get('/dashboard', 'FrontEnd\CustomerController@index')->name('customer.dashboard');
  Route::get('/edit-profile', 'FrontEnd\CustomerController@edit_profile')->name('customer.edit.profile');
  Route::post('/update-profile', 'FrontEnd\CustomerController@update_profile')->name('customer.profile.update');

  Route::get('/lista-de-deseos', 'FrontEnd\CustomerController@wishlist')->name('customer.wishlist');
  Route::get('/mis-entradas', 'FrontEnd\Event\CustomerBookingController@my_booking')->name('customer.booking.my_booking');
  Route::get('/booking/details/{id}', 'FrontEnd\Event\CustomerBookingController@details')->name('customer.booking_details');

  Route::get('/support-ticket', 'FrontEnd\SupportTicketController@index')->name('customer.support_tickert');
  Route::get('/support-ticket/create', 'FrontEnd\SupportTicketController@create')->name('customer.support_tickert.create');
  Route::post('/support-ticket/store', 'FrontEnd\SupportTicketController@store')->name('customer.support_ticket.store');
  Route::get('/support-ticket/message/{id}', 'FrontEnd\SupportTicketController@message')->name('customer.support_ticket.message');
  Route::post('/support-ticket/reply/{id}', 'FrontEnd\SupportTicketController@reply')->name('customer-reply');

  Route::get('/my-orders', 'FrontEnd\Shop\CustomerOrderController@index')->name('customer.my_orders');
  Route::get('/my-orders/details/{id}', 'FrontEnd\Shop\CustomerOrderController@details')->name('customer.order_details');
});
