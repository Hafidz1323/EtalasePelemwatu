<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Galeri;
use App\Models\Potensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DataSyncTest extends TestCase
{
    use RefreshDatabase;

    private array $createdFiles = [];

    protected function setUp(): void
    {
        parent::setUp();
        if (! is_dir(public_path('uploads'))) {
            mkdir(public_path('uploads'), 0755, true);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFiles as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }
        parent::tearDown();
    }

    private function trackFile(?string $path): void
    {
        if ($path) {
            // Bersihkan query param ?v= bila ada
            $cleanPath = explode('?', $path)[0];
            $this->createdFiles[] = public_path(ltrim($cleanPath, '/\\'));
        }
    }

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin Pelemwatu',
            'email' => 'admin@pelemwatu.desa.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
    }

    private function makeCategory(string $name = 'Usaha & Produk'): Category
    {
        return Category::firstOrCreate(['name' => $name]);
    }

    /**
     * Skenario 1:
     * Warga register -> login -> kirim potensi -> TIDAK tampil di GET /api/potensi publik
     * dan tidak menambah angka UMKM di GET / maupun GET /api/stats/public.
     */
    public function test_skenario_1_warga_kirim_potensi_tidak_langsung_tampil_ke_publik(): void
    {
        $cat = $this->makeCategory('Usaha & Produk');

        // Warga register
        $this->postJson('/api/warga/register', [
            'name' => 'Budi Warga',
            'phone' => '081298765432',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        // Login
        $loginRes = $this->postJson('/api/warga/login', [
            'login' => '081298765432',
            'password' => 'password123',
        ])->assertOk();

        $token = $loginRes->json('data.token');
        $this->assertNotEmpty($token);

        // Cek UMKM sebelum submit
        $initialStats = $this->getJson('/api/stats/public')->assertOk()->json('data.umkm');

        // Warga submit potensi
        $submitRes = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/potensi', [
                'name' => 'Kripik Tempe Bu Budi',
                'owner_name' => 'Budi Warga',
                'category_id' => $cat->id,
                'whatsapp' => '081298765432',
                'address' => 'Dusun Krajan RT 02 RW 01',
                'description' => 'Produksi kripik tempe renyah dan gurih khas Pelemwatu',
            ])->assertCreated();

        $potensiId = $submitRes->json('data.id');

        // Potensi status pending di tabel potensi
        $this->assertDatabaseHas('potensi', [
            'id' => $potensiId,
            'status' => 'pending',
        ]);

        // Tidak tampil di GET /api/potensi publik
        $publicPotensi = $this->getJson('/api/potensi')->assertOk()->json('data');
        $this->assertFalse(collect($publicPotensi)->contains('id', $potensiId));

        // Angka UMKM publik tidak bertambah
        $currentStats = $this->getJson('/api/stats/public')->assertOk()->json('data.umkm');
        $this->assertSame($initialStats, $currentStats);

        // Di homepage GET / tetap berhasil dibuka
        $this->get('/')->assertOk();
    }

    /**
     * Skenario 2:
     * Admin approve -> tampil di publik; angka UMKM bertambah 1.
     */
    public function test_skenario_2_admin_approve_tampil_di_publik_dan_tambah_angka_umkm(): void
    {
        $admin = $this->makeAdmin();
        $cat = $this->makeCategory('Usaha & Produk');

        $potensi = Potensi::create([
            'category_id' => $cat->id,
            'name' => 'Warung Kopi Krajan',
            'owner_name' => 'Pak Joko',
            'whatsapp' => '081234567891',
            'address' => 'Jl. Desa Pelemwatu Barat No. 5',
            'description' => 'Warung kopi tradisional dan aneka gorengan hangat.',
            'status' => 'pending',
        ]);

        $initialUmkm = (int) $this->getJson('/api/stats/public')->json('data.umkm');

        // Admin approve
        Sanctum::actingAs($admin);
        $this->postJson("/api/potensi/{$potensi->id}/review", [
            'status' => 'approved',
        ])->assertOk();

        // Tampil di GET /api/potensi publik
        $publicList = $this->getJson('/api/potensi')->assertOk()->json('data');
        $this->assertTrue(collect($publicList)->contains('id', $potensi->id));

        // Angka UMKM publik bertambah 1
        $newUmkm = (int) $this->getJson('/api/stats/public')->json('data.umkm');
        $this->assertSame($initialUmkm + 1, $newUmkm);

        // Homepage GET / render angka baru
        $home = $this->get('/')->assertOk();
        $home->assertSee((string) $newUmkm);
    }

    /**
     * Skenario 3:
     * Admin reject potensi lain dengan alasan -> alasan terlihat di GET /api/warga/potensi milik warga itu.
     */
    public function test_skenario_3_admin_reject_dengan_alasan_terlihat_oleh_warga(): void
    {
        $admin = $this->makeAdmin();
        $cat = $this->makeCategory('Jasa');

        $warga = User::create([
            'name' => 'Warga Service',
            'phone' => '081234567892',
            'password' => Hash::make('password123'),
            'role' => 'warga',
        ]);

        $potensi = Potensi::create([
            'user_id' => $warga->id,
            'category_id' => $cat->id,
            'name' => 'Bengkel Las Maju',
            'owner_name' => $warga->name,
            'whatsapp' => $warga->phone,
            'address' => 'Jl. Raya Pelemwatu No. 88',
            'description' => 'Menerima jasa pengelasan pagar, kanopi, dan teralis.',
            'status' => 'pending',
        ]);

        // Admin reject dengan alasan
        Sanctum::actingAs($admin);
        $this->postJson("/api/potensi/{$potensi->id}/review", [
            'status' => 'rejected',
            'reason' => 'Foto lokasi bengkel belum dilampirkan.',
        ])->assertOk();

        // Warga cek potensi miliknya
        Sanctum::actingAs($warga);
        $wargaPotensi = $this->getJson('/api/warga/potensi')->assertOk()->json('data');

        $item = collect($wargaPotensi)->firstWhere('id', $potensi->id);
        $this->assertNotNull($item);
        $this->assertSame('rejected', $item['status']);
        $this->assertSame('Foto lokasi bengkel belum dilampirkan.', $item['rejection_reason']);
    }

    /**
     * Skenario 4:
     * Admin ubah profil desa -> GET / dan GET /daftar-potensi menampilkan nilai baru.
     */
    public function test_skenario_4_admin_ubah_profil_desa_terpantau_di_view_publik(): void
    {
        $admin = $this->makeAdmin();
        Sanctum::actingAs($admin);

        $payload = [
            'desa_alamat' => 'Jl. Merdeka Baru No. 99, Pelemwatu',
            'desa_kode_pos' => '61254',
            'desa_telepon' => '031-8989898',
            'desa_email' => 'halo@pelemwatu.desa.id',
            'desa_kepala' => 'Bambang Sujarwo',
            'desa_jam_operasional' => 'Senin - Jumat 07:30 - 15:30 WIB',
            'desa_whatsapp' => '082299887766',
            'desa_instagram' => 'desa.pelemwatu.update',
            'desa_jumlah_kk' => 1450,
        ];

        $this->putJson('/api/admin/profil-desa', $payload)->assertOk();

        // Verifikasi di homepage GET /
        $home = $this->get('/')->assertOk();
        $home->assertSee('Jl. Merdeka Baru No. 99, Pelemwatu');
        $home->assertSee('halo@pelemwatu.desa.id');
        $home->assertSee('1450');
        $home->assertSee('wa.me/6282299887766');

        // Verifikasi di GET /daftar-potensi
        $daftar = $this->get('/daftar-potensi')->assertOk();
        $daftar->assertSee('Jl. Merdeka Baru No. 99, Pelemwatu');
        $daftar->assertSee('wa.me/6282299887766');
    }

    /**
     * Skenario 5:
     * Admin tambah galeri -> tampil di GET / dan GET /api/galeri;
     * admin hapus galeri -> hilang dari keduanya dan file fisik terhapus.
     */
    public function test_skenario_5_admin_crud_galeri_dan_file_fisik_terhapus(): void
    {
        $admin = $this->makeAdmin();
        Sanctum::actingAs($admin);

        $file = UploadedFile::fake()->image('kegiatan-sync.jpg', 600, 400);

        // Tambah galeri
        $postRes = $this->postJson('/api/galeri', [
            'image' => $file,
            'caption' => 'Pawai Budaya Desa 2026',
            'description' => 'Dokumentasi kegiatan pawai budaya tahunan warga.',
            'sort_order' => 10,
        ])->assertCreated();

        $galeriId = $postRes->json('data.id');
        $imagePath = $postRes->json('data.image_path');
        $this->trackFile($imagePath);

        $diskPath = public_path(ltrim($imagePath, '/\\'));
        $this->assertFileExists($diskPath);

        // Tampil di GET /api/galeri
        $apiList = $this->getJson('/api/galeri')->assertOk()->json('data');
        $this->assertTrue(collect($apiList)->contains('id', $galeriId));

        // Tampil di GET /
        $this->get('/')->assertOk()->assertSee('Pawai Budaya Desa 2026');

        // Admin hapus galeri
        $this->deleteJson("/api/galeri/{$galeriId}")->assertOk();

        // Hilang dari DB
        $this->assertDatabaseMissing('galeri', ['id' => $galeriId]);

        // File fisik terhapus
        $this->assertFileDoesNotExist($diskPath);

        // Hilang dari GET /api/galeri
        $apiListAfter = $this->getJson('/api/galeri')->assertOk()->json('data');
        $this->assertFalse(collect($apiListAfter)->contains('id', $galeriId));
    }

    /**
     * Skenario 6:
     * Admin ganti logo -> GET /api/settings/logo mengembalikan path baru;
     * warga tidak bisa mengganti logo (403).
     */
    public function test_skenario_6_admin_ganti_logo_dan_warga_dilarang(): void
    {
        $admin = $this->makeAdmin();
        $warga = User::create([
            'name' => 'Warga Biasa',
            'phone' => '081234567899',
            'password' => Hash::make('password123'),
            'role' => 'warga',
        ]);

        // Warga coba ganti logo -> 403 Forbidden
        Sanctum::actingAs($warga);
        $wargaFile = UploadedFile::fake()->image('fake-logo.png', 100, 100);
        $this->postJson('/api/settings/logo', [
            'logo' => $wargaFile,
        ])->assertStatus(403);

        // Admin ganti logo -> sukses
        Sanctum::actingAs($admin);
        $logoFile = UploadedFile::fake()->image('logo-resmi.png', 200, 200);
        $adminRes = $this->postJson('/api/settings/logo', [
            'logo' => $logoFile,
        ])->assertOk();

        $newLogoPath = $adminRes->json('data.path');
        $this->trackFile($newLogoPath);

        // GET /api/settings/logo mengembalikan path baru (bisa berekstensi query ?v=)
        $publicLogo = $this->getJson('/api/settings/logo')->assertOk();
        $this->assertStringStartsWith($newLogoPath, $publicLogo->json('data.path'));
    }

    /**
     * Skenario 7:
     * Permintaan hapus dari warga muncul di GET /api/admin/deletion-requests;
     * approve -> potensi hilang dari publik.
     */
    public function test_skenario_7_warga_minta_hapus_admin_approve_potensi_hilang_dari_publik(): void
    {
        $admin = $this->makeAdmin();
        $cat = $this->makeCategory('Usaha & Produk');

        $warga = User::create([
            'name' => 'Siti Aminah',
            'phone' => '081234567888',
            'password' => Hash::make('password123'),
            'role' => 'warga',
        ]);

        // Potensi aktif milik warga
        $potensi = Potensi::create([
            'user_id' => $warga->id,
            'category_id' => $cat->id,
            'name' => 'Toko Kelontong Berkah',
            'owner_name' => $warga->name,
            'whatsapp' => $warga->phone,
            'address' => 'Jl. Melati RT 01 RW 02',
            'description' => 'Menyediakan sembako lengkap dan kebutuhan harian warga.',
            'status' => 'approved',
        ]);

        // Awalnya ada di publik
        $this->getJson('/api/potensi')->assertOk();
        $this->assertTrue(collect($this->getJson('/api/potensi')->json('data'))->contains('id', $potensi->id));

        // Warga mengajukan permohonan hapus
        Sanctum::actingAs($warga);
        $this->postJson("/api/warga/potensi/{$potensi->id}/request-deletion")->assertOk();

        // Admin melihat di daftar deletion-requests
        Sanctum::actingAs($admin);
        $reqs = $this->getJson('/api/admin/deletion-requests')->assertOk()->json('data');
        $this->assertTrue(collect($reqs)->contains('id', $potensi->id));

        // Admin approve penghapusan
        $this->postJson("/api/admin/deletion-requests/{$potensi->id}/approve")->assertOk();

        // Potensi terhapus dari publik dan database
        $afterList = $this->getJson('/api/potensi')->assertOk()->json('data');
        $this->assertFalse(collect($afterList)->contains('id', $potensi->id));
        $this->assertDatabaseMissing('potensi', ['id' => $potensi->id]);
    }
}
