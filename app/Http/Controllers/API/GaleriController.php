<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GaleriController extends Controller
{
    /**
     * GET /api/galeri (publik)
     * Mengembalikan daftar galeri terurut berdasarkan sort_order lalu id.
     */
    public function index()
    {
        $galeri = Galeri::orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $galeri,
        ]);
    }

    /**
     * POST /api/galeri (admin)
     * Menambahkan foto baru ke galeri kegiatan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'caption' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ], [
            'image.required' => 'Pilih file gambar terlebih dahulu.',
            'image.file' => 'File yang diunggah tidak valid.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
            'caption.required' => 'Judul kegiatan wajib diisi.',
            'caption.string' => 'Judul kegiatan harus berupa teks.',
            'caption.max' => 'Judul kegiatan maksimal 100 karakter.',
            'description.string' => 'Deskripsi kegiatan harus berupa teks.',
            'sort_order.integer' => 'Urutan harus berupa angka bulat.',
        ]);

        $file = $request->file('image');
        // Ekstensi ditentukan dari isi file (MIME type sniffing)
        $filename = 'galeri-'.Str::lower(Str::random(24)).'.'.$file->extension();

        $destinationPath = public_path('uploads');
        if (! is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        $file->move($destinationPath, $filename);

        $maxSort = Galeri::max('sort_order') ?? 0;
        $sortOrder = $request->filled('sort_order')
            ? (int) $request->input('sort_order')
            : ($maxSort + 1);

        $item = Galeri::create([
            'image_path' => '/uploads/'.$filename,
            'caption' => $request->input('caption'),
            'description' => $request->input('description'),
            'sort_order' => $sortOrder,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Foto kegiatan berhasil ditambahkan.',
            'data' => $item,
        ], 201);
    }

    /**
     * PUT /api/galeri/{id} (admin)
     * Mengubah caption dan description foto kegiatan.
     */
    public function update(Request $request, $id)
    {
        $galeri = Galeri::find($id);
        if (! $galeri) {
            return response()->json(['status' => 'error', 'message' => 'Data galeri tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'caption' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ], [
            'caption.required' => 'Judul kegiatan wajib diisi.',
            'caption.string' => 'Judul kegiatan harus berupa teks.',
            'caption.max' => 'Judul kegiatan maksimal 100 karakter.',
            'description.string' => 'Deskripsi kegiatan harus berupa teks.',
            'sort_order.integer' => 'Urutan harus berupa angka bulat.',
        ]);

        $galeri->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Judul dan deskripsi kegiatan berhasil diperbarui.',
            'data' => $galeri,
        ]);
    }

    /**
     * DELETE /api/galeri/{id} (admin)
     * Menghapus record dan file fisiknya jika berada di public/uploads.
     */
    public function destroy(Request $request, $id)
    {
        $galeri = Galeri::find($id);
        if (! $galeri) {
            return response()->json(['status' => 'error', 'message' => 'Data galeri tidak ditemukan.'], 404);
        }

        // Hapus file fisik HANYA bila berada di dalam folder public/uploads
        $rawPath = ltrim($galeri->image_path, '/\\');
        if (str_starts_with($rawPath, 'uploads/')) {
            $fullPath = public_path($rawPath);
            if (file_exists($fullPath) && is_file($fullPath)) {
                @unlink($fullPath);
            }
        }

        $galeri->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Foto galeri berhasil dihapus.',
        ]);
    }
}
