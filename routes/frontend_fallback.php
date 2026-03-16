<?php

use Illuminate\Support\Facades\Route;

Route::get('/{slug}', 'FrontEnd\PageController@page')->name('dynamic_page')->middleware('change.lang');

Route::fallback(function () {
  return view('errors.404');
})->middleware('change.lang');
