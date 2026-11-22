<?php

use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::view('/', 'index')->name('home');
Route::redirect('/index.html', '/', 301);

Route::view('/admin', 'admin')->name('admin');
Route::redirect('/admin.html', '/admin', 301);

Route::view('/daftar-potensi', 'daftar-potensi')->name('daftar-potensi');
Route::redirect('/daftar-potensi.html', '/daftar-potensi', 301);

Route::view('/login', 'login')->name('login');
Route::redirect('/login.html', '/login', 301);

Route::view('/register', 'register')->name('register');
Route::redirect('/register.html', '/register', 301);

Route::view('/profil-warga', 'profil-warga')->name('profil-warga');
Route::redirect('/profil-warga.html', '/profil-warga', 301);
