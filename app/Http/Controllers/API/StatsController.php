<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Potensi;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $total = Potensi::count();
        $categories = Category::withCount('potensi')->get()->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'count' => $c->potensi_count,
            ];
        });
        $pending = Potensi::where('status', 'pending')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'totalPotensi' => $total,
                'perCategory' => $categories,
                'pendingReviews' => $pending,
            ],
        ]);
    }

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
}
