<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline security headers Laravel doesn't set by default. Kept
 * deliberately conservative — a strict Content-Security-Policy is not
 * included here because this app relies on a handful of small inline
 * <script> blocks (Alpine component definitions in product/product-form
 * pages); locking those down needs moving them into external files
 * first, which is a Phase 6 deployment-hardening task, not a Phase 5 one.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}
