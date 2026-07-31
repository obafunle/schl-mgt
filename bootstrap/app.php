<?php

/**
 * Laravel Application Bootstrapper
 *
 * This file is the entry point for configuring the Laravel application.
 * It sets up routing, middleware, service providers, and exception handling.
 *
 * In Laravel 11+, this file replaces the old Kernel.php and other bootstrap files.
 */

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    /* ============================================================
       ROUTING CONFIGURATION
       ============================================================ */
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',      // Web routes (browser)
        commands: __DIR__ . '/../routes/console.php', // Artisan commands
        health: '/up',                            // Health check endpoint
    )
    /* ============================================================
       MIDDLEWARE CONFIGURATION
       ============================================================ */
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware for web requests
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Register middleware aliases (for use in routes)
        $middleware->alias([
            // Laravel Permission package middleware
            'permission' => PermissionMiddleware::class,
            'role' => RoleMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,

            // Custom middleware (if any)
            // 'auth.custom' => \App\Http\Middleware\CustomAuthMiddleware::class,
        ]);
    })
    /* ============================================================
       EXCEPTION HANDLING
       ============================================================ */
    ->withExceptions(function (Exceptions $exceptions): void {
        // Automatically render JSON responses for API requests
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*'),
        );
    })
    /* ============================================================
       CREATE THE APPLICATION INSTANCE
       ============================================================ */
    ->create();
