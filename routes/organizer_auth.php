<?php

use Illuminate\Support\Facades\Route;

Route::get('organizer/pwa/', 'BackEnd\Organizer\OrganizerController@pwa')->middleware('auth:organizer')->name('organizer.pwa');
Route::post('organizer/check-qrcode/', 'BackEnd\Organizer\OrganizerController@check_qrcode')->middleware('auth:organizer')->name('check-qrcode');
Route::get('organizers/email/verify', 'BackEnd\Organizer\OrganizerController@confirm_email');

Route::prefix('/organizer')->group(function () {
  Route::middleware('guest:organizer', 'change.lang', 'adminLang')->group(function () {
    Route::get('/login', 'BackEnd\Organizer\OrganizerController@login')->name('organizer.login');
    Route::get('/signup', 'BackEnd\Organizer\OrganizerController@signup')->name('organizer.signup');
    Route::post('/create', 'BackEnd\Organizer\OrganizerController@create')->name('organizer.create');
    Route::post('/store', 'BackEnd\Organizer\OrganizerController@authentication')->middleware('throttle:5,1')->name('organizer.authentication');
    Route::get('/forget-password', 'BackEnd\Organizer\OrganizerController@forget_passord')->name('organizer.forget.password');
    Route::post('/send-forget-mail', 'BackEnd\Organizer\OrganizerController@forget_mail')->middleware('throttle:5,1')->name('organizer.forget.mail');
    Route::get('/reset-password', 'BackEnd\Organizer\OrganizerController@reset_password')->name('organizer.reset.password');
    Route::post('/update-forget-password', 'BackEnd\Organizer\OrganizerController@update_password')->name('organizer.update-forget-password');
  });
});

Route::prefix('/organizer')->middleware('auth:organizer')->group(function () {
  Route::get('/logout', 'BackEnd\Organizer\OrganizerController@logout')->name('organizer.logout');
  Route::get('/change-password', 'BackEnd\Organizer\OrganizerController@change_password')->name('organizer.change.password');
  Route::post('/update-password', 'BackEnd\Organizer\OrganizerController@updated_password')->name('organizer.update_password');
});
