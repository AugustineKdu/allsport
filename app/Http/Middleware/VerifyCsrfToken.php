<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // In development/local environment, be more lenient with CSRF
        if (app()->environment(['local', 'development']) || config('app.debug')) {
            // Still run CSRF but with more relaxed settings
            try {
                return parent::handle($request, $next);
            } catch (\Illuminate\Session\TokenMismatchException $e) {
                // If CSRF fails in debug mode, regenerate token and try again
                $request->session()->regenerateToken();
                return redirect()->back()->withInput()->with('warning', 'Security token refreshed. Please try again.');
            }
        }

        return parent::handle($request, $next);
    }
}