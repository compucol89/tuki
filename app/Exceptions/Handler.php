<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (TokenMismatchException $e, Request $request) {
            $sessionCookie = (string) config('session.cookie');

            Log::warning('csrf_token_mismatch', [
                'method' => $request->method(),
                'path' => $request->path(),
                'route' => optional($request->route())->getName(),
                'ip' => $request->ip(),
                'referer' => $request->headers->get('referer'),
                'user_agent' => $request->userAgent(),
                'has_session_cookie' => $sessionCookie !== '' && $request->cookies->has($sessionCookie),
                'has_xsrf_cookie' => $request->cookies->has('XSRF-TOKEN'),
                'has_request_token' => $request->has('_token') || $request->headers->has('X-CSRF-TOKEN') || $request->headers->has('X-XSRF-TOKEN'),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tu sesión expiró. Actualizá la página e intentá nuevamente.',
                ], 419);
            }

            return response()->view('errors.419', [], 419);
        });
    }
}
