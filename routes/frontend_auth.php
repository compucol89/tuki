<?php

use Illuminate\Support\Facades\Route;

Route::get('/offline', 'FrontEnd\HomeController@offline');

Route::get('/check-payment', 'CronJobController@checkIyzicoPendingPayment')->middleware('throttle:10,1')->name('cron.check.payment');
Route::get('/send-ticket', 'CronJobController@sendTicket')->middleware('throttle:10,1')->name('cron.send.ticket');

Route::get('midtrans/cancel', 'FrontEnd\HomeController@midtrans_cancel')->name('midtrans_cancel');
Route::post('xendit/callback', 'FrontEnd\HomeController@xendit_callback')->name('xendit_cancel');
Route::get('myfatoorah/callback', 'FrontEnd\HomeController@myfatoorah_callback')->name('myfatoorah_callback');
Route::get('myfatoorah/cancel', 'FrontEnd\HomeController@myfatoorah_cancel')->name('myfatoorah_cancel');

Route::get('login/facebook/callback', 'FrontEnd\CustomerController@handleFacebookCallback');
Route::get('login/google/callback', 'FrontEnd\CustomerController@handleGoogleCallback');

Route::middleware('change.lang')->group(function () {
  Route::middleware('guest:customer')->group(function () {
    Route::get('/login', 'FrontEnd\CustomerController@login')->name('customer.login');
    Route::get('/registro', 'FrontEnd\CustomerController@signup')->name('customer.signup');
    Route::get('/recuperar-contrasena', 'FrontEnd\CustomerController@forget_passord')->name('customer.forget.password');
  });

  Route::middleware('guest:customer')->prefix('customer')->group(function () {
    Route::post('/create', 'FrontEnd\CustomerController@create')->name('customer.create');
    Route::post('/store', 'FrontEnd\CustomerController@authentication')->middleware('throttle:5,1')->name('customer.authentication');
    Route::get('auth/facebook', 'FrontEnd\CustomerController@facebookRedirect')->name('auth.facebook');
    Route::get('auth/google', 'FrontEnd\CustomerController@googleRedirect')->name('auth.google');
    Route::post('/send-forget-mail', 'FrontEnd\CustomerController@forget_mail')->middleware('throttle:5,1')->name('customer.forget.mail');
    Route::get('/reset-password', 'FrontEnd\CustomerController@reset_password')->name('customer.reset.password');
    Route::post('/update-forget-password', 'FrontEnd\CustomerController@update_password')->name('customer.update-forget-password');
  });

  Route::get('/customer/login', function () {
    return redirect()->route('customer.login', request()->query(), 301);
  });
  Route::get('/customer/signup', function () {
    return redirect()->route('customer.signup', request()->query(), 301);
  });
  Route::get('/customer/forget-password', function () {
    return redirect()->route('customer.forget.password', request()->query(), 301);
  });
  Route::get('/cliente/login', function () {
    return redirect()->route('customer.login', request()->query(), 301);
  })->name('cliente.login');
  Route::get('/cliente/registro', function () {
    return redirect()->route('customer.signup', request()->query(), 301);
  })->name('cliente.registro');
  Route::get('/cliente/olvide-contrasena', function () {
    return redirect()->route('customer.forget.password', request()->query(), 301);
  })->name('cliente.olvide-contrasena');
  Route::get('/cliente/restablecer-contrasena', function () {
    return redirect()->route('customer.reset.password', request()->query(), 301);
  })->name('cliente.restablecer-contrasena');
});

Route::middleware('auth:customer', 'change.lang')->prefix('/customer')->group(function () {
  Route::get('/logout', 'FrontEnd\CustomerController@logout')->name('customer.logout');
  Route::get('/change-password', 'FrontEnd\CustomerController@change_password')->name('customer.change.password');
  Route::post('/update-password', 'FrontEnd\CustomerController@updated_password')->name('customer.password.update');
});

Route::get('customer/signup-verify/{token}', 'FrontEnd\CustomerController@signupVerify')->withoutMiddleware('change.lang');

Route::prefix('/admin')->middleware('guest:admin')->group(function () {
  Route::get('/', 'BackEnd\AdminController@login')->name('admin.login');
  Route::post('/auth', 'BackEnd\AdminController@authentication')->middleware('throttle:5,1')->name('admin.auth');
  Route::get('/forget-password', 'BackEnd\AdminController@forgetPassword')->name('admin.forget_password');
  Route::post('/mail-for-forget-password', 'BackEnd\AdminController@sendMail')->middleware('throttle:5,1')->name('admin.mail_for_forget_password');
});
