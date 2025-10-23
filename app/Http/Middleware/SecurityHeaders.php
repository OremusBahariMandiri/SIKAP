<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Dapatkan headers dari config
        $headers = config('security.headers', []);

        // Set security headers
        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        // Strict-Transport-Security (HSTS) untuk HTTPS
        if ($request->secure() || config('security.https.force_https')) {
            $maxAge = config('security.https.hsts_max_age', 31536000);
            $response->headers->set(
                'Strict-Transport-Security',
                "max-age={$maxAge}; includeSubDomains; preload"
            );
        }

        // Content-Security-Policy (CSP)
        // HANYA aktif di production untuk menghindari masalah development
        if (config('app.env') !== 'local' || config('security.csp_enabled', false)) {
            $csp = $this->getContentSecurityPolicy();
            if ($csp) {
                $response->headers->set('Content-Security-Policy', $csp);
            }
        }

        // Remove server information
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }

    /**
     * Get Content Security Policy string
     */
    protected function getContentSecurityPolicy(): string
    {
        $isLocal = config('app.env') === 'local';

        // Policy berbeda untuk development dan production
        if ($isLocal) {
            // Development - lebih permisif untuk Vite HMR
            $policies = [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5173 https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.jquery.com",
                "style-src 'self' 'unsafe-inline' http://localhost:5173 https://fonts.googleapis.com https://cdn.jsdelivr.net",
                "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net data:",
                "img-src 'self' data: https: http: blob:",
                "connect-src 'self' http://localhost:5173 ws://localhost:5173 https://cdn.jsdelivr.net",
                "frame-ancestors 'self'",
                "base-uri 'self'",
                "form-action 'self'",
            ];
        } else {
            // Production - lebih ketat tapi tetap allow CDN
            $policies = [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.jquery.com",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
                "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net data:",
                "img-src 'self' data: https: blob:",
                "connect-src 'self' https://cdn.jsdelivr.net",
                "frame-ancestors 'self'",
                "base-uri 'self'",
                "form-action 'self'",
            ];
        }

        return implode('; ', $policies);
    }
}