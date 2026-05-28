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

    $scheme = $this->resolveScheme($request);
    $wwwUrl = $scheme . '://www.tukipass.com' . $request->getRequestUri();

    return redirect()->away($wwwUrl, 301);
  }

  private function resolveScheme(Request $request): string
  {
    if ($request->isSecure()) {
      return 'https';
    }

    $forwardedProto = strtolower((string) $request->header('X-Forwarded-Proto', ''));
    if ($forwardedProto === 'https' || str_contains($forwardedProto, 'https')) {
      return 'https';
    }

    if (app()->environment('production')) {
      return 'https';
    }

    return $request->getScheme();
  }
}
