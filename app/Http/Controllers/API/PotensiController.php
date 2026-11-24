<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Potensi;
use App\Models\Category;
use App\Models\PotensiImage;
=======
use App\Http\Resources\PotensiAdminResource;
use App\Http\Resources\PotensiPublicResource;
use App\Models\Potensi;
use App\Models\PotensiImage;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2

class PotensiController extends Controller
{
    /**
     * Sanitasi nomor WhatsApp ke format internasional (628...)
     */
    private function sanitizeWhatsApp(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($clean, '08')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }

        return $clean ?: null;
    }

    /**
     * Hapus berkas fisik gambar di public/uploads
     */
    private function deletePhysicalImage(?string $path): void
    {
        if (!$path) return;

        $relative = ltrim($path, '/\\');
        $fullPath = public_path($relative);

        if (File::exists($fullPath) && is_file($fullPath)) {
            File::delete($fullPath);
        }
    }

    /**
     * Katalog Potensi (Publik & Admin) dengan Dukungan Pagination
     */
    public function index(Request $request)
    {
        $query = Potensi::with('category', 'images', 'user:id,name,email,phone');

<<<<<<< HEAD
        // Filter status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } elseif (!$request->has('admin_view')) {
            $query->approved();
        }

        // Filter kategori
=======
        // Pengunjung publik hanya melihat yang sudah disetujui; hanya admin (role=admin) melihat semua.
        $authUser = $request->user('sanctum');
        $isAdmin = $authUser && $authUser->role === 'admin';
        if (! $isAdmin) {
            $query->whereIn('status', Potensi::STATUS_PUBLIK);
        }

>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
        if ($request->has('category') && $request->category !== 'all') {
            $cat = $request->category;
            $query->where(function ($q) use ($cat) {
                if (is_numeric($cat)) {
                    $q->where('category_id', $cat);
                } else {
                    $q->whereHas('category', function ($sub) use ($cat) {
                        $sub->where('name', 'like', "%{$cat}%");
                    });
                }
            });
        }

<<<<<<< HEAD
        // Pencarian
        if ($request->has('search') && trim((string)$request->search) !== '') {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $query->orderBy('id', 'desc');

        // Jika request meminta pagination via per_page atau paginate=true
        if ($request->has('per_page') || $request->has('page')) {
            $perPage = max(1, min(50, (int)$request->input('per_page', 9)));
            $paginated = $query->paginate($perPage);

            return response()->json([
                'status'     => 'success',
                'data'       => $paginated->items(),
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page'    => $paginated->lastPage(),
                    'per_page'     => $paginated->perPage(),
                    'total'        => $paginated->total(),
                    'has_more'     => $paginated->hasMorePages(),
                ],
            ]);
        }

        $potensi = $query->get();

        return response()->json([
            'status' => 'success',
            'data'   => $potensi,
=======
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
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
        ]);
    }

    /**
     * Pendaftaran Potensi (Oleh Warga atau Tamu)
     */
    public function store(Request $request)
    {
<<<<<<< HEAD
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'owner_name'  => 'required|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
            'whatsapp'    => 'nullable|string|max:25',
            'address'     => 'nullable|string|max:500',
            'description' => 'nullable|string|max:2000',
            'location'    => 'nullable|string|max:255',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'status'      => 'nullable|string|in:aktif,pending,approved,rejected',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'string',
        ], [
            'name.required'       => 'Nama usaha / potensi wajib diisi.',
            'owner_name.required' => 'Nama pemilik wajib diisi.',
            'category_id.exists'  => 'Kategori yang dipilih tidak valid.',
        ]);

        if (!empty($validated['whatsapp'])) {
            $validated['whatsapp'] = $this->sanitizeWhatsApp($validated['whatsapp']);
        }

        $user = $request->user();
        if ($user) {
            $validated['user_id'] = $user->id;
        }

        if (empty($validated['status'])) {
            $validated['status'] = 'pending';
        }
=======
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
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2

        // d. Pendaftaran warga selalu menunggu verifikasi admin
        $validated['status'] = 'pending';

<<<<<<< HEAD
        if (!empty($validated['images']) && is_array($validated['images'])) {
            foreach ($validated['images'] as $imgSrc) {
                if (!empty($imgSrc) && is_string($imgSrc)) {
                    PotensiImage::create([
                        'potensi_id' => $potensi->id,
                        'image_path' => $imgSrc,
                    ]);
                }
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Potensi berhasil didaftarkan dan menunggu persetujuan admin desa.',
            'data'    => $potensi->load('category', 'images', 'user'),
        ], 201);
    }

    /**
     * Detail Potensi
     */
    public function show($id)
=======
        // Simpan data potensi
        $potensiData = collect($validated)->except('images')->toArray();

        // Jika ada warga yang login, hubungkan potensi ke akun mereka
        $authUser = $request->user('sanctum');
        if ($authUser && $authUser->role === 'warga') {
            $potensiData['user_id'] = $authUser->id;
        }

        $potensi = Potensi::create($potensiData);

        // e. Simpan foto jika ada (resize max 1600px, konversi webp kualitas 80)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = ImageOptimizer::processAndSave($image);
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
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
    {
        $potensi = Potensi::with('category', 'images', 'user:id,name,email,phone')->find($id);

        $isAdmin = $request->user('sanctum') && $request->user('sanctum')->role === 'admin';

        if (! $potensi || (! $isAdmin && ! in_array($potensi->status, Potensi::STATUS_PUBLIK, true))) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

        $resource = $isAdmin
            ? new PotensiAdminResource($potensi)
            : new PotensiPublicResource($potensi);

        return response()->json(['status' => 'success', 'data' => $resource]);
    }

    /**
     * Edit Potensi
     */
    public function update(Request $request, $id)
    {
<<<<<<< HEAD
        $potensi = Potensi::with('images')->find($id);
=======
        // Hanya admin yang boleh menggunakan endpoint ini
        $authUser = $request->user();
        if (! $authUser || $authUser->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $potensi = Potensi::find($id);
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2

        if (! $potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

<<<<<<< HEAD
        $user = $request->user();

        if ($user && !$user->isAdmin() && $potensi->user_id && $potensi->user_id !== $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak berhak mengedit data potensi ini.'], 403);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'owner_name'  => 'sometimes|required|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
            'whatsapp'    => 'nullable|string|max:25',
            'address'     => 'nullable|string|max:500',
            'description' => 'nullable|string|max:2000',
            'location'    => 'nullable|string|max:255',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'status'      => 'nullable|string|in:aktif,pending,approved,rejected',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'string',
        ]);

        if (isset($validated['whatsapp'])) {
            $validated['whatsapp'] = $this->sanitizeWhatsApp($validated['whatsapp']);
        }

        if ($user && $user->isWarga() && empty($validated['status'])) {
            $validated['status'] = 'pending';
        }

        $potensi->update($validated);

        if (array_key_exists('images', $validated) && is_array($validated['images'])) {
            $newImages = $validated['images'];
            $existingImages = $potensi->images;

            foreach ($existingImages as $oldImg) {
                if (!in_array($oldImg->image_path, $newImages)) {
                    $this->deletePhysicalImage($oldImg->image_path);
                    $oldImg->delete();
                }
            }

            $currentPaths = $existingImages->pluck('image_path')->toArray();
            foreach ($newImages as $imgPath) {
                if (!in_array($imgPath, $currentPaths)) {
                    PotensiImage::create([
                        'potensi_id' => $potensi->id,
                        'image_path' => $imgPath,
                    ]);
                }
            }
        }
=======
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
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2

        return response()->json([
            'status'  => 'success',
            'message' => 'Potensi berhasil diperbarui',
<<<<<<< HEAD
            'data'    => $potensi->fresh(['category', 'images', 'user']),
=======
            'data' => $potensi->fresh(['category', 'images']),
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
        ]);
    }

    /**
     * Hapus Potensi
     */
    public function destroy(Request $request, $id)
    {
<<<<<<< HEAD
        $potensi = Potensi::with('images')->find($id);
=======
        // Hanya admin yang boleh menghapus via endpoint ini
        $authUser = request()->user();
        if (! $authUser || $authUser->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2

        $potensi = Potensi::with('images')->find($id);

        if (! $potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

<<<<<<< HEAD
        $user = $request->user();

        if ($user && !$user->isAdmin() && $potensi->user_id && $potensi->user_id !== $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak berhak menghapus data potensi ini.'], 403);
        }

        foreach ($potensi->images as $img) {
            $this->deletePhysicalImage($img->image_path);
=======
        // Hapus file gambar yang terdaftar di potensi_images sebelum hapus baris
        foreach ($potensi->images as $image) {
            $filePath = public_path(ltrim($image->image_path, '/'));
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
        }

        $potensi->delete();

        return response()->json(['status' => 'success', 'message' => 'Potensi dan seluruh data terkait berhasil dihapus']);
    }

    /**
     * Potensi Milik Warga yang Sedang Login
     */
    public function myPotensi(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $data = Potensi::with('category', 'images')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    /**
     * Ekspor Data Potensi ke CSV/Excel
     */
    public function exportCsv()
    {
        $potensiList = Potensi::with('category', 'user')->orderBy('id', 'asc')->get();

        $filename = 'rekap-potensi-pelemwatu-' . date('Ymd-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($potensiList) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'ID',
                'Nama Potensi / Usaha',
                'Kategori',
                'Nama Pemilik',
                'Akun Pendaftar',
                'Nomor WhatsApp',
                'Alamat',
                'Lokasi / Dusun',
                'Status',
                'Tanggal Didaftarkan',
            ]);

            foreach ($potensiList as $p) {
                fputcsv($file, [
                    $p->id,
                    $p->name,
                    $p->category ? $p->category->name : 'Tanpa Kategori',
                    $p->owner_name,
                    $p->user ? $p->user->name . ' (' . $p->user->email . ')' : 'Tamu / Umum',
                    $p->whatsapp ? "'" . $p->whatsapp : '-',
                    $p->address ?: '-',
                    $p->location ?: '-',
                    strtoupper($p->status),
                    $p->created_at ? $p->created_at->format('d-m-Y H:i') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
