<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class ChangeLanguage
{
  /**
   * Handle an incoming request.
   * Site is configured to always use Spanish.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    $spanishLanguage = Language::where('code', 'es')->first();

    if ($spanishLanguage) {
      Session::put('lang', 'es');
      App::setLocale('es');
    } else {
      if ($request->session()->has('lang')) {
        $locale = $request->session()->get('lang');
      }
      if (empty($locale)) {
        $language = Language::where('is_default', 1)->first();
        $languageCode = $language ? $language->code : 'en';
        App::setLocale($languageCode);
      } else {
        App::setLocale($locale);
      }
    }

    return $next($request);
  }
}
