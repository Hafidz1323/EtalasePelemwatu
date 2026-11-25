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
    }
}
