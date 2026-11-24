<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Potensi;
use App\Models\Category;
use App\Models\PotensiImage;

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

        // Filter status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } elseif (!$request->has('admin_view')) {
            $query->approved();
        }

        // Filter kategori
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
        ]);
    }

    /**
     * Pendaftaran Potensi (Oleh Warga atau Tamu)
     */
    public function store(Request $request)
    {
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

        $potensi = Potensi::create($validated);

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
    {
        $potensi = Potensi::with('category', 'images', 'user:id,name,email,phone')->find($id);

        if (!$potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $potensi]);
    }

    /**
     * Edit Potensi
     */
    public function update(Request $request, $id)
    {
        $potensi = Potensi::with('images')->find($id);

        if (!$potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

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

        return response()->json([
            'status'  => 'success',
            'message' => 'Potensi berhasil diperbarui',
            'data'    => $potensi->fresh(['category', 'images', 'user']),
        ]);
    }

    /**
     * Hapus Potensi
     */
    public function destroy(Request $request, $id)
    {
        $potensi = Potensi::with('images')->find($id);

        if (!$potensi) {
            return response()->json(['status' => 'error', 'message' => 'Potensi tidak ditemukan'], 404);
        }

        $user = $request->user();

        if ($user && !$user->isAdmin() && $potensi->user_id && $potensi->user_id !== $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak berhak menghapus data potensi ini.'], 403);
        }

        foreach ($potensi->images as $img) {
            $this->deletePhysicalImage($img->image_path);
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
