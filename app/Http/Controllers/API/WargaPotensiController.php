<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Potensi;
use App\Models\PotensiImage;
use App\Services\ImageOptimizer;
use App\Services\MediaStorage;
use Illuminate\Http\Request;

class WargaPotensiController extends Controller
{
    private function requireWarga(Request $request)
    {
        $user = $request->user();
        if (! $user || $user->role !== 'warga') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        return null;
    }

    /** GET /api/warga/potensi */
    public function index(Request $request)
    {
        if ($err = $this->requireWarga($request)) {
            return $err;
        }

        $perPage = min((int) $request->input('per_page', 12), 50);
        if ($perPage < 1) {
            $perPage = 12;
        }

        $potensi = Potensi::with('category', 'images')
            ->where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $potensi->items(),
            'meta' => [
                'current_page' => $potensi->currentPage(),
                'last_page' => $potensi->lastPage(),
                'total' => $potensi->total(),
                'per_page' => $potensi->perPage(),
            ],
        ]);
    }

    /** GET /api/warga/potensi/{id} */
    public function show(Request $request, $id)
    {
        if ($err = $this->requireWarga($request)) {
            return $err;
        }

        $potensi = Potensi::with('category', 'images')->find($id);
        if (! $potensi || $potensi->user_id !== $request->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan.'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $potensi]);
    }

    /** POST /api/warga/potensi/{id} — edit (hanya pending/rejected) */
    public function update(Request $request, $id)
    {
        if ($err = $this->requireWarga($request)) {
            return $err;
        }

        $potensi = Potensi::with('images')->find($id);
        if (! $potensi || $potensi->user_id !== $request->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan.'], 404);
        }

        if (! in_array($potensi->status, ['pending', 'rejected'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Potensi yang sudah disetujui tidak dapat diedit. Hubungi admin jika perlu perubahan.',
            ], 422);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:100'],
            'owner_name' => ['sometimes', 'required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s.\'\-]+$/u'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'whatsapp' => ['sometimes', 'required', 'regex:/^08[1-9][0-9]{7,10}$/'],
            'address' => ['sometimes', 'required', 'string', 'min:10', 'max:255'],
            'description' => ['sometimes', 'required', 'string', 'min:20', 'max:1000', 'not_regex:/(\p{L})\1{7,}/u'],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email:rfc', 'max:100'],
            'jenis' => ['sometimes', 'nullable', 'string', 'max:100'],
            'jam_operasional' => ['sometimes', 'nullable', 'string', 'max:100'],
            'images' => ['sometimes', 'nullable', 'array', 'max:3'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'whatsapp.regex' => 'Nomor telepon tidak valid. Contoh: 08123456789.',
            'description.not_regex' => 'Deskripsi mengandung huruf yang diulang-ulang.',
            'images.max' => 'Maksimal 3 foto.',
            'images.*.mimes' => 'Format foto tidak didukung. Gunakan JPG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        if ($potensi->status === 'rejected') {
            $data['status'] = 'pending';
            $data['rejection_reason'] = null;
            $data['reviewed_by'] = null;
            $data['reviewed_at'] = null;
        }

        $potensiData = collect($data)->except('images')->toArray();
        $potensi->update($potensiData);

        if ($request->hasFile('images')) {
            foreach ($potensi->images as $img) {
                MediaStorage::delete($img->image_path);
                $img->delete();
            }
            foreach ($request->file('images') as $image) {
                $filename = ImageOptimizer::processAndSave($image);
                PotensiImage::create(['potensi_id' => $potensi->id, 'image_path' => '/uploads/'.$filename]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Potensi berhasil diperbarui.',
            'data' => $potensi->fresh(['category', 'images']),
        ]);
    }

    /**
     * DELETE /api/warga/potensi/{id}
     * Alias klasik untuk backward-compat dengan test & client lama.
     * pending/rejected → langsung hapus; approved → 422 (harus lewat request-deletion).
     */
    public function destroy(Request $request, $id)
    {
        if ($err = $this->requireWarga($request)) {
            return $err;
        }

        $potensi = Potensi::with('images')->find($id);
        if (! $potensi || $potensi->user_id !== $request->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan.'], 404);
        }

        if (! in_array($potensi->status, ['pending', 'rejected'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Potensi yang sudah disetujui tidak dapat langsung dihapus. Gunakan fitur "Ajukan Hapus" untuk meminta persetujuan admin.',
            ], 422);
        }

        foreach ($potensi->images as $img) {
            MediaStorage::delete($img->image_path);
        }
        $potensi->delete();

        return response()->json(['status' => 'success', 'message' => 'Potensi berhasil dihapus.']);
    }

    /**
     * POST /api/warga/potensi/{id}/request-deletion
     * Warga mengajukan permintaan hapus ke admin.
     * Untuk potensi pending/rejected → langsung hapus (tidak perlu persetujuan admin).
     * Untuk potensi approved → set deletion_requested_at, tunggu admin.
     */
    public function requestDeletion(Request $request, $id)
    {
        if ($err = $this->requireWarga($request)) {
            return $err;
        }

        $potensi = Potensi::with('images')->find($id);
        if (! $potensi || $potensi->user_id !== $request->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan.'], 404);
        }

        // Sudah ada permintaan sebelumnya
        if ($potensi->deletion_requested_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'Permintaan penghapusan sudah diajukan. Tunggu persetujuan admin.',
            ], 422);
        }

        // Pending/rejected → langsung hapus tanpa perlu persetujuan admin
        if (in_array($potensi->status, ['pending', 'rejected'])) {
            foreach ($potensi->images as $img) {
                MediaStorage::delete($img->image_path);
            }
            $potensi->delete();

            return response()->json(['status' => 'success', 'message' => 'Potensi berhasil dihapus.']);
        }

        // Approved → ajukan ke admin
        $potensi->deletion_requested_at = now();
        $potensi->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan penghapusan berhasil diajukan. Admin akan meninjau dalam 1×24 jam.',
        ]);
    }

    /**
     * POST /api/warga/potensi/{id}/cancel-deletion
     * Warga membatalkan permintaan hapus yang sudah diajukan.
     */
    public function cancelDeletion(Request $request, $id)
    {
        if ($err = $this->requireWarga($request)) {
            return $err;
        }

        $potensi = Potensi::find($id);
        if (! $potensi || $potensi->user_id !== $request->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan.'], 404);
        }

        if (! $potensi->deletion_requested_at) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada permintaan penghapusan aktif.'], 422);
        }

        $potensi->deletion_requested_at = null;
        $potensi->save();

        return response()->json(['status' => 'success', 'message' => 'Permintaan penghapusan dibatalkan.']);
    }

    /** DELETE /api/warga/potensi/{id}/images/{imageId} */
    public function destroyImage(Request $request, $id, $imageId)
    {
        if ($err = $this->requireWarga($request)) {
            return $err;
        }

        $potensi = Potensi::find($id);
        if (! $potensi || $potensi->user_id !== $request->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan.'], 404);
        }

        $image = PotensiImage::where('id', $imageId)->where('potensi_id', $id)->first();
        if (! $image) {
            return response()->json(['status' => 'error', 'message' => 'Foto tidak ditemukan.'], 404);
        }

        MediaStorage::delete($image->image_path);
        $image->delete();

        return response()->json(['status' => 'success', 'message' => 'Foto berhasil dihapus.']);
    }
}
