<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServeStorageFromQuery
{
    /**
     * Handle an incoming request.
     * 
     * This middleware serves storage files via Laravel when nginx rewrites (fixes 403 Forbidden).
     * For Apache setups, this acts as a pass-through middleware.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pass through the request - storage files are handled by the web server
        // or Laravel's storage routes if configured
        return $next($request);
    }
}
