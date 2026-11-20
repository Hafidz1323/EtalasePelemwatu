<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom deletion_requested_at ke tabel potensi.
 * Diisi saat warga mengajukan permintaan hapus ke admin.
 * Null = tidak ada permintaan; not-null = sedang menunggu persetujuan admin.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('potensi', function (Blueprint $table) {
            $table->timestamp('deletion_requested_at')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('potensi', function (Blueprint $table) {
            $table->dropColumn('deletion_requested_at');
        });
    }
};
