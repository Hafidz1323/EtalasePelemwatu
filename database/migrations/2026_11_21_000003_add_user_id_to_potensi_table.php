<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan kolom user_id ke tabel potensi.
 * Nullable agar potensi yang didaftarkan anonim (sebelum fitur warga) tetap valid.
 * nullOnDelete: jika user dihapus, potensinya tidak ikut terhapus (tetap di database).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('potensi', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('potensi', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
