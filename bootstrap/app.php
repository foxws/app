<?php

declare(strict_types=1);

use Domain\Users\Commands\CreateUserCommand;
use Foundation\Http\Middlewares\AddCspHeaders;
use Foundation\Http\Middlewares\AddTelescopeCspNonce;
use Foundation\Http\Middlewares\EnsureRequestHasPrivateSubnet;
use Foundation\Http\Middlewares\SetCacheHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Spatie\ResponseCache\Middlewares\CacheResponse;
use Spatie\ResponseCache\Middlewares\DoNotCacheResponse;
use Support\Inertia\Middlewares\HandleInertiaRequests;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust Proxies configuration
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_AWS_ELB,
        );

        // Global middleware aliases for convenient usage in routes and controllers
        $middleware->alias([
            'cache' => SetCacheHeaders::class,
            'cache.bypass' => DoNotCacheResponse::class,
            'csp.telescope' => AddTelescopeCspNonce::class,
            'private' => EnsureRequestHasPrivateSubnet::class,
            'precognitive' => HandlePrecognitiveRequests::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'role' => RoleMiddleware::class,
        ]);

        // Configure web middleware
        $middleware->web(append: [
            AddCspHeaders::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Configure API middleware
        $middleware->api(append: [
            CacheResponse::class,
        ]);

        $middleware->throttleWithRedis();
        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })
    ->withEvents(discover: [
        // domain_path('*/Listeners'),
    ])
    ->withCommands([
        CreateUserCommand::class,
    ])
    ->create();
