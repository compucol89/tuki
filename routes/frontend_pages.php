<?php

use Illuminate\Support\Facades\Route;

Route::get('/change-language', 'Controller@changeLanguage')->name('change_language');
Route::post('/store-subscriber', 'Controller@storeSubscriber')->name('store_subscriber');

Route::middleware('change.lang')->group(function () {
  Route::get('/blog', 'FrontEnd\BlogController@blogs')->name('blogs');
  Route::get('/blog/{slug}', 'FrontEnd\BlogController@details')->name('blog_details');
  Route::redirect('/faq', '/preguntas-frecuentes', 301);
  Route::get('/preguntas-frecuentes', 'FrontEnd\FaqController@faqs')->name('faqs');
  Route::get('/contacto', 'FrontEnd\ContactController@contact')->name('contact');
});

Route::post('/contact/send-mail', 'FrontEnd\ContactController@sendMail')->name('contact.send_mail');
Route::post('/advertisement/{id}/total-view', 'Controller@countAdView');
Route::get('/service-unavailable', 'Controller@serviceUnavailable')->name('service_unavailable')->middleware('exists.down');
