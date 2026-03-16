<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/organizer')->middleware('auth:organizer', 'Deactive:organizer', 'EmailStatus:organizer', 'adminLang')->group(function () {
  Route::get('/dashboard', 'BackEnd\Organizer\OrganizerController@index')->name('organizer.dashboard');
  Route::get('monthly-income', 'BackEnd\Organizer\OrganizerController@monthly_income')->name('organizer.monthly_income');
  Route::get('/transaction', 'BackEnd\Organizer\OrganizerController@transaction')->name('organizer.transcation');
  Route::post('/transcation/delete', 'BackEnd\Organizer\OrganizerController@destroy')->name('organizer.transcation.delete');
  Route::post('/transcation/bulk-delete', 'BackEnd\Organizer\OrganizerController@bulk_destroy')->name('organizer.transcation.bulk_delete');
  Route::post('/change-theme', 'BackEnd\Organizer\OrganizerController@changeTheme')->name('organizer.change_theme');
  Route::get('/edit-profile', 'BackEnd\Organizer\OrganizerController@edit_profile')->name('organizer.edit.profile');
  Route::post('/organizer-update-profile', 'BackEnd\Organizer\OrganizerController@update_profile')->name('organizer.update_profile');
  Route::get('/verify/email', 'BackEnd\Organizer\OrganizerController@verify_email')->name('organizer.verify.email');
  Route::post('/send-verify/link', 'BackEnd\Organizer\OrganizerController@send_link')->name('organizer.send.verify.link');
  Route::get('/email/verify', 'BackEnd\Organizer\OrganizerController@confirm_email');
});
