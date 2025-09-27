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
        // Be more lenient with CSRF for cloud deployment
        try {
            return parent::handle($request, $next);
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            // If CSRF fails, regenerate token and redirect back with clear message
            $request->session()->regenerateToken();

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'CSRF token mismatch. Please refresh and try again.',
                    'csrf_token' => csrf_token()
                ], 419);
            }

            return redirect()->back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('warning', '보안 토큰이 만료되었습니다. 다시 시도해주세요.');
        }
    }
}