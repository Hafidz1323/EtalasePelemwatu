<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Potensi;

class PotensiReviewController extends Controller
{
    /** POST /api/potensi/{id}/review — setujui / tolak pendaftaran */
    public function review(Request $request, $id)
    {
        if ($request->user() && $request->user()->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:approved,rejected,pending',
            'reason' => 'required_if:status,rejected|nullable|string|max:1000',
        ], [
            'reason.required_if' => 'Alasan penolakan wajib diisi.',
        ]);

        $potensi = Potensi::find($id);
        if (!$potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

        $potensi->status           = $data['status'];
        $potensi->reviewed_by      = $request->user()->id;
        $potensi->reviewed_at      = now();
        $potensi->rejection_reason = $data['status'] === 'rejected' ? ($data['reason'] ?? null) : null;
        $potensi->save();

        return response()->json(['status' => 'success', 'data' => $potensi]);
    }

    /**
     * GET /api/admin/deletion-requests
     * Daftar potensi yang mengajukan permintaan penghapusan.
     */
    public function deletionRequests(Request $request)
    {
        if ($request->user() && $request->user()->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $list = Potensi::with('category', 'images', 'owner')
            ->whereNotNull('deletion_requested_at')
            ->orderBy('deletion_requested_at', 'asc')
            ->get()
            ->map(function ($p) {
                return [
                    'id'                     => $p->id,
                    'name'                   => $p->name,
                    'owner_name'             => $p->owner_name,
                    'owner_phone'            => $p->whatsapp,
                    'status'                 => $p->status,
                    'category'               => $p->category ? $p->category->name : null,
                    'deletion_requested_at'  => $p->deletion_requested_at,
                    'images'                 => $p->images->map(fn($img) => ['id' => $img->id, 'path' => $img->image_path]),
                ];
            });

        return response()->json(['status' => 'success', 'data' => $list, 'total' => $list->count()]);
    }

    /**
     * POST /api/admin/deletion-requests/{id}/approve
     * Admin menyetujui penghapusan → benar-benar hapus.
     */
    public function approveDeletion(Request $request, $id)
    {
        if ($request->user() && $request->user()->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $potensi = Potensi::with('images')->find($id);
        if (! $potensi || ! $potensi->deletion_requested_at) {
            return response()->json(['status' => 'error', 'message' => 'Permintaan penghapusan tidak ditemukan.'], 404);
        }

        foreach ($potensi->images as $img) {
            $filePath = public_path(ltrim($img->image_path, '/'));
            if (file_exists($filePath)) @unlink($filePath);
        }
        $potensi->delete();

        return response()->json(['status' => 'success', 'message' => 'Potensi berhasil dihapus.']);
    }

    /**
     * POST /api/admin/deletion-requests/{id}/reject
     * Admin menolak penghapusan → hapus flag, potensi tetap ada.
     */
    public function rejectDeletion(Request $request, $id)
    {
        if ($request->user() && $request->user()->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $potensi = Potensi::find($id);
        if (! $potensi || ! $potensi->deletion_requested_at) {
            return response()->json(['status' => 'error', 'message' => 'Permintaan penghapusan tidak ditemukan.'], 404);
        }

        $potensi->deletion_requested_at = null;
        $potensi->save();

        return response()->json(['status' => 'success', 'message' => 'Permintaan penghapusan ditolak. Potensi tetap aktif.']);
    }
}

