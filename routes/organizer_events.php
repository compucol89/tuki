<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/organizer')->middleware('auth:organizer', 'Deactive:organizer', 'EmailStatus:organizer', 'adminLang')->group(function () {
  Route::get('event-management/events/', 'BackEnd\Organizer\EventController@index')->name('organizer.event_management.event');
  Route::get('choose-event-type/', 'BackEnd\Organizer\EventController@choose_event_type')->name('choose-event-type');
  Route::get('add-event/', 'BackEnd\Organizer\EventController@add_event')->name('organizer.add.event.event');
  Route::post('event-imagesstore', 'BackEnd\Organizer\EventController@gallerystore')->name('organizer.event.imagesstore');
  Route::post('event-imagermv', 'BackEnd\Organizer\EventController@imagermv')->name('organizer.event.imagermv');
  Route::post('event-store', 'BackEnd\Organizer\EventController@store')->name('organizer.event_management.store_event');
  Route::post('/event/{id}/update-status', 'BackEnd\Organizer\EventController@updateStatus')->name('organizer.event_management.event.event_status');
  Route::post('/event/{id}/update-featured', 'BackEnd\Organizer\EventController@updateFeatured')->name('organizer.event_management.event.update_featured');
  Route::post('/delete-event/{id}', 'BackEnd\Organizer\EventController@destroy')->name('organizer.event_management.delete_event');
  Route::get('/edit-event/{id}', 'BackEnd\Organizer\EventController@edit')->name('organizer.event_management.edit_event');
  Route::post('/event-img-dbrmv', 'BackEnd\Organizer\EventController@imagedbrmv')->name('organizer.event.imgdbrmv');
  Route::post('/delete-date/{id}', 'BackEnd\Organizer\EventController@deleteDate')->name('organizer.event.delete.date');
  Route::get('/edit-ticket-setting/{id}', 'BackEnd\Organizer\EventController@editTicketSetting')->name('organizer.event_management.ticket_setting');
  Route::post('/update-ticket-setting', 'BackEnd\Organizer\EventController@updateTicketSetting')->name('organizer.event_management.update_ticket_setting');
  Route::get('/event-images/{id}', 'BackEnd\Organizer\EventController@images')->name('organizer.event.images');
  Route::post('/event-update', 'BackEnd\Organizer\EventController@update')->name('organizer.event.update');
  Route::post('bulk/delete/event', 'BackEnd\Organizer\EventController@bulk_delete')->name('organizer.event_management.bulk_delete_event');
  Route::get('event/ticket', 'BackEnd\Organizer\TicketController@index')->name('organizer.event.ticket');
  Route::get('event/add-ticket', 'BackEnd\Organizer\TicketController@create')->name('organizer.event.add.ticket');
  Route::post('event/ticket/store-ticket', 'BackEnd\Organizer\TicketController@store')->name('organizer.ticket_management.store_ticket');
  Route::get('event/edit/ticket', 'BackEnd\Organizer\TicketController@edit')->name('organizer.event.edit.ticket');
  Route::post('event/ticket/delete-ticket', 'BackEnd\Organizer\TicketController@destroy')->name('organizer.ticket_management.delete_ticket');
  Route::post('delete-variation/{id}', 'BackEnd\Organizer\TicketController@delete_variation')->name('organizer.delete.variation');
  Route::post('ticket_management/update/ticket', 'BackEnd\Organizer\TicketController@update')->name('organizer.ticket_management.update_ticket');
  Route::post('bulk/delete/bulk/event/ticket', 'BackEnd\Organizer\TicketController@bulk_delete')->name('organizer.event_management.bulk_delete_event_ticket');
  Route::get('event-booking', 'BackEnd\Organizer\EventBookingController@index')->name('organizer.event.booking');
  Route::post('event-booking/update/payment-status/{id}', 'BackEnd\Organizer\EventBookingController@updatePaymentStatus')->name('organizer.event_booking.update_payment_status');
  Route::get('event-booking/details/{id}', 'BackEnd\Organizer\EventBookingController@show')->name('organizer.event_booking.details');
  Route::post('/{id}/delete', 'BackEnd\Organizer\EventBookingController@destroy')->name('organizer.event_booking.delete');
  Route::post('/event-booking/bulk-delete', 'BackEnd\Organizer\EventBookingController@bulkDestroy')->name('organizer.event_booking.bulk_delete');
  Route::get('/event-booking/report', 'BackEnd\Organizer\EventBookingController@report')->name('organizer.event_booking.report');
  Route::get('/event-booking/export', 'BackEnd\Organizer\EventBookingController@export')->name('organizer.event_bookings.export');
});
