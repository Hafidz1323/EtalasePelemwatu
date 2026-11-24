<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Potensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PublicStatsTest extends TestCase
{
    use RefreshDatabase;

    private function createPotensiWithCat(string $catName, string $status): Potensi
    {
        $cat = Category::firstOrCreate(['name' => $catName], [
            'description' => 'Deskripsi '.$catName,
        ]);

        return Potensi::create([
            'category_id' => $cat->id,
            'name' => 'Potensi '.$catName.' '.uniqid(),
            'owner_name' => 'Pemilik Test',
            'whatsapp' => '081234567890',
            'address' => 'Jl. Raya Pelemwatu No. 1',
            'description' => 'Deskripsi potensi yang valid untuk pengujian',
            'status' => $status,
        ]);
    }

    public function test_get_stats_public_bisa_diakses_tanpa_autentikasi(): void
    {
        $res = $this->getJson('/api/stats/public');

        $res->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertNotNull($res->json('umkm') ?? $res->json('data.umkm'));
    }

    public function test_get_stats_public_hanya_menghitung_status_publik(): void
    {
        // Bersihkan potensi demo agar hitungan presisi
        Potensi::query()->delete();

        // 1. Approved Usaha (dihitung)
        $this->createPotensiWithCat('Usaha & Produk', 'approved');

        // 2. Pending Usaha (TIDAK dihitung)
        $this->createPotensiWithCat('Usaha & Produk', 'pending');

        // 3. Rejected Usaha (TIDAK dihitung)
        $this->createPotensiWithCat('Usaha & Produk', 'rejected');

        $res = $this->getJson('/api/stats/public');
        $res->assertStatus(200);

        $count = $res->json('umkm') ?? $res->json('data.umkm');
        $this->assertEquals(1, $count);
    }

    public function test_get_stats_public_hanya_menghitung_kategori_usaha_jasa_hunian(): void
    {
        Potensi::query()->delete();

        // Approved Usaha, Jasa, Hunian (semua dihitung)
        $this->createPotensiWithCat('Usaha & Produk', 'approved');
        $this->createPotensiWithCat('Jasa', 'approved');
        $this->createPotensiWithCat('Hunian', 'approved');

        // Approved Barang Warga (TIDAK dihitung)
        $this->createPotensiWithCat('Barang Warga', 'approved');

        $res = $this->getJson('/api/stats/public');
        $res->assertStatus(200);

        $count = $res->json('umkm') ?? $res->json('data.umkm');
        $this->assertEquals(3, $count);
    }

    public function test_get_stats_public_tidak_mengekspos_data_lain(): void
    {
        $res = $this->getJson('/api/stats/public');
        $res->assertStatus(200);

        // Jangan mengekspos data internal/admin
        $res->assertJsonMissing([
            'totalPotensi',
            'pendingReviews',
            'perCategory',
        ]);
    }

    public function test_api_stats_admin_tetap_terproteksi(): void
    {
        // Tanpa token ditolak 401
        $this->getJson('/api/stats')->assertStatus(401);

        // Token warga ditolak 403
        $warga = User::create([
            'name' => 'Warga Test',
            'phone' => '081234567891',
            'password' => Hash::make('password123'),
            'role' => 'warga',
        ]);
        Sanctum::actingAs($warga);
        $this->getJson('/api/stats')->assertStatus(403);
    }
}
