<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('warga')->after('password');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }
        });

        Schema::table('potensi', function (Blueprint $table) {
            if (!Schema::hasColumn('potensi', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('potensi', function (Blueprint $table) {
            if (Schema::hasColumn('potensi', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('users', 'address')) $cols[] = 'address';
            if (Schema::hasColumn('users', 'phone')) $cols[] = 'phone';
            if (Schema::hasColumn('users', 'role')) $cols[] = 'role';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
