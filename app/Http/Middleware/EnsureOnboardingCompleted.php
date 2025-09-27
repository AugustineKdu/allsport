<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->onboarding_done) {
            // Allow access to onboarding routes, logout, and profile edit
            if ($request->routeIs('onboarding.*') ||
                $request->routeIs('logout') ||
                $request->routeIs('profile.*')) {
                return $next($request);
            }

            // If user has minimal info but hasn't completed onboarding, still allow access
            $user = auth()->user();
            if (!$user->nickname || !$user->city || !$user->selected_sport) {
                // Show onboarding reminder but allow access
                session()->flash('onboarding_reminder', '프로필 설정을 완료하면 더 많은 기능을 이용할 수 있습니다.');
            }
        }

        return $next($request);
    }
}
