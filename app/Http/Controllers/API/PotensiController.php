<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PotensiAdminResource;
use App\Http\Resources\PotensiPublicResource;
use App\Models\Potensi;
use App\Models\PotensiImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PotensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Potensi::with('category', 'images');

        // Pengunjung publik hanya melihat yang sudah disetujui; hanya admin (role=admin) melihat semua.
        $authUser = $request->user('sanctum');
        $isAdmin = $authUser && $authUser->role === 'admin';
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
            // Escape karakter wildcard LIKE agar tidak disalahgunakan
            $search = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('name LIKE ? ESCAPE ?', ["%{$search}%", '\\'])
                    ->orWhereRaw('description LIKE ? ESCAPE ?', ["%{$search}%", '\\'])
                    ->orWhereRaw('owner_name LIKE ? ESCAPE ?', ["%{$search}%", '\\']);
            });
        }

        // Pagination: per_page default 12, maks 50
        $perPage = min((int) $request->input('per_page', 12), 50);
        if ($perPage < 1) {
            $perPage = 12;
        }

        $paginator = $query->orderBy('id', 'desc')->paginate($perPage);

        $resource = $isAdmin
            ? PotensiAdminResource::collection($paginator)
            : PotensiPublicResource::collection($paginator);

        return response()->json([
            'status' => 'success',
            'data' => $resource,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        // a. Normalisasi sebelum validasi
        $wa = $request->input('whatsapp');
        if (! is_null($wa)) {
            $wa = preg_replace('/[\s\-()]/', '', $wa);
            $wa = preg_replace('/^\+?62(?=8)/', '0', $wa);
        }

        $email = $request->input('email');
        if (! is_null($email)) {
            $email = strtolower(trim($email));
            $email = $email === '' ? null : $email;
        }

        $request->merge([
            'whatsapp' => $wa,
            'email' => $email,
        ]);

        // b. Validasi ketat dengan pesan bahasa Indonesia
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'owner_name' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s.\'\-]+$/u'],
            'category_id' => 'nullable|integer|exists:categories,id',
            'whatsapp' => ['required', 'regex:/^08[1-9][0-9]{7,10}$/'],
            'address' => 'required|string|min:10|max:255',
            'description' => ['required', 'string', 'min:20', 'max:1000', 'not_regex:/(\p{L})\1{7,}/u'],
            'location' => 'nullable|string|max:255',
            'email' => 'nullable|email:rfc|max:100',
            'jenis' => 'nullable|string|max:100',
            'jam_operasional' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'Nama potensi wajib diisi.',
            'name.min' => 'Nama potensi minimal 3 karakter.',
            'name.max' => 'Nama potensi maksimal 100 karakter.',
            'owner_name.required' => 'Nama lengkap wajib diisi.',
            'owner_name.min' => 'Nama lengkap minimal 3 huruf.',
            'owner_name.regex' => 'Nama lengkap hanya boleh berisi huruf.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'whatsapp.required' => 'Nomor telepon wajib diisi.',
            'whatsapp.regex' => 'Nomor telepon tidak valid. Contoh: 08123456789.',
            'address.required' => 'Alamat rumah wajib diisi.',
            'address.min' => 'Alamat rumah minimal 10 karakter.',
            'description.required' => 'Deskripsi wajib diisi.',
            'description.min' => 'Deskripsi minimal 20 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'description.not_regex' => 'Deskripsi mengandung huruf yang diulang-ulang, mohon ditulis dengan kalimat yang jelas.',
            'email.email' => 'Format email tidak valid. Contoh: nama@gmail.com.',
            'images.max' => 'Maksimal 3 foto yang dapat diunggah.',
            'images.*.image' => 'File yang diunggah harus berupa gambar.',
            'images.*.mimes' => 'Format foto tidak didukung. Gunakan JPG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        // d. Pendaftaran warga selalu menunggu verifikasi admin
        $validated['status'] = 'pending';

        // Simpan data potensi
        $potensiData = collect($validated)->except('images')->toArray();

        // Jika ada warga yang login, hubungkan potensi ke akun mereka
        $authUser = $request->user('sanctum');
        if ($authUser && $authUser->role === 'warga') {
            $potensiData['user_id'] = $authUser->id;
        }

        $potensi = Potensi::create($potensiData);

        // e. Simpan foto jika ada (ekstensi dari isi file, nama acak)
        if ($request->hasFile('images')) {
            $uploadsDir = public_path('uploads');
            if (! is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            foreach ($request->file('images') as $image) {
                $ext = $image->extension(); // dari isi file, bukan nama klien
                $filename = Str::lower(Str::random(24)).'.'.$ext;
                $image->move($uploadsDir, $filename);
                PotensiImage::create([
                    'potensi_id' => $potensi->id,
                    'image_path' => '/uploads/'.$filename,
                ]);
            }
        }

        // POST publik: hanya kembalikan id dan pesan sukses (tanpa data pribadi)
        return response()->json([
            'status' => 'success',
            'message' => 'Potensi berhasil didaftarkan!',
            'data' => ['id' => $potensi->id],
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $potensi = Potensi::with('category', 'images')->find($id);

        $isAdmin = $request->user('sanctum') && $request->user('sanctum')->role === 'admin';

        if (! $potensi || (! $isAdmin && ! in_array($potensi->status, Potensi::STATUS_PUBLIK, true))) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

        $resource = $isAdmin
            ? new PotensiAdminResource($potensi)
            : new PotensiPublicResource($potensi);

        return response()->json(['status' => 'success', 'data' => $resource]);
    }

    public function update(Request $request, $id)
    {
        // Hanya admin yang boleh menggunakan endpoint ini
        $authUser = $request->user();
        if (! $authUser || $authUser->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $potensi = Potensi::find($id);

        if (! $potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|required|string|min:3|max:100',
            'owner_name' => ['sometimes', 'required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s.\'\-]+$/u'],
            'category_id' => 'sometimes|nullable|integer|exists:categories,id',
            'whatsapp' => ['sometimes', 'required', 'regex:/^08[1-9][0-9]{7,10}$/'],
            'address' => 'sometimes|required|string|min:10|max:255',
            'description' => ['sometimes', 'required', 'string', 'min:20', 'max:1000', 'not_regex:/(\p{L})\1{7,}/u'],
            'location' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|nullable|email:rfc|max:100',
            'jenis' => 'sometimes|nullable|string|max:100',
            'jam_operasional' => 'sometimes|nullable|string|max:100',
            'latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'status' => 'sometimes|required|in:approved,pending,rejected',
            'rejection_reason' => 'sometimes|nullable|string|max:1000',
        ]);

        // Perubahan status oleh admin dicatat sebagai hasil review.
        if (isset($data['status']) && $data['status'] !== $potensi->status) {
            $data['reviewed_by'] = $request->user()->id;
            $data['reviewed_at'] = now();

            // Saat keluar dari 'rejected', bersihkan alasan penolakan
            if ($potensi->status === 'rejected' && $data['status'] !== 'rejected') {
                $data['rejection_reason'] = null;
            }
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
        // Hanya admin yang boleh menghapus via endpoint ini
        $authUser = request()->user();
        if (! $authUser || $authUser->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $potensi = Potensi::with('images')->find($id);

        if (! $potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

        // Hapus file gambar yang terdaftar di potensi_images sebelum hapus baris
        foreach ($potensi->images as $image) {
            $filePath = public_path(ltrim($image->image_path, '/'));
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $potensi->delete();

        return response()->json(['status' => 'success', 'message' => 'Potensi berhasil dihapus']);
    }
}
