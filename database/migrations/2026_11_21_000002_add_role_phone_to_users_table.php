<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan kolom role dan phone ke tabel users.
 * - role: 'admin' untuk admin desa, 'warga' untuk warga terdaftar.
 *   Default 'admin' agar user lama (admin yang sudah ada) tetap berfungsi.
 * - phone: nomor WhatsApp/HP warga, dipakai sebagai alternatif login.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('admin')->after('avatar');
            $table->string('phone', 20)->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone']);
        });
    }
};
