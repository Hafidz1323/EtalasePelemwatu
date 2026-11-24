<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdminPageMiddleware
 *
 * Melindungi halaman admin Blade dari akses tanpa login.
 * Memeriksa Bearer token dari header Authorization atau cookie ep-token.
 * Jika token tidak valid atau bukan admin, redirect ke halaman login.
 */
class AdminPageMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = null;

        // 1. Cek dari header Authorization: Bearer <token>
        $authHeader = $request->header('Authorization', '');
        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
        }

        // 2. Fallback: cek dari cookie ep-token (dikirim oleh JS setelah login)
        if (!$token) {
            $token = $request->cookie('ep-token');
        }

        // 3. Validasi token
        if ($token) {
            $pat = PersonalAccessToken::findToken($token);
            if ($pat && $pat->tokenable) {
                $user = $pat->tokenable;
                if ($user->role === 'admin') {
                    return $next($request);
                }
                // Login tapi bukan admin → redirect ke beranda
                return redirect('/')->with('error', 'Akses ditolak. Halaman ini hanya untuk admin.');
            }
        }

        // Tidak ada token atau token tidak valid → redirect ke login
        return redirect('/login')->with('info', 'Silakan login sebagai admin terlebih dahulu.');
    }
}
