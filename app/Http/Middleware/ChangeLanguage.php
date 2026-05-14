<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class ChangeLanguage
{
  public function handle($request, Closure $next)
  {
    App::setLocale('es');
    Session::put('lang', 'es');
    return $next($request);
  }
}
