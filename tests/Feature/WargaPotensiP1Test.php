<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Potensi;
use App\Models\PotensiImage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WargaPotensiP1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Pastikan kategori dasar tersedia
        Category::firstOrCreate(['id' => 1], [
            'name' => 'Usaha & Produk',
            'description' => 'Usaha kuliner dan produk olahan warga',
        ]);
        Category::firstOrCreate(['id' => 2], [
            'name' => 'Jasa',
            'description' => 'Layanan jasa masyarakat',
        ]);
    }

    /**
     * 1. Test Registrasi Warga
     */
    public function test_warga_dapat_registrasi_dan_login(): void
    {
        $email = 'warga_test_' . time() . '@pelemwatu.desa.id';

        // Register
        $regResponse = $this->postJson('/api/register', [
            'name'                  => 'Budi Santoso',
            'email'                 => $email,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'phone'                 => '081234567899',
            'address'               => 'Dusun Pelemwatu RT 01',
        ]);

        $regResponse->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.user.role', 'warga');

        $token = $regResponse->json('data.token');
        $this->assertNotEmpty($token);

        // Login
        $loginResponse = $this->postJson('/api/login', [
            'email'    => $email,
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.user.email', $email);
    }

    /**
     * 2. Test Profil Warga
     */
    public function test_warga_dapat_melihat_dan_update_profil(): void
    {
        $user = User::create([
            'name'     => 'Siti Aminah',
            'email'    => 'siti_' . time() . '@pelemwatu.desa.id',
            'password' => Hash::make('password123'),
            'role'     => 'warga',
            'phone'    => '081233344455',
            'address'  => 'RT 02 RW 01',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/user/profile');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.user.name', 'Siti Aminah');

        // Update profil
        $updateRes = $this->actingAs($user, 'sanctum')->putJson('/api/user/profile', [
            'name'    => 'Siti Aminah M.Pd',
            'phone'   => '081999888777',
            'address' => 'RT 03 RW 01 Pelemwatu',
        ]);

        $updateRes->assertStatus(200)
            ->assertJsonPath('data.name', 'Siti Aminah M.Pd');

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => 'Siti Aminah M.Pd',
            'phone' => '081999888777',
        ]);
    }

    /**
     * 3. Test Relasi Potensi dengan Akun Warga & Pendaftaran
     */
    public function test_warga_mendaftarkan_potensi_terelasi_dengan_user_id(): void
    {
        $user = User::create([
            'name'     => 'Joko Tarub',
            'email'    => 'joko_' . time() . '@pelemwatu.desa.id',
            'password' => Hash::make('password123'),
            'role'     => 'warga',
        ]);

        $payload = [
            'category_id' => 1,
            'name'        => 'Warung Nasi Pecel Joko',
            'owner_name'  => 'Joko Tarub',
            'whatsapp'    => '081298765432',
            'address'     => 'Jl. Balai Desa No. 5',
            'description' => 'Nasi pecel pincuk khas Pelemwatu',
            'location'    => 'Dusun Krajan',
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/potensi', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.whatsapp', '6281298765432'); // Sanitasi WA berhasil

        $this->assertDatabaseHas('potensi', [
            'name'    => 'Warung Nasi Pecel Joko',
            'user_id' => $user->id,
            'status'  => 'pending',
        ]);
    }

    /**
     * 4. Test Warga Dapat Mengedit Potensi Miliknya
     */
    public function test_warga_dapat_mengedit_potensi_miliknya_sendiri(): void
    {
        $pemilik = User::create([
            'name'     => 'Pemilik Asli',
            'email'    => 'pemilik_' . time() . '@pelemwatu.desa.id',
            'password' => Hash::make('secret123'),
            'role'     => 'warga',
        ]);

        $orangLain = User::create([
            'name'     => 'Orang Lain',
            'email'    => 'oranglain_' . time() . '@pelemwatu.desa.id',
            'password' => Hash::make('secret123'),
            'role'     => 'warga',
        ]);

        $potensi = Potensi::create([
            'user_id'     => $pemilik->id,
            'category_id' => 1,
            'name'        => 'Bengkel Las Pak Joko',
            'owner_name'  => 'Pak Joko',
            'whatsapp'    => '628111222333',
            'status'      => 'approved',
        ]);

        // Orang lain mencoba mengedit -> ditolak (403)
        $unauthRes = $this->actingAs($orangLain, 'sanctum')->putJson("/api/potensi/{$potensi->id}", [
            'name' => 'Dibajak Orang Lain',
        ]);
        $unauthRes->assertStatus(403);

        // Pemilik mengedit -> berhasil (200)
        $authRes = $this->actingAs($pemilik, 'sanctum')->putJson("/api/potensi/{$potensi->id}", [
            'name'        => 'Bengkel Las Pelemwatu Makmur',
            'description' => 'Menerima pesanan pagar dan kanopi',
        ]);
        $authRes->assertStatus(200);

        $this->assertDatabaseHas('potensi', [
            'id'   => $potensi->id,
            'name' => 'Bengkel Las Pelemwatu Makmur',
        ]);
    }

    /**
     * 5. Test Admin Memverifikasi & Menolak Potensi
     */
    public function test_admin_dapat_verifikasi_dan_menolak_potensi(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@pelemwatu.desa.id'],
            [
                'name'     => 'Administrator Desa',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        $potensi = Potensi::create([
            'category_id' => 2,
            'name'        => 'Laundry Kilat Pelemwatu',
            'owner_name'  => 'Bu Sari',
            'status'      => 'pending',
        ]);

        // 1. Verifikasi Approve
        $approveRes = $this->actingAs($admin, 'sanctum')->postJson("/api/potensi/{$potensi->id}/review", [
            'status' => 'approved',
        ]);

        $approveRes->assertStatus(200)
            ->assertJsonPath('data.status', 'approved');

        $this->assertDatabaseHas('potensi', [
            'id'          => $potensi->id,
            'status'      => 'approved',
            'reviewed_by' => $admin->id,
        ]);

        // 2. Reject Potensi
        $rejectRes = $this->actingAs($admin, 'sanctum')->postJson("/api/potensi/{$potensi->id}/review", [
            'status' => 'rejected',
            'reason' => 'Lokasi usaha berada di luar wilayah Desa Pelemwatu',
        ]);

        $rejectRes->assertStatus(200)
            ->assertJsonPath('data.status', 'rejected')
            ->assertJsonPath('data.rejection_reason', 'Lokasi usaha berada di luar wilayah Desa Pelemwatu');
    }

    /**
     * 6. Test Katalog Hanya Menampilkan yang Disetujui (Approved / Aktif)
     */
    public function test_katalog_hanya_menampilkan_potensi_yang_disetujui(): void
    {
        Potensi::create([
            'category_id' => 1,
            'name'        => 'Potensi Disetujui',
            'owner_name'  => 'Warga Sah',
            'status'      => 'approved',
        ]);

        Potensi::create([
            'category_id' => 1,
            'name'        => 'Potensi Masih Pending',
            'owner_name'  => 'Warga Baru',
            'status'      => 'pending',
        ]);

        Potensi::create([
            'category_id' => 1,
            'name'        => 'Potensi Ditolak Admin',
            'owner_name'  => 'Warga Ditolak',
            'status'      => 'rejected',
        ]);

        $response = $this->getJson('/api/potensi');

        $response->assertStatus(200);
        $names = collect($response->json('data'))->pluck('name')->toArray();

        $this->assertContains('Potensi Disetujui', $names);
        $this->assertNotContains('Potensi Masih Pending', $names);
        $this->assertNotContains('Potensi Ditolak Admin', $names);
    }

    /**
     * 7. Test Pencarian dan Filter Kategori Berjalan Benar
     */
    public function test_pencarian_dan_kategori_berjalan_benar(): void
    {
        Potensi::create([
            'category_id' => 1,
            'name'        => 'Keripik Tempe Renyah Pelemwatu',
            'owner_name'  => 'Bu Endang',
            'description' => 'Camilan gurih renyah',
            'status'      => 'approved',
        ]);

        // Cari berdasarkan nama
        $searchRes = $this->getJson('/api/potensi?search=Renyah');
        $searchRes->assertStatus(200);
        $this->assertTrue(collect($searchRes->json('data'))->contains('name', 'Keripik Tempe Renyah Pelemwatu'));

        // Filter kategori
        $catRes = $this->getJson('/api/potensi?category=1');
        $catRes->assertStatus(200);
        $this->assertTrue(collect($catRes->json('data'))->every(fn($item) => $item['category_id'] === 1));
    }

    /**
     * 8. Test Penanganan Foto dan Penghapusan Data Tanpa Merusak DB
     */
    public function test_penanganan_foto_dan_hapus_potensi_cascade(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@pelemwatu.desa.id'],
            ['name' => 'Admin', 'role' => 'admin', 'password' => Hash::make('admin123')]
        );

        $potensi = Potensi::create([
            'category_id' => 1,
            'name'        => 'Toko Roti Makmur',
            'owner_name'  => 'Ibu Maya',
            'status'      => 'approved',
        ]);

        $foto1 = PotensiImage::create([
            'potensi_id' => $potensi->id,
            'image_path' => '/uploads/test-foto1.jpg',
        ]);

        $foto2 = PotensiImage::create([
            'potensi_id' => $potensi->id,
            'image_path' => '/uploads/test-foto2.jpg',
        ]);

        $this->assertDatabaseHas('potensi_images', ['id' => $foto1->id]);
        $this->assertDatabaseHas('potensi_images', ['id' => $foto2->id]);

        // Hapus potensi oleh admin
        $delRes = $this->actingAs($admin, 'sanctum')->deleteJson("/api/potensi/{$potensi->id}");
        $delRes->assertStatus(200);

        // Potensi terhapus & gambar terhapus cascade
        $this->assertDatabaseMissing('potensi', ['id' => $potensi->id]);
        $this->assertDatabaseMissing('potensi_images', ['id' => $foto1->id]);
        $this->assertDatabaseMissing('potensi_images', ['id' => $foto2->id]);
    }

    /**
     * 9. Test Statistik Menampilkan Data Aktual
     */
    public function test_statistik_menampilkan_data_aktual(): void
    {
        $response = $this->getJson('/api/stats/public');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'data' => [
                    'totalPotensi',
                    'potensiAktif',
                    'pendingReviews',
                    'rejectedPotensi',
                    'totalWarga',
                    'perCategory',
                ],
            ]);

        $data = $response->json('data');
        $this->assertIsInt($data['totalPotensi']);
        $this->assertIsInt($data['potensiAktif']);
        $this->assertIsArray($data['perCategory']);
    }
}
