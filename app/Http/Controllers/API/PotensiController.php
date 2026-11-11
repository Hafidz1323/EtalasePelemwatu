<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Potensi;
use App\Models\Category;

class PotensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Potensi::with('category', 'images');

        if ($request->has('category') && $request->category !== 'all') {
            $cat = $request->category;
            $query->whereHas('category', function ($q) use ($cat) {
                $q->where('name', $cat);
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        $potensi = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $potensi,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'category_id' => 'nullable|integer',
            'whatsapp' => 'nullable|string',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $validated['status'] = 'aktif';

        $potensi = Potensi::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Potensi berhasil didaftarkan!',
            'data' => $potensi,
        ], 201);
    }

    public function show($id)
    {
        $potensi = Potensi::with('category', 'images')->find($id);

        if (!$potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $potensi]);
    }

    public function update(Request $request, $id)
    {
        $potensi = Potensi::find($id);

        if (!$potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

        $potensi->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Potensi berhasil diperbarui',
            'data' => $potensi,
        ]);
    }

    public function destroy($id)
    {
        $potensi = Potensi::find($id);

        if (!$potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

        $potensi->delete();

        return response()->json(['status' => 'success', 'message' => 'Potensi berhasil dihapus']);
    }
}

