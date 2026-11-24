<?php

use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::view('/', 'index')->name('home');
Route::redirect('/index.html', '/', 301);

<<<<<<< HEAD
// Halaman admin dilindungi AdminPageMiddleware (server-side token check)
Route::middleware(\App\Http\Middleware\AdminPageMiddleware::class)->group(function () {
    Route::get('/admin.html', fn() => view('admin'));
    Route::get('/admin', fn() => view('admin'));
});
=======
Route::view('/admin', 'admin')->name('admin');
Route::redirect('/admin.html', '/admin', 301);

Route::view('/daftar-potensi', 'daftar-potensi')->name('daftar-potensi');
Route::redirect('/daftar-potensi.html', '/daftar-potensi', 301);
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2

Route::view('/login', 'login')->name('login');
Route::redirect('/login.html', '/login', 301);

Route::view('/register', 'register')->name('register');
Route::redirect('/register.html', '/register', 301);

<<<<<<< HEAD
Route::get('/login.html', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/register', function () {
    return view('login'); // Menuju tab daftar di halaman login/register
});
=======
Route::view('/profil-warga', 'profil-warga')->name('profil-warga');
Route::redirect('/profil-warga.html', '/profil-warga', 301);
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
