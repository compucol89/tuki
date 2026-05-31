<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next)
    {
        App::setLocale('admin');

        return $next($request);
    }
}
