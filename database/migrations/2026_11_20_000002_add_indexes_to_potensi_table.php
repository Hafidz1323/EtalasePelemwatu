<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('potensi', function (Blueprint $table) {
            // Indeks tunggal untuk filter by status
            $table->index('status', 'idx_potensi_status');
            // Indeks komposit untuk filter status + category_id bersamaan
            $table->index(['status', 'category_id'], 'idx_potensi_status_category');
        });
    }

    public function down(): void
    {
        Schema::table('potensi', function (Blueprint $table) {
            $table->dropIndex('idx_potensi_status');
            $table->dropIndex('idx_potensi_status_category');
        });
    }
};
