<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $user = $request->user();
        if (! $user || $user->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'image.required' => 'Pilih file gambar terlebih dahulu.',
            'image.image'    => 'File harus berupa gambar.',
            'image.mimes'    => 'Format gambar harus JPG, PNG, atau WEBP.',
            'image.max'      => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $file = $request->file('image');
        // extension() ditebak dari ISI file, bukan dari nama yang dikirim pengguna.
        $filename = 'image-' . Str::lower(Str::random(24)) . '.' . $file->extension();
        $file->move(public_path('uploads'), $filename);

        return response()->json([
            'status' => 'success',
            'message' => 'File uploaded',
            'path' => '/uploads/' . $filename,
        ]);
    }
}
