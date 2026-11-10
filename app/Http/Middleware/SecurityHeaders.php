<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Vite;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menambahkan header keamanan HTTP ke setiap respons
 * (temuan scan OWASP ZAP: CSP, clickjacking, HSTS, X-Content-Type-Options).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Di production, paksa semua akses lewat HTTPS
        if (app()->isProduction() && ! $request->secure()) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        $response = $next($request);

        $headers = $response->headers;
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        if ($request->secure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Server dev Vite (npm run dev) memuat skrip dari host lain, jadi CSP dilewati saat itu
        if (! app(Vite::class)->isRunningHot()) {
            $headers->set('Content-Security-Policy', $this->csp());
        }

        return $response;
    }

    private function csp(): string
    {
        // 'unsafe-eval' dibutuhkan Alpine.js; 'unsafe-inline' untuk skrip & onclick inline di view
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
            "font-src 'self' data: https://fonts.bunny.net",
            "img-src 'self' data: blob: https://images.unsplash.com",
            "connect-src 'self'",
            "frame-src 'self'",
            "frame-ancestors 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
    }
}
