<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockSensitivePaths
{
    /**
     * Rutas y patrones de archivos bloqueados.
     */
    private array $blockedPatterns = [
        '/^\.git\/',
        '/^\.env$/',
        '/\.(sql|gz|tar|zip|bak|backup|old|dump|pem|key|crt|p12|pfx)$/i',
        '/^(composer\.lock|package-lock\.json|yarn\.lock)$/i',
    ];

    /**
     * Headers de seguridad HTTP.
     */
    private array $securityHeaders = [
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();

        foreach ($this->blockedPatterns as $pattern) {
            if (preg_match($pattern, $path)) {
                abort(403, 'Acceso denegado.');
            }
        }

        $response = $next($request);

        // Solo agregar headers en respuestas HTTP (no en streams/downloads)
        if (method_exists($response, 'header')) {
            foreach ($this->securityHeaders as $header => $value) {
                $response->header($header, $value);
            }

            // HSTS solo si la conexión es HTTPS
            if ($request->isSecure()) {
                $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            }
        }

        return $response;
    }
}
