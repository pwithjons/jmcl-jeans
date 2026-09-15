<?php

use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Custom middleware aliases used across the storefront + admin panel.
        $middleware->alias([
            'role' => EnsureAdminRole::class,
        ]);

        // Applied to every request — see SecurityHeaders' docblock for
        // why this doesn't include a strict CSP yet.
        $middleware->append(SecurityHeaders::class);

        // `auth:admin` / `guest:admin` (Laravel's built-in Authenticate and
        // RedirectIfAuthenticated middleware) already respect the guard
        // passed to them, so no separate admin middleware classes are
        // needed — see routes/admin.php.
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
