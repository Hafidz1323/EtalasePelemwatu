<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin awal: buat hanya jika email belum ada.
        //    Jika sudah ada, password dan name TIDAK diubah.
        $email = strtolower(config('admin.email'));
        $exists = DB::table('users')->where('email', $email)->exists();

        if (! $exists) {
            $password = config('admin.password');
            $generated = false;

            if (empty($password)) {
                $password = Str::password(16, symbols: false);
                $generated = true;
            }

            DB::table('users')->insert([
                'name' => config('admin.name'),
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($this->command) {
                if ($generated) {
                    $this->command->warn('ADMIN_PASSWORD belum diatur di .env. Password sementara dibuat acak:');
                    $this->command->warn('  '.$email.' / '.$password);
                    $this->command->warn('Catat sekarang lalu ganti lewat menu profil admin (tidak akan ditampilkan lagi).');
                } else {
                    $this->command->info('Akun admin baru dibuat: '.$email);
                }
            }
        }
        // Jika sudah ada: diam saja — password yang sudah diubah tetap utuh.

        // 2. Kategori default: tetap idempoten (data master).
        $categories = [
            ['id' => 1, 'name' => 'Usaha & Produk', 'description' => 'Usaha kuliner, kerajinan, dan produk olahan warga desa'],
            ['id' => 2, 'name' => 'Jasa',            'description' => 'Layanan jasa dan keahlian masyarakat desa'],
            ['id' => 3, 'name' => 'Hunian',          'description' => 'Kost, kontrakan, dan akomodasi tempat tinggal di desa'],
            ['id' => 4, 'name' => 'Barang Warga',    'description' => 'Barang dan produk yang dijual atau disewakan warga desa'],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(['id' => $cat['id']], array_merge($cat, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 3. Galeri kegiatan awal
        $defaultGaleri = [
            [
                'id' => 1,
                'image_path' => 'assets/img/kegiatan-kantor-desa.jpg',
                'caption' => 'Rapat Koordinasi Desa',
                'description' => 'Kegiatan rapat koordinasi rutin yang dihadiri oleh perangkat desa dan perwakilan warga untuk membahas program dan kebijakan desa.',
                'sort_order' => 1,
            ],
            [
                'id' => 2,
                'image_path' => 'assets/img/kegiatan-kerja-bakti.jpg',
                'caption' => 'Kerja Bakti Warga',
                'description' => 'Kegiatan gotong royong membersihkan lingkungan desa yang diikuti oleh seluruh lapisan masyarakat Desa Pelemwatu secara sukarela.',
                'sort_order' => 2,
            ],
            [
                'id' => 3,
                'image_path' => 'assets/img/kegiatan-penghijauan.jpg',
                'caption' => 'Program Penghijauan',
                'description' => 'Program penanaman pohon dan tanaman di area desa sebagai upaya menjaga lingkungan hijau dan asri di Desa Pelemwatu.',
                'sort_order' => 3,
            ],
            [
                'id' => 4,
                'image_path' => 'assets/img/foto-kantor-desa.jpg',
                'caption' => 'Kantor Desa Pelemwatu',
                'description' => 'Kantor Desa Pelemwatu sebagai pusat pelayanan administrasi dan pemerintahan desa yang melayani kebutuhan warga setiap hari kerja.',
                'sort_order' => 4,
            ],
            [
                'id' => 5,
                'image_path' => 'assets/img/foto-warung-desa.jpg',
                'caption' => 'Warung Usaha Warga',
                'description' => 'Salah satu contoh usaha kuliner milik warga Desa Pelemwatu yang berhasil berkembang dan menjadi bagian dari potensi ekonomi lokal desa.',
                'sort_order' => 5,
            ],
        ];

        foreach ($defaultGaleri as $g) {
            DB::table('galeri')->updateOrInsert(['id' => $g['id']], array_merge($g, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 4. Data demo potensi: hanya di environment local/testing.
        if (app()->environment('local', 'testing')) {
            $this->call(DemoPotensiSeeder::class);
        }
    }
}
