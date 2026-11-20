<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * GET /api/admin/profile
     */
    public function show(Request $request)
    {
        $user = $request->user();
        if ($user && $user->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil admin',
            'data'    => [
                'name'   => $user->name,
                'email'  => $user->email,
                'avatar' => $user->avatar ? url($user->avatar) : null,
            ],
        ]);
    }

    /**
     * POST /api/admin/profile  (multipart: name, email, avatar?)
     */
    public function update(Request $request)
    {
        $user = $request->user();
        if ($user && $user->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'];

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada; validasi path berada di dalam public/uploads
            if ($user->avatar) {
                $oldPath    = public_path(ltrim($user->avatar, '/'));
                $uploadsDir = realpath(public_path('uploads'));
                $realOld    = file_exists($oldPath) ? realpath($oldPath) : null;
                if ($realOld && $uploadsDir && str_starts_with($realOld, $uploadsDir . DIRECTORY_SEPARATOR)) {
                    @unlink($realOld);
                }
            }

            // Gunakan ekstensi dari ISI file (bukan nama klien) + nama acak
            $file     = $request->file('avatar');
            $filename = 'avatar-' . Str::lower(Str::random(24)) . '.' . $file->extension();
            $file->move(public_path('uploads'), $filename);
            $user->avatar = '/uploads/' . $filename;
        }

        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'data'    => [
                'name'   => $user->name,
                'email'  => $user->email,
                'avatar' => $user->avatar ? url($user->avatar) : null,
            ],
        ]);
    }

    /**
     * PUT /api/admin/password
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();
        if ($user && $user->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $data = $request->validate([
            'current_password'      => ['required', 'string'],
            'password'              => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/[A-Z]/',   // minimal 1 huruf besar
                'regex:/[0-9]/',   // minimal 1 angka
            ],
            'password_confirmation' => ['required', 'string'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password lama tidak sesuai.'],
            ]);
        }

        // Tentukan ID token saat ini agar tidak ikut dicabut.
        // Prioritas: (1) currentAccessToken() dari Sanctum, (2) parse bearer token plain-text.
        // Format plain-text Sanctum: "{id}|{hash}" — ID selalu integer di awal.
        $currentTokenId = null;

        $accessToken = $user->currentAccessToken();
        if ($accessToken && method_exists($accessToken, 'getKey') && $accessToken->getKey()) {
            $currentTokenId = $accessToken->getKey();
        } elseif ($accessToken && isset($accessToken->id) && $accessToken->id) {
            $currentTokenId = $accessToken->id;
        }

        if ($currentTokenId === null && $request->bearerToken()) {
            $parts = explode('|', $request->bearerToken(), 2);
            if (count($parts) === 2 && ctype_digit($parts[0])) {
                $currentTokenId = (int) $parts[0];
            }
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        // Cabut semua token lain milik user ini kecuali token sesi saat ini,
        // sehingga perangkat / sesi lain dipaksa login ulang.
        if ($currentTokenId !== null) {
            $user->tokens()->where('id', '!=', $currentTokenId)->delete();
        } else {
            // Token transient (tidak ada di DB) — cabut semua token persisten
            $user->tokens()->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Password berhasil diperbarui.',
            'data'    => null,
        ]);
    }
}
