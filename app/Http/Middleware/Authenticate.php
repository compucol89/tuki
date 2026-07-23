<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
  /**
   * Get the path the user should be redirected to when they are not authenticated.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return string|null
   */
  protected function redirectTo($request)
  {
    if (!$request->expectsJson()) {
      if (Route::is('admin.*') || $request->is('admin') || $request->is('admin/*')) {
        return route('admin.login');
      }

      if (Route::is('user.*') || $request->is('user') || $request->is('user/*')) {
        return route('user.login');
      }
      if (Route::is('customer.*') || $request->is('customer') || $request->is('customer/*')) {
        return route('customer.login');
      }
      if (Route::is('organizer.*') || $request->is('organizer') || $request->is('organizer/*')) {
        return route('organizer.login');
      }
    }
  }
}
