<?php

use App\Http\Controllers\FrontEnd\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/csrf-token', function () {
  return response()
    ->json(['token' => csrf_token()])
    ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
})->middleware('throttle:60,1')->name('csrf-token');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/robots.txt', function () {
  return response()->file(public_path('robots.txt'), [
    'Content-Type' => 'text/plain; charset=UTF-8',
  ]);
})->name('robots');

Route::redirect('/privacy-policy', '/politica-de-privacidad', 301);
Route::redirect('/terms-&-conditions', '/terminos-y-condiciones', 301);

require __DIR__ . '/frontend_auth.php';
require __DIR__ . '/frontend_customer.php';
require __DIR__ . '/frontend_events.php';
require __DIR__ . '/frontend_payments.php';
require __DIR__ . '/frontend_shop.php';
require __DIR__ . '/frontend_event_addons.php';
require __DIR__ . '/frontend_pages.php';
require __DIR__ . '/frontend_fallback.php';
