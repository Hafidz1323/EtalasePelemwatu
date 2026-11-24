<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Potensi;
use App\Models\Category;
use App\Models\User;

class StatsController extends Controller
{
    /**
     * Mengambil Data Statistik Aktual Desa
     */
    public function index()
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
}
