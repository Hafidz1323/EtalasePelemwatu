<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Potensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenyempurnaanP2Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Category::firstOrCreate(['id' => 1], [
            'name' => 'Usaha & Produk',
            'description' => 'Usaha kuliner dan produk olahan warga',
        ]);
    }

    /**
     * 1. Test Pagination Katalog API
     */
    public function test_katalog_mendukung_pagination(): void
    {
        // Buat 15 potensi yang disetujui
        for ($i = 1; $i <= 15; $i++) {
            Potensi::create([
                'category_id' => 1,
                'name' => "Usaha Warga {$i}",
                'owner_name' => "Pemilik {$i}",
                'status' => 'approved',
            ]);
        }

        // Request page 1 dengan per_page 6
        $response = $this->getJson('/api/potensi?page=1&per_page=6');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'data',
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                    'has_more',
                ],
            ]);

        $this->assertCount(6, $response->json('data'));
        $this->assertEquals(1, $response->json('pagination.current_page'));
        $this->assertEquals(3, $response->json('pagination.last_page'));
        $this->assertTrue($response->json('pagination.has_more'));

        // Request page 3 (halaman terakhir: sisa 3)
        $resPage3 = $this->getJson('/api/potensi?page=3&per_page=6');
        $resPage3->assertStatus(200);
        $this->assertCount(3, $resPage3->json('data'));
        $this->assertFalse($resPage3->json('pagination.has_more'));
    }

    /**
     * 2. Test Halaman Error 404 Kustom Ramah Pengguna
     */
    public function test_halaman_404_kustom_ditampilkan_saat_rute_tidak_ditemukan(): void
    {
        $response = $this->get('/halaman-yang-pasti-tidak-ada-12345');

        $response->assertStatus(404);
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('Ke Halaman Utama');
    }

    /**
     * 3. Test Metadata SEO dan Open Graph di Halaman Utama
     */
    public function test_halaman_utama_memiliki_metadata_seo(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('EtalasePelemwatu');
        $response->assertSee('og:title', false);
        $response->assertSee('og:description', false);
        $response->assertSee('twitter:card', false);
    }
}
