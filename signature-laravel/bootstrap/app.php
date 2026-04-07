<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust proxies (HTTPS / X-Forwarded-* behind nginx, Cloudflare, load balancers) — fixes mixed URL/session issues on Safari and mobile
        $middleware->trustProxies(at: '*');
        // Serve /storage/* via Laravel when nginx rewrites (fixes 403 Forbidden)
        $middleware->web(prepend: [\App\Http\Middleware\ServeStorageFromQuery::class]);
        // Register Spatie Permission middleware aliases
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            Log::warning('Post body exceeded server limit', [
                'url' => $request->fullUrl(),
                'content_length' => $request->header('Content-Length'),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The form data is too large. Reduce text or number of machines, or ask the host to raise PHP post_max_size and max_input_vars.',
                ], 413);
            }

            return redirect()->back()->withInput()->with(
                'error',
                'The form could not be submitted because it is too large for the server. Try shortening terms text or removing a machine row, or contact support to increase upload limits.'
            );
        });
    })->create();
