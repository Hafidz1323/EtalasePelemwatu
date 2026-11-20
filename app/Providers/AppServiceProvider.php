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
        // Rate limiter login: dibatasi per email (lowercase) + IP, 5 percobaan/menit.
        // Kunci per-email memastikan throttle email A tidak memblokir email B dari IP yang sama.
        RateLimiter::for('login', function (Request $request) {
            $email = $request->input('email', '');
            $key   = strtolower(is_string($email) ? $email : '') . '|' . $request->ip();

            return Limit::perMinute(5)
                ->by($key)
                ->response(function () {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Terlalu banyak percobaan login. Coba lagi nanti.',
                    ], 429);
                });
        });
    }
}
