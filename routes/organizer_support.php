<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/organizer')->middleware('auth:organizer', 'Deactive:organizer', 'EmailStatus:organizer', 'adminLang')->group(function () {
  Route::prefix('support-tikcet')->group(function () {
    Route::get('create', 'BackEnd\Organizer\SupportTicketController@create')->name('organizer.support_ticket.create');
    Route::post('/store', 'BackEnd\Organizer\SupportTicketController@store')->name('organizer.support_ticket.store');
    Route::get('tickets', 'BackEnd\Organizer\SupportTicketController@index')->name('organizer.support_tickets');
    Route::get('/message/{id}', 'BackEnd\Organizer\SupportTicketController@message')->name('organizer.support_tickets.message');
    Route::post('/zip-upload', 'BackEnd\Organizer\SupportTicketController@zip_file_upload')->name('organizer.support_ticket.zip_file.upload');
    Route::post('/reply/{id}', 'BackEnd\Organizer\SupportTicketController@ticketreply')->name('organizer.support_ticket.reply');
    Route::post('/delete/{id}', 'BackEnd\Organizer\SupportTicketController@delete')->name('organizer.support_tickets.delete');
    Route::post('/bulk/delete/', 'BackEnd\Organizer\SupportTicketController@bulk_delete')->name('organizer.support_tickets.bulk_delete');
  });
});
