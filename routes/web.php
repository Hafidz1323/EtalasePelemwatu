<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/index.html', function () {
    return view('index');
});

Route::get('/admin.html', function () {
    return view('admin');
});

Route::get('/admin', function () {
    return view('admin');
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
