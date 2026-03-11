<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Increase the default API rate limit to 1000 requests per minute
        // This should prevent the "Too Many Requests" error for normal usage
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(1000)->by($request->user()?->id ?: $request->ip());
        });

        // Also configure the web throttle if needed (for login attempts)
        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(1000)->by($request->user()?->id ?: $request->ip());
        });
    }
}
