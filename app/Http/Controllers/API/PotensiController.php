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

        // Pengunjung publik hanya melihat yang sudah disetujui; admin (token valid) melihat semua.
        $isAdmin = (bool) $request->user('sanctum');
        if (! $isAdmin) {
            $query->whereIn('status', Potensi::STATUS_PUBLIK);
        }

        if ($request->has('category') && $request->category !== 'all') {
            $cat = $request->category;
            $query->whereHas('category', function ($q) use ($cat) {
                $q->where('name', $cat);
            });
        }

        if ($isAdmin && $request->filled('status')) {
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
        // a. Normalisasi sebelum validasi
        $wa = $request->input('whatsapp');
        if (!is_null($wa)) {
            $wa = preg_replace('/[\s\-()]/', '', $wa);
            $wa = preg_replace('/^\+?62(?=8)/', '0', $wa);
        }

        $email = $request->input('email');
        if (!is_null($email)) {
            $email = strtolower(trim($email));
            $email = $email === '' ? null : $email;
        }

        $request->merge([
            'whatsapp' => $wa,
            'email'    => $email,
        ]);

        // b. Validasi ketat dengan pesan bahasa Indonesia
        $validated = $request->validate([
            'name'            => 'required|string|min:3|max:100',
            'owner_name'      => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s.\'\-]+$/u'],
            'category_id'     => 'nullable|integer|exists:categories,id',
            'whatsapp'        => ['required', 'regex:/^08[1-9][0-9]{7,10}$/'],
            'address'         => 'required|string|min:10|max:255',
            'description'     => ['required', 'string', 'min:20', 'max:1000', 'not_regex:/(\p{L})\1{7,}/u'],
            'location'        => 'nullable|string|max:255',
            'email'           => 'nullable|email:rfc|max:100',
            'jenis'           => 'nullable|string|max:100',
            'jam_operasional' => 'nullable|string|max:100',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
        ], [
            'name.required'          => 'Nama potensi wajib diisi.',
            'name.min'               => 'Nama potensi minimal 3 karakter.',
            'name.max'               => 'Nama potensi maksimal 100 karakter.',
            'owner_name.required'    => 'Nama lengkap wajib diisi.',
            'owner_name.min'         => 'Nama lengkap minimal 3 huruf.',
            'owner_name.regex'       => 'Nama lengkap hanya boleh berisi huruf.',
            'category_id.exists'     => 'Kategori yang dipilih tidak valid.',
            'whatsapp.required'      => 'Nomor telepon wajib diisi.',
            'whatsapp.regex'         => 'Nomor telepon tidak valid. Contoh: 08123456789.',
            'address.required'       => 'Alamat rumah wajib diisi.',
            'address.min'            => 'Alamat rumah minimal 10 karakter.',
            'description.required'   => 'Deskripsi wajib diisi.',
            'description.min'        => 'Deskripsi minimal 20 karakter.',
            'description.max'        => 'Deskripsi maksimal 1000 karakter.',
            'description.not_regex'  => 'Deskripsi mengandung huruf yang diulang-ulang, mohon ditulis dengan kalimat yang jelas.',
            'email.email'            => 'Format email tidak valid. Contoh: nama@gmail.com.',
        ]);

        // d. Pendaftaran warga selalu menunggu verifikasi admin
        $validated['status'] = 'pending';

        $potensi = Potensi::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Potensi berhasil didaftarkan!',
            'data'    => $potensi,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $potensi = Potensi::with('category', 'images')->find($id);

        if (! $potensi || (! $request->user('sanctum') && ! in_array($potensi->status, Potensi::STATUS_PUBLIK, true))) {
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

        $data = $request->validate([
            'name'            => 'sometimes|required|string|min:3|max:100',
            'owner_name'      => ['sometimes', 'required', 'string', 'min:3', 'max:100'],
            'category_id'     => 'sometimes|nullable|integer|exists:categories,id',
            'whatsapp'        => ['sometimes', 'required', 'regex:/^08[1-9][0-9]{7,10}$/'],
            'address'         => 'sometimes|required|string|min:10|max:255',
            'description'     => 'sometimes|required|string|min:20|max:1000',
            'location'        => 'sometimes|nullable|string|max:255',
            'email'           => 'sometimes|nullable|email:rfc|max:100',
            'jenis'           => 'sometimes|nullable|string|max:100',
            'jam_operasional' => 'sometimes|nullable|string|max:100',
            'latitude'        => 'sometimes|nullable|numeric|between:-90,90',
            'longitude'       => 'sometimes|nullable|numeric|between:-180,180',
            'status'          => 'sometimes|required|in:aktif,approved,pending,rejected',
        ]);

        // Perubahan status oleh admin dicatat sebagai hasil review.
        if (isset($data['status']) && $data['status'] !== $potensi->status) {
            $data['reviewed_by'] = $request->user()->id;
            $data['reviewed_at'] = now();
        }

        $potensi->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Potensi berhasil diperbarui',
            'data' => $potensi->fresh(['category', 'images']),
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

