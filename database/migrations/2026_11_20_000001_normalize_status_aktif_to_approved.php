<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        // Ubah semua baris yang masih 'aktif' menjadi 'approved'
        DB::table('potensi')->where('status', 'aktif')->update(['status' => 'approved']);

        // Ubah default kolom dari 'aktif' ke 'approved'
        Schema::table('potensi', function (Blueprint $table) {
            $table->string('status')->default('approved')->change();
        });
    }

    public function down(): void
    {
        Schema::table('potensi', function (Blueprint $table) {
            $table->string('status')->default('aktif')->change();
        });

        DB::table('potensi')->where('status', 'approved')->update(['status' => 'aktif']);
    }
};
