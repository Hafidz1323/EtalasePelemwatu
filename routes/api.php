<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\GaleriController;
use App\Http\Controllers\API\PotensiController;
use App\Http\Controllers\API\PotensiReviewController;
use App\Http\Controllers\API\ProfilDesaController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\StatsController;
use App\Http\Controllers\API\UploadController;
use App\Http\Controllers\API\WargaAuthController;
use App\Http\Controllers\API\WargaPotensiController;
use App\Http\Controllers\API\WargaProfileController;
use Illuminate\Support\Facades\Route;

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

// Login dibatasi oleh RateLimiter 'login' (5x/menit per email+IP, didefinisikan di AppServiceProvider)
Route::middleware('throttle:login')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Data publik: GET /potensi hanya menampilkan yang disetujui bagi pengunjung biasa
Route::get('/potensi', [PotensiController::class, 'index']);
Route::get('/potensi/{id}', [PotensiController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/galeri', [GaleriController::class, 'index']);
Route::get('/stats/public', [StatsController::class, 'publicStats']);
Route::get('/settings/logo', [SettingController::class, 'getLogo']);

// Formulir publik (dibatasi agar tidak bisa dipakai spam)
Route::post('/potensi', [PotensiController::class, 'store'])->middleware('throttle:10,1');

/*
|--------------------------------------------------------------------------
| Rute bersama terautentikasi (token Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class,      'logout']);
    Route::get('/auth/verify', [AuthController::class,      'verify']);
    Route::post('/warga/logout', [WargaAuthController::class, 'logout']);
    Route::get('/warga/verify', [WargaAuthController::class,  'verify']);
});

/*
|--------------------------------------------------------------------------
| Rute admin (wajib token Sanctum + role admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/deletion-requests', [PotensiReviewController::class, 'deletionRequests']);
    Route::post('/admin/deletion-requests/{id}/approve', [PotensiReviewController::class, 'approveDeletion']);
    Route::post('/admin/deletion-requests/{id}/reject', [PotensiReviewController::class, 'rejectDeletion']);

    Route::put('/potensi/{id}', [PotensiController::class, 'update']);
    Route::delete('/potensi/{id}', [PotensiController::class, 'destroy']);
    Route::post('/potensi/{id}/review', [PotensiReviewController::class, 'review']);

    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    Route::post('/galeri', [GaleriController::class, 'store']);
    Route::put('/galeri/{id}', [GaleriController::class, 'update']);
    Route::delete('/galeri/{id}', [GaleriController::class, 'destroy']);

    Route::get('/stats', [StatsController::class, 'index']);
    Route::post('/upload', [UploadController::class, 'upload']);

    Route::get('/admin/profile', [ProfileController::class, 'show']);
    Route::post('/admin/profile', [ProfileController::class, 'update']);
    Route::put('/admin/password', [ProfileController::class, 'changePassword']);
    Route::post('/settings/logo', [SettingController::class, 'uploadLogo']);
    Route::get('/admin/profil-desa', [ProfilDesaController::class, 'show']);
    Route::put('/admin/profil-desa', [ProfilDesaController::class, 'update']);
});

/*
|--------------------------------------------------------------------------
| Rute warga (registrasi & login)
|--------------------------------------------------------------------------
*/
Route::post('/warga/register', [WargaAuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/warga/login', [WargaAuthController::class, 'login'])->middleware('throttle:warga-login');

/*
|--------------------------------------------------------------------------
| Rute warga terautentikasi (token Sanctum + role warga)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:warga'])->group(function () {
    Route::get('/warga/profile', [WargaProfileController::class, 'show']);
    Route::post('/warga/profile', [WargaProfileController::class, 'update']);
    Route::put('/warga/password', [WargaProfileController::class, 'changePassword']);

    Route::get('/warga/potensi', [WargaPotensiController::class, 'index']);
    Route::get('/warga/potensi/{id}', [WargaPotensiController::class, 'show']);
    Route::post('/warga/potensi/{id}', [WargaPotensiController::class, 'update']);
    Route::delete('/warga/potensi/{id}', [WargaPotensiController::class, 'destroy']);
    Route::post('/warga/potensi/{id}/request-deletion', [WargaPotensiController::class, 'requestDeletion']);
    Route::post('/warga/potensi/{id}/cancel-deletion', [WargaPotensiController::class, 'cancelDeletion']);
    Route::delete('/warga/potensi/{id}/images/{imageId}', [WargaPotensiController::class, 'destroyImage']);
});
