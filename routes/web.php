<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/index.html', function () {
    return view('index');
});

// Halaman admin dilindungi AdminPageMiddleware (server-side token check)
Route::middleware(\App\Http\Middleware\AdminPageMiddleware::class)->group(function () {
    Route::get('/admin.html', fn() => view('admin'));
    Route::get('/admin', fn() => view('admin'));
});

Route::get('/daftar-potensi.html', function () {
    return view('daftar-potensi');
});

Route::get('/daftar-potensi', function () {
    return view('daftar-potensi');
});

Route::get('/login.html', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/register', function () {
    return view('login'); // Menuju tab daftar di halaman login/register
});
