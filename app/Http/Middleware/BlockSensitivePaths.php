<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockSensitivePaths
{
    /**
     * Prefijos de rutas bloqueadas.
     */
    private array $blockedPrefixes = [
        '.git/',
        '.env',
    ];

    /**
     * Extensiones de archivo bloqueadas.
     */
    private array $blockedExtensions = [
        'sql', 'gz', 'tar', 'zip', 'bak', 'backup',
        'old', 'dump', 'pem', 'key', 'crt', 'p12', 'pfx',
    ];

    /**
     * Nombres de archivo exactos bloqueados.
     */
    private array $blockedFiles = [
        'composer.lock',
        'package-lock.json',
        'yarn.lock',
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

        // Bloquear por prefijo
        foreach ($this->blockedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                abort(403, 'Acceso denegado.');
            }
        }

        // Bloquear por extension
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($extension, $this->blockedExtensions, true)) {
            abort(403, 'Acceso denegado.');
        }

        // Bloquear archivos exactos
        if (in_array($path, $this->blockedFiles, true)) {
            abort(403, 'Acceso denegado.');
        }

        $response = $next($request);

        // Agregar headers de seguridad
        if (method_exists($response, 'header')) {
            foreach ($this->securityHeaders as $header => $value) {
                $response->header($header, $value);
            }

            // HSTS solo en HTTPS
            if ($request->isSecure()) {
                $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            }
        }

        return $response;
    }
}
