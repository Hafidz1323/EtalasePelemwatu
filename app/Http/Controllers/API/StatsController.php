<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Potensi;
use App\Models\Category;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user() && $request->user()->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

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
}

