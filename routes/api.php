<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PotensiController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\StatsController;
use App\Http\Controllers\API\PotensiReviewController;
use App\Http\Controllers\API\UploadController;
use App\Http\Middleware\AdminAuthMiddleware;

/*
|--------------------------------------------------------------------------
| Public Routes (Dapat diakses oleh publik & pengunjung umum)
|--------------------------------------------------------------------------
*/

// Status API
Route::get('/status', function () {
    return response()->json([
        'status'  => 'success',
        'message' => 'API EtalasePelemwatu Laravel aktif',
        'version' => '1.0.0',
    ]);
});

// Autentikasi Pengguna & Warga (Rate limited: maks 15 percobaan per menit)
Route::middleware('throttle:15,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/auth/register', [AuthController::class, 'register']);
});

Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::get('/auth/verify', [AuthController::class, 'verify']);

// Publik: Katalog Potensi, Kategori, & Statistik Aktual Desa
Route::get('/potensi', [PotensiController::class, 'index']);
Route::get('/potensi/{id}', [PotensiController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/stats/public', [StatsController::class, 'index']);

// Publik / Tamu: Pendaftaran Potensi & Form Kontak Warga (Rate limited anti-spam)
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/potensi', [PotensiController::class, 'store']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::post('/upload', [UploadController::class, 'upload']);
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Warga & Admin Login via Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Profil Warga
    Route::get('/user/profile', [AuthController::class, 'profile']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);

    // Potensi Milik Warga Sendiri
    Route::get('/user/potensi', [PotensiController::class, 'myPotensi']);

    // Edit & Hapus Potensi (Dengan Otorisasi Pemilik / Admin)
    Route::put('/potensi/{id}', [PotensiController::class, 'update']);
    Route::delete('/potensi/{id}', [PotensiController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Admin Protected Routes (Hanya untuk Pengelola / Perangkat Desa)
|--------------------------------------------------------------------------
*/
Route::middleware([AdminAuthMiddleware::class])->group(function () {
    // Admin verifikasi & tolak potensi
    Route::post('/potensi/{id}/review', [PotensiReviewController::class, 'review']);
    Route::post('/potensi-review/{id}', [PotensiReviewController::class, 'review']);

    // Ekspor Data Laporan Desa ke Excel / CSV
    Route::get('/potensi-export/csv', [PotensiController::class, 'exportCsv']);

    // Kelola Kategori Desa
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Rekap Pesan Warga & Statistik Lengkap Admin
    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/stats', [StatsController::class, 'index']);
});
