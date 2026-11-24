<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
<<<<<<< HEAD
use App\Models\User;

class StatsController extends Controller
{
    /**
     * Mengambil Data Statistik Aktual Desa
     */
    public function index()
=======
use App\Models\Potensi;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request)
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
    {
        // 1. Data potensi riil
        $totalPotensi = Potensi::count();
        $potensiAktif = Potensi::whereIn('status', ['approved', 'aktif'])->count();
        $pendingReviews = Potensi::where('status', 'pending')->count();
        $rejectedPotensi = Potensi::where('status', 'rejected')->count();

        // 2. Data warga & kategori
        $totalWarga = User::where('role', 'warga')->count();

        // 3. Rekap jumlah per kategori aktual
        $perCategory = Category::withCount(['potensi' => function ($q) {
            $q->whereIn('status', ['approved', 'aktif']);
        }])->get()->map(function ($c) {
            return [
                'id'    => $c->id,
                'name'  => $c->name,
                'count' => $c->potensi_count,
            ];
        });
<<<<<<< HEAD
=======
        $pending = Potensi::where('status', 'pending')->count();
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2

        return response()->json([
            'status' => 'success',
            'data'   => [
                'totalPotensi'    => $totalPotensi,
                'potensiAktif'    => $potensiAktif,
                'pendingReviews'  => $pendingReviews,
                'rejectedPotensi' => $rejectedPotensi,
                'totalWarga'      => $totalWarga,
                'perCategory'     => $perCategory,
            ],
        ]);
    }
<<<<<<< HEAD
=======

    /**
     * GET /api/stats/public (publik)
     * Mengembalikan jumlah potensi berstatus publik (approved) berkategori usaha/jasa/hunian.
     * Tidak mengekspos data internal lainnya.
     */
    public function publicStats()
    {
        $umkm = Potensi::whereIn('status', Potensi::STATUS_PUBLIK)
            ->whereHas('category', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', 'usaha%')
                        ->orWhere('name', 'like', 'jasa%')
                        ->orWhere('name', 'like', 'hunian%');
                });
            })
            ->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'umkm' => $umkm,
            ],
            'umkm' => $umkm,
        ]);
    }
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
}
