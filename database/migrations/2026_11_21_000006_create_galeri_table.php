<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galeri', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->string('caption', 100);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Masukkan 5 data bawaan galeri kegiatan desa
        $defaultItems = [
            [
                'image_path' => 'assets/img/kegiatan-kantor-desa.jpg',
                'caption' => 'Rapat Koordinasi Desa',
                'description' => 'Kegiatan rapat koordinasi rutin yang dihadiri oleh perangkat desa dan perwakilan warga untuk membahas program dan kebijakan desa.',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image_path' => 'assets/img/kegiatan-kerja-bakti.jpg',
                'caption' => 'Kerja Bakti Warga',
                'description' => 'Kegiatan gotong royong membersihkan lingkungan desa yang diikuti oleh seluruh lapisan masyarakat Desa Pelemwatu secara sukarela.',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image_path' => 'assets/img/kegiatan-penghijauan.jpg',
                'caption' => 'Program Penghijauan',
                'description' => 'Program penanaman pohon dan tanaman di area desa sebagai upaya menjaga lingkungan hijau dan asri di Desa Pelemwatu.',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image_path' => 'assets/img/foto-kantor-desa.jpg',
                'caption' => 'Kantor Desa Pelemwatu',
                'description' => 'Kantor Desa Pelemwatu sebagai pusat pelayanan administrasi dan pemerintahan desa yang melayani kebutuhan warga setiap hari kerja.',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image_path' => 'assets/img/foto-warung-desa.jpg',
                'caption' => 'Warung Usaha Warga',
                'description' => 'Salah satu contoh usaha kuliner milik warga Desa Pelemwatu yang berhasil berkembang dan menjadi bagian dari potensi ekonomi lokal desa.',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('galeri')->insert($defaultItems);
    }

    public function down(): void
    {
        Schema::dropIfExists('galeri');
    }
};
