<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('potensi', function (Blueprint $table) {
            $table->string('email', 100)->nullable()->after('whatsapp');
            $table->string('jenis', 100)->nullable()->after('location');
            $table->string('jam_operasional', 100)->nullable()->after('jenis');
        });
    }

    public function down(): void
    {
        Schema::table('potensi', function (Blueprint $table) {
            $table->dropColumn(['email', 'jenis', 'jam_operasional']);
        });
    }
};
