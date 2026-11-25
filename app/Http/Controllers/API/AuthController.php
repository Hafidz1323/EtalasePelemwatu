<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $user = User::whereRaw('LOWER(email) = ?', [strtolower(trim($data['email']))])->first();

        // Hanya akun dengan role 'admin' yang boleh login via endpoint admin ini.
        // Warga menggunakan POST /api/warga/login.
        if (! $user || $user->role !== 'admin' || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau password salah',
            ], 401);
        }

        // Batasi maksimal 5 token aktif per user: hapus token terlama jika sudah mencapai atau melebihi 5
        $excess = $user->tokens()->count() - 4;
        if ($excess > 0) {
            $ids = $user->tokens()->oldest()->limit($excess)->pluck('id');
            $user->tokens()->whereIn('id', $ids)->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'token' => $user->createToken('api-token')->plainTextToken,
                'user' => $user,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        // Hanya cabut token yang dipakai sekarang (perangkat lain tetap login).
        // Sesi non-token (TransientToken) tidak punya delete(), jadi dicek dulu.
        $token = $request->user()->currentAccessToken();
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil',
        ]);
    }

    public function verify(Request $request)
    {
        // Sampai di sini berarti token sudah divalidasi middleware auth:sanctum.
        return response()->json([
            'status' => 'success',
            'message' => 'Sesi aktif',
            'data' => ['user' => $request->user()],
        ]);
    }
}
