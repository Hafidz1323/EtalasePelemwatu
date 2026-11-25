<?php

use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\SecurityHeaders;
use Dotenv\Dotenv;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Env;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);

        $middleware->alias([
            'role' => EnsureRole::class,
        ]);

        // Muat .env lebih awal jika ada (selain environment testing), karena callback middleware dieksekusi sebelum kernel bootstrap.
        if (($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) !== 'testing' && class_exists(Dotenv::class)) {
            Dotenv::create(Env::getRepository(), dirname(__DIR__))->safeLoad();
        }

        // Percayai proxy tertentu berdasarkan env TRUSTED_PROXIES.
        // Isi dengan IP proxy (koma-pisah) atau '*' untuk semua, kosongkan jika tidak ada proxy.
        $trustedProxies = env('TRUSTED_PROXIES', '');
        if (! empty($trustedProxies)) {
            $middleware->trustProxies(
                at: $trustedProxies === '*' ? '*' : array_map('trim', explode(',', $trustedProxies))
            );
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
