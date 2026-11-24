<?php

namespace App\Providers;

use App\Http\Controllers\API\ProfilDesaController;
use App\Models\Setting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
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
        // View composer untuk profil desa di halaman publik
        View::composer(['index', 'daftar-potensi'], function ($view) {
            $desa = Cache::remember('profil_desa_settings', 600, function () {
                $keys = array_keys(ProfilDesaController::DEFAULTS);
                $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');

                $data = [];
                foreach (ProfilDesaController::DEFAULTS as $key => $default) {
                    $val = $settings[$key] ?? null;
                    $shortKey = str_replace('desa_', '', $key);
                    $data[$shortKey] = ($val !== null && $val !== '') ? $val : $default;
                }

                $rawWa = $data['whatsapp'] ?? '';
                $displayWa = preg_replace('/^62/', '0', $rawWa);
                if (strlen($displayWa) >= 10) {
                    $data['whatsapp_display'] = substr($displayWa, 0, 4).'-'.substr($displayWa, 4, 4).'-'.substr($displayWa, 8);
                } else {
                    $data['whatsapp_display'] = $displayWa;
                }

                return $data;
            });

            $view->with('desa', $desa);
        });

        // Rate limiter login: dibatasi per email (lowercase) + IP, 5 percobaan/menit.
        // Kunci per-email memastikan throttle email A tidak memblokir email B dari IP yang sama.
        RateLimiter::for('login', function (Request $request) {
            $email = $request->input('email', '');
            $key = strtolower(is_string($email) ? $email : '').'|'.$request->ip();

            return Limit::perMinute(5)
                ->by($key)
                ->response(function () {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Terlalu banyak percobaan login. Coba lagi nanti.',
                    ], 429);
                });
        });

        // Rate limiter login warga: dibatasi per login (email/HP) + IP, 5 percobaan/menit.
        RateLimiter::for('warga-login', function (Request $request) {
            $login = $request->input('login', '');
            $key = strtolower(is_string($login) ? $login : '').'|'.$request->ip();

            return Limit::perMinute(5)
                ->by($key)
                ->response(function () {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Terlalu banyak percobaan login. Coba lagi nanti.',
                    ], 429);
                });
        });
    }
}
