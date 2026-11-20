<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Laravel\Sanctum\PersonalAccessToken;

class WargaAuthController extends Controller
{
    /**
     * POST /api/warga/register
     * Daftarkan akun warga baru. Auto-login: kembalikan token setelah registrasi.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'min:3', 'max:100'],
            'email'                 => ['nullable', 'email:rfc', 'max:100', 'unique:users,email'],
            'phone'                 => ['required', 'regex:/^08[1-9][0-9]{7,10}$/', 'unique:users,phone'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ], [
            'name.required'    => 'Nama lengkap wajib diisi.',
            'name.min'         => 'Nama minimal 3 karakter.',
            'email.email'      => 'Format email tidak valid.',
            'email.unique'     => 'Email ini sudah terdaftar.',
            'phone.required'   => 'Nomor HP wajib diisi.',
            'phone.regex'      => 'Nomor HP tidak valid. Contoh: 08123456789.',
            'phone.unique'     => 'Nomor HP ini sudah terdaftar.',
            'password.min'     => 'Password minimal 8 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'] ?? null,
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
            'role'     => 'warga',
        ]);

        $token = $user->createToken('warga-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Akun berhasil dibuat.',
            'data'    => [
                'token' => $token,
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role'  => $user->role,
                ],
            ],
        ], 201);
    }

    /**
     * POST /api/warga/login
     * Login dengan nomor HP atau email.
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'login'    => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ], [
            'login.required'    => 'Email atau nomor HP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $login = trim($data['login']);

        // Deteksi: input nomor HP atau email
        $user = null;
        if (preg_match('/^(08|\+62)/', $login)) {
            // Normalisasi: +62 → 0
            $phone = preg_replace('/^\+?62(?=8)/', '0', preg_replace('/[\s\-()]/', '', $login));
            $user = User::where('phone', $phone)->where('role', 'warga')->first();
        } else {
            $user = User::whereRaw('LOWER(email) = ?', [strtolower($login)])
                        ->where('role', 'warga')
                        ->first();
        }

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Nomor HP/email atau password salah.',
            ], 401);
        }

        $token = $user->createToken('warga-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil.',
            'data'    => [
                'token' => $token,
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role'  => $user->role,
                ],
            ],
        ]);
    }

    /**
     * POST /api/warga/logout
     */
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json(['status' => 'success', 'message' => 'Logout berhasil.']);
    }

    /**
     * GET /api/warga/verify
     */
    public function verify(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'warga') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role'  => $user->role,
                ],
            ],
        ]);
    }
}
