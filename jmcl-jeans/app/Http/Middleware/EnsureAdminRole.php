<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route-level role gate for the admin panel, e.g.:
 *   Route::middleware(['auth:admin', 'role:super_admin'])->group(...)
 *
 * This sits on top of (not instead of) the `auth:admin` guard check —
 * it only decides which *authenticated* admin can proceed, so it must
 * always be listed after `auth:admin` in a route's middleware chain.
 */
class EnsureAdminRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin || ! in_array($admin->role, $roles, true)) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
