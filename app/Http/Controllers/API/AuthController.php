<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
<<<<<<< HEAD
    /**
     * Registrasi Akun Warga Baru
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone'    => 'nullable|string|max:25',
            'address'  => 'nullable|string|max:500',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.unique'      => 'Email sudah terdaftar. Silakan gunakan email lain atau login.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal 6 karakter.',
            'password.confirmed'=> 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name'     => strip_tags(trim($validated['name'])),
            'email'    => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role'     => 'warga',
            'phone'    => $validated['phone'] ? preg_replace('/[^0-9+\-\s()]/', '', $validated['phone']) : null,
            'address'  => $validated['address'] ? strip_tags(trim($validated['address'])) : null,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Pendaftaran akun warga berhasil!',
            'data'    => [
                'token' => $token,
                'user'  => [
                    'id'      => $user->id,
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'role'    => $user->role,
                    'phone'   => $user->phone,
                    'address' => $user->address,
                ],
            ],
        ], 201);
    }

    /**
     * Login Pengguna (Bisa Akun Admin maupun Warga)
     */
    public function login(Request $request)
    {
        $inputUser = $request->input('email') ?: $request->input('username');
        $email = strtolower(trim((string)$inputUser));
        $password = (string)$request->input('password');

        if (!$email || !$password) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email dan kata sandi wajib diisi',
            ], 400);
        }

        // Cek di tabel users
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user && Hash::check($password, $user->password)) {
            $token = $user->createToken('auth-token')->plainTextToken;
            return response()->json([
                'status'  => 'success',
                'message' => 'Login berhasil',
                'data'    => [
                    'token' => $token,
                    'user'  => [
                        'id'      => $user->id,
                        'name'    => $user->name,
                        'email'   => $user->email,
                        'role'    => $user->role ?: 'warga',
                        'phone'   => $user->phone,
                        'address' => $user->address,
                    ],
                ],
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Email atau kata sandi salah',
        ], 401);
=======
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
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
    }

    /**
     * Lihat Profil Pengguna yang Sedang Login
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'user'    => $user,
                'potensi' => $user->potensi()->with('category', 'images')->get(),
            ],
        ]);
    }

    /**
     * Perbarui Profil Warga
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'name'    => 'sometimes|required|string|max:255',
            'phone'   => 'nullable|string|max:25',
            'address' => 'nullable|string|max:500',
            'password'=> 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($validated['name'])) {
            $user->name = strip_tags(trim($validated['name']));
        }
        if (array_key_exists('phone', $validated)) {
            $user->phone = $validated['phone']
                ? preg_replace('/[^0-9+\-\s()]/', '', $validated['phone'])
                : null;
        }
        if (array_key_exists('address', $validated)) {
            $user->address = $validated['address'] ? strip_tags(trim($validated['address'])) : null;
        }
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui',
            'data'    => $user,
        ]);
    }

    public function logout(Request $request)
    {
<<<<<<< HEAD
        if ($request->user()) {
            $request->user()->tokens()->delete();
=======
        // Hanya cabut token yang dipakai sekarang (perangkat lain tetap login).
        // Sesi non-token (TransientToken) tidak punya delete(), jadi dicek dulu.
        $token = $request->user()->currentAccessToken();
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil. Sesi telah diakhiri.',
        ]);
    }

    public function verify(Request $request)
    {
<<<<<<< HEAD
        $auth = $request->header('Authorization');
        if ($auth && Str::startsWith($auth, 'Bearer ')) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Sesi aktif',
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Sesi tidak valid',
        ], 401);
=======
        // Sampai di sini berarti token sudah divalidasi middleware auth:sanctum.
        return response()->json([
            'status' => 'success',
            'message' => 'Sesi aktif',
            'data' => ['user' => $request->user()],
        ]);
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
    }
}
