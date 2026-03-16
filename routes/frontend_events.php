<?php

use Illuminate\Support\Facades\Route;

Route::middleware('change.lang')->group(function () {
  Route::post('/ticket-booking/{id}', 'FrontEnd\Event\BookingController@index')->name('ticket.booking');
  Route::get('/event-booking/{id}/cancel', 'FrontEnd\Event\BookingController@cancel')->name('event_booking.cancel');
  Route::get('/event-booking-complete', 'FrontEnd\Event\BookingController@complete')->name('event_booking.complete');
});

Route::middleware('change.lang')->group(function () {
  Route::get('/', 'FrontEnd\HomeController@index')->name('index');
  Route::get('eventos', 'FrontEnd\EventController@index')->name('events');
  Route::get('event/{slug}/{id}', 'FrontEnd\EventController@details')->name('event.details');
  Route::get('addto/wishlist/{id}', 'FrontEnd\EventController@add_to_wishlist')->name('addto.wishlist');
  Route::get('remove/wishlist/{id}', 'FrontEnd\CustomerController@remove_wishlist')->name('remove.wishlist');
  Route::get('organizer/details/{id}/{name}', 'FrontEnd\OrganizerController@details')->name('frontend.organizer.details');
  Route::get('organizers/', 'FrontEnd\OrganizerController@index')->name('frontend.all.organizer');
  Route::post('organizers/contact/send-mail', 'FrontEnd\OrganizerController@sendMail')->name('organizer.contact.send_mail');
});
