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

// Status
Route::get('/status', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API EtalasePelemwatu Laravel aktif',
        'version' => '1.0.0',
    ]);
});

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::get('/auth/verify', [AuthController::class, 'verify']);

// Potensi
Route::get('/potensi', [PotensiController::class, 'index']);
Route::post('/potensi', [PotensiController::class, 'store']);
Route::get('/potensi/{id}', [PotensiController::class, 'show']);
Route::put('/potensi/{id}', [PotensiController::class, 'update']);
Route::delete('/potensi/{id}', [PotensiController::class, 'destroy']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

// Messages
Route::get('/messages', [MessageController::class, 'index']);
Route::post('/messages', [MessageController::class, 'store']);

// Stats & Review
Route::get('/stats', [StatsController::class, 'index']);
Route::post('/potensi/{id}/review', [PotensiReviewController::class, 'review']);
Route::post('/potensi-review/{id}', [PotensiReviewController::class, 'review']);

// Upload
Route::post('/upload', [UploadController::class, 'upload']);

// Admin Profile (protected by Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/profile', [ProfileController::class, 'show']);
    Route::post('/admin/profile', [ProfileController::class, 'update']);
    Route::put('/admin/password', [ProfileController::class, 'changePassword']);
    Route::post('/settings/logo', [SettingController::class, 'uploadLogo']);
});

// Settings (public)
Route::get('/settings/logo', [SettingController::class, 'getLogo']);

