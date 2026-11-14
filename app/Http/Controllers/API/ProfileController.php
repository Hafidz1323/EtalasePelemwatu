<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'];

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($user->avatar) {
                $oldPath = public_path(ltrim($user->avatar, '/'));
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file     = $request->file('avatar');
            $filename = 'avatar_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
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

        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Password berhasil diperbarui.',
            'data'    => null,
        ]);
    }
}
