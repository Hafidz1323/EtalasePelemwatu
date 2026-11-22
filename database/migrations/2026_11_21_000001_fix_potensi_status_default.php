<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Perbaiki default kolom status pada tabel potensi.
 *
 * Migrasi awal (2026_11_11) memakai default('aktif') yang tidak konsisten
 * dengan STATUS_PUBLIK = ['approved']. Migrasi ini mengubah default menjadi
 * 'pending' agar fresh install langsung benar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('potensi', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('potensi', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};
