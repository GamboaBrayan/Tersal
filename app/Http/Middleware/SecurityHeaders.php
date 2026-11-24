<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('X-Frame-Options', 'SAMEORIGIN');
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('X-XSS-Protection', '1; mode=block');
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
            
            // Allow Vite dev server in local environment by not sending strict CSP
            if (app()->environment('production')) {
                $csp = implode('; ', [
                    "default-src 'self'",
                    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com https://www.google-analytics.com",
                    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
                    "font-src 'self' data: https://fonts.gstatic.com",
                    "img-src 'self' data: blob: https:",
                    "connect-src 'self' https://www.google-analytics.com https://analytics.google.com https://www.googletagmanager.com",
                    "frame-src 'self' https://www.googletagmanager.com",
                    "object-src 'none'",
                    "base-uri 'self'",
                    "form-action 'self'",
                ]);
                $response->header('Content-Security-Policy', $csp);
            }
        }

        return $response;
    }
}
