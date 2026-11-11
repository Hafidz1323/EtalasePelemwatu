<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Simple login that checks against users table or fallback admin.
     */
    public function login(Request $request)
    {
        $email = strtolower(trim($request->input('email')));
        $password = $request->input('password');

        // Try to find user
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user && Hash::check($password, $user->password)) {
            $token = $user->createToken('api-token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'data' => [
                    'token' => $token,
                    'user' => $user,
                ],
            ]);
        }

        // Fallback default admin (hardcoded as in original Node.js)
        $default = [
            'id' => 1,
            'name' => 'Administrator Desa',
            'email' => 'admin@pelemwatu.desa.id',
            'role' => 'Admin Desa',
            'password' => 'admin123',
        ];
        if ($email === strtolower($default['email']) && $password === $default['password']) {
            $token = 'ep-session-'.$default['id'].'-'.time();
            return response()->json([
                'status' => 'success',
                'message' => 'Login admin berhasil',
                'data' => [
                    'token' => $token,
                    'user' => $default,
                ],
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Email atau password salah',
        ], 401);
    }

    public function logout(Request $request)
    {
        // If token based, revoke all tokens for user
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil',
        ]);
    }

    public function verify(Request $request)
    {
        // Simple verification: check Authorization header format
        $auth = $request->header('Authorization');
        if ($auth && Str::startsWith($auth, 'Bearer ')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Sesi aktif',
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Sesi tidak valid',
        ], 401);
    }
}

