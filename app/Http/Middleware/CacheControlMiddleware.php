<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheControlMiddleware
{
  public function handle(Request $request, Closure $next): Response
  {
    $response = $next($request);
    $path = $request->path();

    if ($request->is('css/*') || $request->is('js/*') || $request->is('fonts/*')) {
      $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
    } elseif (preg_match('/\.(jpg|jpeg|png|gif|webp|svg|ico)$/i', $path)) {
      $response->headers->set('Cache-Control', 'public, max-age=604800');
    } elseif (str_starts_with($path, 'assets/')) {
      $response->headers->set('Cache-Control', 'public, max-age=604800');
      $response->headers->set('ETag', '"' . md5($response->getContent()) . '"');
    }

    return $response;
  }
}
