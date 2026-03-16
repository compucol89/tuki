<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/organizer')->middleware('auth:organizer', 'Deactive:organizer', 'EmailStatus:organizer', 'adminLang')->group(function () {
  Route::get('withdraw', 'BackEnd\Organizer\OrganizerWithdrawController@index')->name('organizer.withdraw');
  Route::get('withdraw/create', 'BackEnd\Organizer\OrganizerWithdrawController@create')->name('organizer.withdraw.create');
  Route::get('/get-withdraw-method/input/{id}', 'BackEnd\Organizer\OrganizerWithdrawController@get_inputs');
  Route::get('withdraw/balance-calculation/{method}/{amount}', 'BackEnd\Organizer\OrganizerWithdrawController@balance_calculation');
  Route::post('/withdraw/send-request', 'BackEnd\Organizer\OrganizerWithdrawController@send_request')->name('organizer.withdraw.send-request');
  Route::post('/withdraw/witdraw/bulk-delete', 'BackEnd\Organizer\OrganizerWithdrawController@bulkDelete')->name('organizer.witdraw.bulk_delete_withdraw');
  Route::post('/withdraw/witdraw/delete', 'BackEnd\Organizer\OrganizerWithdrawController@Delete')->name('organizer.witdraw.delete_withdraw');
});
