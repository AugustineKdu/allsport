<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force HTTPS only in production environment
        if (app()->environment('production') &&
            env('FORCE_HTTPS', true) &&
            !$request->secure()) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        // In development, set secure headers to prevent warnings
        if (app()->environment('local', 'development')) {
            $request->server->set('HTTPS', 'on');
            $request->server->set('SERVER_PORT', '443');
        }

        return $next($request);
    }
}
