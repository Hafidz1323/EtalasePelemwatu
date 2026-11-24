<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class WargaProfileController extends Controller
{
    /**
     * Pastikan yang mengakses adalah warga.
     */
    private function requireWarga(Request $request)
    {
        $user = $request->user();
        if (! $user || $user->role !== 'warga') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        return null;
    }

    /**
     * GET /api/warga/profile
     * Ambil profil warga yang sedang login.
     */
    public function show(Request $request)
    {
        if ($deny = $this->requireWarga($request)) {
            return $deny;
        }

        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * POST /api/warga/profile
     * Perbarui nama dan/atau nomor HP warga.
     */
    public function update(Request $request)
    {
        if ($deny = $this->requireWarga($request)) {
            return $deny;
        }

        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', 'regex:/^08[1-9][0-9]{7,10}$/', 'unique:users,phone,'.$user->id],
            'email' => ['sometimes', 'nullable', 'email:rfc', 'max:255', 'unique:users,email,'.$user->id],
        ], [
            'phone.regex' => 'Nomor HP tidak valid. Contoh: 081234567890.',
            'phone.unique' => 'Nomor HP sudah dipakai akun lain.',
            'email.unique' => 'Email sudah dipakai akun lain.',
        ]);

        $user->fill($data)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * PUT /api/warga/password
     * Ganti password warga.
     */
    public function changePassword(Request $request)
    {
        if ($deny = $this->requireWarga($request)) {
            return $deny;
        }

        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password lama tidak sesuai.'],
            ]);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        // Cabut semua token lain
        $currentTokenId = null;
        $accessToken = $user->currentAccessToken();
        if ($accessToken && isset($accessToken->id)) {
            $currentTokenId = $accessToken->id;
        }

        if ($currentTokenId !== null) {
            $user->tokens()->where('id', '!=', $currentTokenId)->delete();
        } else {
            $user->tokens()->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diperbarui.',
        ]);
    }
}
