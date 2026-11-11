<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Potensi;

class PotensiReviewController extends Controller
{
    public function review(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending,aktif',
        ]);

        $potensi = Potensi::find($id);
        if (!$potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

        $potensi->status = $request->status;
        $potensi->reviewed_at = now();
        $potensi->rejection_reason = $request->reason;
        $potensi->save();

        return response()->json([
            'status' => 'success',
            'data' => $potensi,
        ]);
    }
}

