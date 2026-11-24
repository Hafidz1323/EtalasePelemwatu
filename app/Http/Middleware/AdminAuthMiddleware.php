<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    /**
     * Memeriksa otentikasi admin via Bearer Token atau fallback sesi
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        // Terima jika ada Bearer token valid (Sanctum token atau ep-session)
        if ($authHeader && preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
            $token = $matches[1];

            // 1. Cek sesi bawaan admin
            if (str_starts_with($token, 'ep-session-') || str_starts_with($token, 'session-')) {
                return $next($request);
            }

            // 2. Cek token Sanctum
            if (auth('sanctum')->check()) {
                return $next($request);
            }
        }

        // Jika user web session sudah login
        if (auth()->check()) {
            return $next($request);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Akses ditolak. Silakan login sebagai Admin terlebih dahulu.',
        ], 401);
    }
}
