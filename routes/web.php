<?php

use App\Http\Controllers\FrontEnd\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

require __DIR__ . '/frontend_auth.php';
require __DIR__ . '/frontend_customer.php';
require __DIR__ . '/frontend_events.php';
require __DIR__ . '/frontend_payments.php';
require __DIR__ . '/frontend_shop.php';
require __DIR__ . '/frontend_pages.php';
require __DIR__ . '/frontend_fallback.php';
