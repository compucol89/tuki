<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToWww
{
  public function handle(Request $request, Closure $next): Response
  {
    if ($request->getHost() !== 'tukipass.com') {
      return $next($request);
    }

    $wwwUrl = str_replace(
      $request->getSchemeAndHttpHost(),
      $request->getScheme() . '://www.' . $request->getHost(),
      $request->fullUrl()
    );

    return redirect()->away($wwwUrl, 301);
  }
}
