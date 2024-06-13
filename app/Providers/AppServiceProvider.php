<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::enablePasswordGrant();

        RateLimiter::for('api', function ($request) {
            $user = $request->user();
            $limit = 120;

            $key = $user?->id ?: $request->ip();
            $limit = Limit::perMinute($limit)->by($key);

            // Log permintaan yang mencapai batas
            if (RateLimiter::tooManyAttempts($key, $limit->maxAttempts)) {
                \Log::warning("Rate limit exceeded for key: $key");
            }

            return $limit;
        });
    }
}
