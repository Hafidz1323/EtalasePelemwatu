<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Bersihkan token Sanctum yang kedaluwarsa secara berkala
Schedule::command('sanctum:prune-expired --hours=24')->daily();

// Backup harian database dan uploads (simpan 7 arsip terakhir)
Schedule::command('backup:run')->daily();
