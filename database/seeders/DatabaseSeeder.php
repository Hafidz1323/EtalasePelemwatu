<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Default
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@pelemwatu.desa.id'],
            [
                'name' => 'Administrator Desa',
                'password' => Hash::make('admin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. Kategori Default
        $categories = [
            ['id' => 1, 'name' => 'Usaha & Produk', 'description' => 'Usaha kuliner, kerajinan, dan produk olahan warga desa'],
            ['id' => 2, 'name' => 'Jasa', 'description' => 'Layanan jasa dan keahlian masyarakat desa'],
            ['id' => 3, 'name' => 'Hunian', 'description' => 'Kost, kontrakan, dan akomodasi tempat tinggal di desa'],
            ['id' => 4, 'name' => 'Barang Warga', 'description' => 'Barang dan produk yang dijual atau disewakan warga desa'],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(['id' => $cat['id']], array_merge($cat, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 3. Potensi Bawaan
        $potensiList = [
            [
                'id' => 1,
                'category_id' => 1,
                'name' => 'Omah Dimsum',
                'owner_name' => 'Bapak Ahmad',
                'whatsapp' => '081234567890',
                'address' => 'Jl. Melati No. 12 Pelemwatu',
                'description' => 'Usaha kuliner yang menyajikan berbagai jenis dimsum pilihan dengan cita rasa khas.',
                'location' => 'Desa Pelemwatu, Menganti, Gresik',
                'status' => 'aktif',
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'name' => 'Warung Prasmanan Selarasnasi',
                'owner_name' => 'Ibu Siti',
                'whatsapp' => '081234567891',
                'address' => 'Jl. Raya Pelemwatu No. 45',
                'description' => 'Warung makan prasmanan dengan menu masakan rumahan yang beragam setiap harinya.',
                'location' => 'Desa Pelemwatu, Menganti, Gresik',
                'status' => 'aktif',
            ],
            [
                'id' => 3,
                'category_id' => 1,
                'name' => 'Ratu Frozen Food Pelemwatu',
                'owner_name' => 'Ibu Ratna',
                'whatsapp' => '081234567892',
                'address' => 'Dusun Pelemwatu RT 03',
                'description' => 'Usaha penjualan produk frozen food berkualitas untuk kebutuhan rumah tangga dan kuliner.',
                'location' => 'Desa Pelemwatu, Menganti, Gresik',
                'status' => 'aktif',
            ],
            [
                'id' => 4,
                'category_id' => 2,
                'name' => 'Moni Laundry 24 Jam',
                'owner_name' => 'Mas Bayu',
                'whatsapp' => '081234567893',
                'address' => 'Jl. Masjid Pelemwatu',
                'description' => 'Layanan jasa laundry yang beroperasi 24 jam untuk memenuhi kebutuhan masyarakat.',
                'location' => 'Desa Pelemwatu, Menganti, Gresik',
                'status' => 'aktif',
            ],
            [
                'id' => 5,
                'category_id' => 2,
                'name' => 'One Laundry Koin',
                'owner_name' => 'Pak Doni',
                'whatsapp' => '081234567894',
                'address' => 'Jl. Raya Pelemwatu No. 88',
                'description' => 'Layanan laundry mandiri berbasis koin yang praktis dan terjangkau bagi warga desa.',
                'location' => 'Desa Pelemwatu, Menganti, Gresik',
                'status' => 'aktif',
            ],
            [
                'id' => 6,
                'category_id' => 3,
                'name' => 'My Kost',
                'owner_name' => 'Pak Hendra',
                'whatsapp' => '081234567895',
                'address' => 'Gang Mawar No. 5 Pelemwatu',
                'description' => 'Hunian kost nyaman dan terjangkau di lingkungan Desa Pelemwatu.',
                'location' => 'Desa Pelemwatu, Menganti, Gresik',
                'status' => 'aktif',
            ],
            [
                'id' => 7,
                'category_id' => 3,
                'name' => 'RAMA Kost',
                'owner_name' => 'Pak Rama',
                'whatsapp' => '081234567896',
                'address' => 'Jl. Dahlia Pelemwatu',
                'description' => 'Pilihan hunian kost dengan fasilitas lengkap di lingkungan desa.',
                'location' => 'Desa Pelemwatu, Menganti, Gresik',
                'status' => 'aktif',
            ],
            [
                'id' => 8,
                'category_id' => 4,
                'name' => 'Toko Kelontong Berkah Warga',
                'owner_name' => 'Bu Sumiati',
                'whatsapp' => '081234567897',
                'address' => 'Jl. Balai Desa No. 02',
                'description' => 'Menyediakan aneka sembako, jajanan dan perlengkapan rumah tangga dengan harga bersahabat.',
                'location' => 'Desa Pelemwatu, Menganti, Gresik',
                'status' => 'aktif',
            ],
        ];

        foreach ($potensiList as $p) {
            DB::table('potensi')->updateOrInsert(['id' => $p['id']], array_merge($p, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
