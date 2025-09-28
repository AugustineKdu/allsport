<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Development environment security settings
        if (app()->environment('local', 'development')) {
            // Disable secure cookie requirements in development
            config([
                'session.secure' => false,
                'session.same_site' => 'lax',
            ]);

            // Set trusted proxies for development
            config([
                'trustedproxy.proxies' => '*',
            ]);
        }
    }
}
