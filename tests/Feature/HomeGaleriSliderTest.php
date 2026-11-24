<?php

namespace Tests\Feature;

use App\Models\Galeri;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeGaleriSliderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Pastikan tabel dibersihkan untuk pengujian deterministik
        Galeri::query()->delete();
    }

    public function test_galeri_tab_is_first_button_and_active_in_profil_nav(): void
    {
        Galeri::create([
            'image_path' => 'assets/img/hero.jpg',
            'caption' => 'Kegiatan Kerja Bakti',
            'description' => 'Warga bergotong royong membersihkan saluran air.',
            'sort_order' => 1,
        ]);

        $res = $this->get('/')->assertOk();
        $html = $res->getContent();

        // 1. Tombol tab Galeri adalah tombol PERTAMA di dalam .profil-nav dan ber-class active
        $this->assertStringContainsString('class="profil-nav', $html);
        $posNav = strpos($html, 'class="profil-nav');
        $posGaleriBtn = strpos($html, 'data-tab="tab-galeri"');
        $posTentangBtn = strpos($html, 'data-tab="tab-tentang"');
        $posLokasiBtn = strpos($html, 'data-tab="tab-lokasi"');

        $this->assertTrue($posGaleriBtn !== false, 'Tombol tab galeri harus ada');
        $this->assertTrue($posTentangBtn !== false, 'Tombol tab tentang harus ada');
        $this->assertTrue($posLokasiBtn !== false, 'Tombol tab lokasi harus ada');

        $this->assertTrue(
            $posNav < $posGaleriBtn && $posGaleriBtn < $posTentangBtn && $posTentangBtn < $posLokasiBtn,
            'Tombol Galeri harus berada di posisi pertama sebelum Tentang dan Lokasi'
        );

        // Ambil potongan tag tombol galeri untuk memastikan class active
        $galeriBtnTag = substr($html, $posGaleriBtn - 50, 100);
        $this->assertStringContainsString('profil-nav-btn active', $galeriBtnTag);

        // 2. Pane #tab-galeri adalah pane PERTAMA dan ber-class active
        $posGaleriPane = strpos($html, 'id="tab-galeri"');
        $posTentangPane = strpos($html, 'id="tab-tentang"');
        $posLokasiPane = strpos($html, 'id="tab-lokasi"');

        $this->assertTrue($posGaleriPane !== false, 'Pane #tab-galeri harus ada');
        $this->assertTrue($posTentangPane !== false, 'Pane #tab-tentang harus ada');
        $this->assertTrue($posLokasiPane !== false, 'Pane #tab-lokasi harus ada');

        $this->assertTrue(
            $posGaleriPane < $posTentangPane && $posTentangPane < $posLokasiPane,
            'Pane #tab-galeri harus berada di urutan pertama sebelum #tab-tentang'
        );

        $galeriPaneTag = substr($html, $posGaleriPane - 50, 100);
        $this->assertStringContainsString('profil-tab-pane active', $galeriPaneTag);

        // Pastikan #tab-tentang tidak memiliki class active saat load pertama
        $tentangPaneTag = substr($html, $posTentangPane - 50, 100);
        $this->assertStringNotContainsString('profil-tab-pane active', $tentangPaneTag);
    }

    public function test_semua_caption_galeri_dari_database_muncul_di_slider(): void
    {
        Galeri::create([
            'image_path' => 'assets/img/kegiatan-1.jpg',
            'caption' => 'Festival Panen Raya 2026',
            'description' => 'Perayaan hasil bumi desa oleh kelompok tani.',
            'sort_order' => 1,
        ]);

        Galeri::create([
            'image_path' => 'assets/img/kegiatan-2.jpg',
            'caption' => 'Pemeriksaan Posyandu Balita',
            'description' => 'Layanan kesehatan berkala untuk ibu dan balita.',
            'sort_order' => 2,
        ]);

        Galeri::create([
            'image_path' => 'assets/img/kegiatan-3.jpg',
            'caption' => 'Pelatihan UMKM Keripik Pisang',
            'description' => 'Pendampingan pemasaran digital bagi pelaku usaha.',
            'sort_order' => 3,
        ]);

        $res = $this->get('/')->assertOk();

        // Asersi caption dan deskripsi muncul di markup
        $res->assertSee('Festival Panen Raya 2026');
        $res->assertSee('Perayaan hasil bumi desa oleh kelompok tani.');
        $res->assertSee('Pemeriksaan Posyandu Balita');
        $res->assertSee('Layanan kesehatan berkala untuk ibu dan balita.');
        $res->assertSee('Pelatihan UMKM Keripik Pisang');
        $res->assertSee('Pendampingan pemasaran digital bagi pelaku usaha.');

        // Asersi elemen slider dan kartu tersedia
        $res->assertSee('id="gsContainer"', false);
        $res->assertSee('class="gs-track"', false);
        $res->assertSee('class="gs-card is-active"', false);
    }

    public function test_caption_dan_deskripsi_berisi_script_ter_escape_dengan_aman(): void
    {
        Galeri::create([
            'image_path' => 'assets/img/kegiatan-xss.jpg',
            'caption' => '<script>alert("xss")</script>Pentas Seni',
            'description' => '<img src=x onerror=alert(1)>Deskripsi Pentas',
            'sort_order' => 1,
        ]);

        $res = $this->get('/')->assertOk();

        // Tidak boleh muncul unescaped / raw script
        $res->assertDontSee('<script>alert("xss")</script>', false);
        $res->assertDontSee('<img src=x onerror=alert(1)>', false);

        // Harus ter-escape secara aman di HTML
        $res->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;Pentas Seni', false);
        $res->assertSee('&lt;img src=x onerror=alert(1)&gt;Deskripsi Pentas', false);
    }

    public function test_kosong_galeri_menampilkan_pesan_belum_ada_foto_kegiatan(): void
    {
        // Pastikan tabel kosong
        Galeri::query()->delete();

        $res = $this->get('/')->assertOk();

        // Pesan fallback harus muncul
        $res->assertSee('Belum ada foto kegiatan.');
        // Slider track tidak boleh dirender saat tidak ada data
        $res->assertDontSee('id="gsTrack"', false);
    }

    public function test_struktur_aksesibilitas_dan_kontrol_slider_tersedia(): void
    {
        Galeri::create([
            'image_path' => 'assets/img/kegiatan-1.jpg',
            'caption' => 'Musyawarah Perencanaan Desa',
            'description' => 'Penyusunan rencana kerja pemerintah desa.',
            'sort_order' => 1,
        ]);

        $res = $this->get('/')->assertOk();

        // Role carousel dan aria labels
        $res->assertSee('role="region"', false);
        $res->assertSee('aria-roledescription="carousel"', false);
        $res->assertSee('aria-live="polite"', false);
        $res->assertSee('id="gsBtnView"', false);
        $res->assertSee('id="gsPrev"', false);
        $res->assertSee('id="gsNext"', false);
    }
}
