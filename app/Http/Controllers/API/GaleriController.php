<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use App\Services\ImageOptimizer;
use App\Services\MediaStorage;
use Illuminate\Http\Request;

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
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'caption' => $item->caption,
                    'description' => $item->description,
                    'image_path' => MediaStorage::url($item->image_path),
                    'sort_order' => $item->sort_order,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

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
        $filename = ImageOptimizer::processAndSave($file, 'galeri');

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

        $returnData = $item->toArray();
        $returnData['image_path'] = MediaStorage::url($item->image_path);

        return response()->json([
            'status' => 'success',
            'message' => 'Foto kegiatan berhasil ditambahkan.',
            'data' => $returnData,
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

        $returnData = $galeri->toArray();
        $returnData['image_path'] = MediaStorage::url($galeri->image_path);

        return response()->json([
            'status' => 'success',
            'message' => 'Judul dan deskripsi kegiatan berhasil diperbarui.',
            'data' => $returnData,
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

        // Hapus file fisik via MediaStorage (hanya jika berada di /uploads/)
        MediaStorage::delete($galeri->image_path);

        $galeri->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Foto galeri berhasil dihapus.',
        ]);
    }
}
