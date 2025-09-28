<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 사용자가 로그인되어 있는지 확인
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', '로그인이 필요합니다.');
        }

        // 사용자가 관리자인지 확인
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('home')->with('error', '관리자 권한이 필요합니다.');
        }

        return $next($request);
    }
}
