<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PotensiController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\StatsController;
use App\Http\Controllers\API\PotensiReviewController;
use App\Http\Controllers\API\UploadController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\SettingController;

/*
|--------------------------------------------------------------------------
| Rute publik (tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/status', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API EtalasePelemwatu Laravel aktif',
        'version' => '1.0.0',
    ]);
});

// Login dibatasi 5 percobaan per menit per IP
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/auth/login', [AuthController::class, 'login']);
});

// Data publik: GET /potensi hanya menampilkan yang disetujui bagi pengunjung biasa
Route::get('/potensi', [PotensiController::class, 'index']);
Route::get('/potensi/{id}', [PotensiController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/settings/logo', [SettingController::class, 'getLogo']);

// Formulir publik (dibatasi agar tidak bisa dipakai spam)
Route::post('/potensi', [PotensiController::class, 'store'])->middleware('throttle:10,1');
Route::post('/messages', [MessageController::class, 'store'])->middleware('throttle:5,1');

/*
|--------------------------------------------------------------------------
| Rute admin (wajib token Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/verify', [AuthController::class, 'verify']);

    Route::put('/potensi/{id}', [PotensiController::class, 'update']);
    Route::delete('/potensi/{id}', [PotensiController::class, 'destroy']);
    Route::post('/potensi/{id}/review', [PotensiReviewController::class, 'review']);
    Route::post('/potensi-review/{id}', [PotensiReviewController::class, 'review']);

    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    Route::get('/messages', [MessageController::class, 'index']);
    Route::get('/stats', [StatsController::class, 'index']);
    Route::post('/upload', [UploadController::class, 'upload']);

    Route::get('/admin/profile', [ProfileController::class, 'show']);
    Route::post('/admin/profile', [ProfileController::class, 'update']);
    Route::put('/admin/password', [ProfileController::class, 'changePassword']);
    Route::post('/settings/logo', [SettingController::class, 'uploadLogo']);
});
