<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\MediaStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'image.required' => 'Pilih file gambar terlebih dahulu.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $file = $request->file('image');
        // extension() ditebak dari ISI file, bukan dari nama yang dikirim pengguna.
        $filename = 'image-'.Str::lower(Str::random(24)).'.'.$file->extension();
        MediaStorage::putFile($file, $filename);

        return response()->json([
            'status' => 'success',
            'message' => 'File uploaded',
            'path' => MediaStorage::url('/uploads/'.$filename),
        ]);
    }
}
