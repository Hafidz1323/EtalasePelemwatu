<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('potensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('owner_name');
            $table->string('whatsapp')->nullable();
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('aktif'); // aktif, pending, approved, rejected
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('potensi_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potensi_id')->constrained('potensi')->cascadeOnDelete();
            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('potensi_images');
        Schema::dropIfExists('potensi');
        Schema::dropIfExists('categories');
    }
};

