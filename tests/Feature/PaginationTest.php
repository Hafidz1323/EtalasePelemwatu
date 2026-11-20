<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Potensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function category(): Category
    {
        return Category::first() ?? Category::create(['name' => 'Usaha & Produk']);
    }

    /** Buat sejumlah potensi berstatus approved */
    private function buatPotensi(int $n, array $attrs = []): void
    {
        $cat = $this->category();
        for ($i = 1; $i <= $n; $i++) {
            Potensi::create(array_merge([
                'category_id' => $cat->id,
                'name'        => "Potensi $i",
                'owner_name'  => 'Budi Santoso',
                'whatsapp'    => '081234567890',
                'address'     => 'Jl. Raya Pelemwatu No. 1',
                'description' => 'Deskripsi potensi yang cukup panjang dan jelas.',
                'status'      => 'approved',
            ], $attrs));
        }
    }

    // -----------------------------------------------------------------------
    // 1. Pagination dasar
    // -----------------------------------------------------------------------

    /** Respons harus mengandung meta {current_page, last_page, total, per_page}. */
    public function test_respons_mengandung_meta_pagination(): void
    {
        $this->buatPotensi(5);

        $res = $this->getJson('/api/potensi')->assertOk();

        $res->assertJsonStructure([
            'status',
            'data',
            'meta' => ['current_page', 'last_page', 'total', 'per_page'],
        ]);
    }

    /** per_page default 12: 15 data → 2 halaman, halaman 1 mengembalikan 12 baris. */
    public function test_per_page_default_12_dan_total_benar(): void
    {
        $this->buatPotensi(15);

        $res = $this->getJson('/api/potensi')->assertOk();

        $this->assertCount(12, $res->json('data'));
        $this->assertSame(12, $res->json('meta.per_page'));
        $this->assertSame(1,  $res->json('meta.current_page'));
        $this->assertSame(2,  $res->json('meta.last_page'));
        $this->assertSame(15, $res->json('meta.total'));
    }

    /** Halaman 2 mengembalikan sisa data. */
    public function test_halaman_2_mengembalikan_sisa_data(): void
    {
        $this->buatPotensi(15);

        $res = $this->getJson('/api/potensi?page=2')->assertOk();

        $this->assertCount(3, $res->json('data'));
        $this->assertSame(2, $res->json('meta.current_page'));
        $this->assertSame(2, $res->json('meta.last_page'));
    }

    /** per_page kustom: ?per_page=5 menghasilkan 5 baris per halaman. */
    public function test_per_page_kustom_dihormati(): void
    {
        $this->buatPotensi(10);

        $res = $this->getJson('/api/potensi?per_page=5')->assertOk();

        $this->assertCount(5, $res->json('data'));
        $this->assertSame(5,  $res->json('meta.per_page'));
        $this->assertSame(2,  $res->json('meta.last_page'));
        $this->assertSame(10, $res->json('meta.total'));
    }

    // -----------------------------------------------------------------------
    // 2. Batas per_page maks 50
    // -----------------------------------------------------------------------

    /** per_page > 50 dikembalikan sebagai 50. */
    public function test_per_page_melebihi_50_dibatasi_jadi_50(): void
    {
        $this->buatPotensi(5);

        $res = $this->getJson('/api/potensi?per_page=100')->assertOk();

        $this->assertSame(50, $res->json('meta.per_page'));
    }

    /** per_page=0 atau nilai tidak valid jatuh ke default 12. */
    public function test_per_page_tidak_valid_jatuh_ke_default(): void
    {
        $this->buatPotensi(5);

        $res = $this->getJson('/api/potensi?per_page=0')->assertOk();
        $this->assertSame(12, $res->json('meta.per_page'));

        $res = $this->getJson('/api/potensi?per_page=-5')->assertOk();
        $this->assertSame(12, $res->json('meta.per_page'));
    }

    // -----------------------------------------------------------------------
    // 3. Search dengan karakter LIKE wildcard
    // -----------------------------------------------------------------------

    /** Search dengan '%' tidak bocor jadi wildcard — hanya cari literal '%'. */
    public function test_search_karakter_persen_tidak_jadi_wildcard(): void
    {
        $cat = $this->category();

        // Potensi yang namanya mengandung literal '%'
        Potensi::create([
            'category_id' => $cat->id,
            'name'        => 'Diskon 50% per bulan',
            'owner_name'  => 'Budi Santoso',
            'whatsapp'    => '081234567890',
            'address'     => 'Jl. Raya Pelemwatu No. 1',
            'description' => 'Deskripsi dengan persen di dalamnya.',
            'status'      => 'approved',
        ]);

        // Potensi lain yang tidak mengandung '%'
        Potensi::create([
            'category_id' => $cat->id,
            'name'        => 'Usaha Biasa Tanpa Persen',
            'owner_name'  => 'Siti Rahayu',
            'whatsapp'    => '081234567891',
            'address'     => 'Jl. Raya Pelemwatu No. 2',
            'description' => 'Deskripsi normal tanpa karakter khusus.',
            'status'      => 'approved',
        ]);

        // Search dengan '%' — hanya harus menemukan yang namanya mengandung '%'
        $res = $this->getJson('/api/potensi?per_page=50&search=' . urlencode('%'))->assertOk();
        $names = collect($res->json('data'))->pluck('name')->all();

        $this->assertContains('Diskon 50% per bulan', $names,
            "Potensi yang mengandung '%' harus ditemukan");
        $this->assertNotContains('Usaha Biasa Tanpa Persen', $names,
            "Potensi yang tidak mengandung '%' tidak boleh ikut tampil");
    }

    /** Search dengan '_' tidak bocor jadi wildcard single-char. */
    public function test_search_karakter_underscore_tidak_jadi_wildcard(): void
    {
        $cat = $this->category();

        Potensi::create([
            'category_id' => $cat->id,
            'name'        => 'Usaha_Spesial',
            'owner_name'  => 'Budi Santoso',
            'whatsapp'    => '081234567890',
            'address'     => 'Jl. Raya Pelemwatu No. 1',
            'description' => 'Deskripsi dengan underscore di dalam nama.',
            'status'      => 'approved',
        ]);

        Potensi::create([
            'category_id' => $cat->id,
            'name'        => 'Usaha Normal',
            'owner_name'  => 'Siti Rahayu',
            'whatsapp'    => '081234567891',
            'address'     => 'Jl. Raya Pelemwatu No. 2',
            'description' => 'Deskripsi potensi usaha yang cukup panjang.',
            'status'      => 'approved',
        ]);

        // Search dengan literal '_' — hanya menemukan yang namanya mengandung '_'
        $res = $this->getJson('/api/potensi?per_page=50&search=' . urlencode('_'))->assertOk();
        $names = collect($res->json('data'))->pluck('name')->all();

        $this->assertContains('Usaha_Spesial', $names);
        $this->assertNotContains('Usaha Normal', $names);
    }

    // -----------------------------------------------------------------------
    // 4. Admin tetap bisa melihat semua data
    // -----------------------------------------------------------------------

    /** Admin dapat mengambil semua data dengan per_page=50 dan loop halaman. */
    public function test_admin_dapat_memuat_semua_halaman(): void
    {
        $this->buatPotensi(55, ['status' => 'pending']); // lebih dari 1 halaman

        Sanctum::actingAs($this->admin());

        // Halaman 1
        $res1 = $this->getJson('/api/potensi?per_page=50&page=1')->assertOk();
        $this->assertSame(50, count($res1->json('data')));
        $this->assertSame(2,  $res1->json('meta.last_page'));

        // Halaman 2
        $res2 = $this->getJson('/api/potensi?per_page=50&page=2')->assertOk();
        $this->assertSame(5, count($res2->json('data')));

        // Total gabungan = 55
        $total = count($res1->json('data')) + count($res2->json('data'));
        $this->assertSame(55, $total);
    }

    // -----------------------------------------------------------------------
    // 5. Struktur respons tidak berubah bagi kode yang sudah ada
    // -----------------------------------------------------------------------

    /** json.data tetap berupa array (kompatibel dengan kode frontend lama). */
    public function test_json_data_adalah_array(): void
    {
        $this->buatPotensi(3);

        $res = $this->getJson('/api/potensi')->assertOk();

        $this->assertIsArray($res->json('data'));
        $this->assertSame('success', $res->json('status'));
    }
}
