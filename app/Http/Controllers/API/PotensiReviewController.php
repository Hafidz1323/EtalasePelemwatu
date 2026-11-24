<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Potensi;

class PotensiReviewController extends Controller
{
    /**
     * Admin Memverifikasi atau Menolak Potensi Warga
     */
    public function review(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,pending,aktif',
            'reason' => 'nullable|string|max:1000',
        ], [
            'status.required' => 'Status verifikasi harus dipilih.',
            'status.in'       => 'Status harus berupa approved, rejected, pending, atau aktif.',
        ]);

        $potensi = Potensi::find($id);
        if (!$potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

        $admin = $request->user();

        $potensi->status = $validated['status'];
        $potensi->reviewed_by = $admin ? $admin->id : null;
        $potensi->reviewed_at = now();
        $potensi->rejection_reason = ($validated['status'] === 'rejected') ? ($validated['reason'] ?? 'Tidak memenuhi kriteria desa') : null;
        $potensi->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Status potensi berhasil diperbarui menjadi ' . strtoupper($potensi->status),
            'data'    => $potensi->fresh(['category', 'images', 'reviewer', 'user']),
        ]);
    }
}
