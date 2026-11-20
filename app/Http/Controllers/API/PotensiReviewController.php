<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Potensi;

class PotensiReviewController extends Controller
{
    public function review(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:approved,rejected,pending,aktif',
            'reason' => 'required_if:status,rejected|nullable|string|max:1000',
        ], [
            'reason.required_if' => 'Alasan penolakan wajib diisi.',
        ]);

        $potensi = Potensi::find($id);
        if (!$potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

        $potensi->status = $data['status'];
        $potensi->reviewed_by = $request->user()->id;
        $potensi->reviewed_at = now();
        $potensi->rejection_reason = $data['status'] === 'rejected' ? ($data['reason'] ?? null) : null;
        $potensi->save();

        return response()->json([
            'status' => 'success',
            'data' => $potensi,
        ]);
    }
}
