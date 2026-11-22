<?php

namespace Tests\Feature;

use App\Http\Controllers\API\ProfilDesaController;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfilDesaTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
    }

    private function makeWarga(): User
    {
        return User::create([
            'name' => 'Warga Test',
            'email' => 'warga@test.id',
            'password' => Hash::make('password123'),
            'role' => 'warga',
        ]);
    }

    public function test_profil_desa_get_unauthenticated_returns_401(): void
    {
        $res = $this->getJson('/api/admin/profil-desa');
        $res->assertStatus(401);
    }

    public function test_profil_desa_get_warga_returns_403(): void
    {
        Sanctum::actingAs($this->makeWarga());

        $res = $this->getJson('/api/admin/profil-desa');
        $res->assertStatus(403)
            ->assertJson(['status' => 'error', 'message' => 'Akses ditolak.']);
    }

    public function test_profil_desa_get_admin_returns_defaults_when_empty(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        $res = $this->getJson('/api/admin/profil-desa');
        $res->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'desa_alamat' => ProfilDesaController::DEFAULTS['desa_alamat'],
                    'desa_email' => ProfilDesaController::DEFAULTS['desa_email'],
                    'desa_jam_operasional' => ProfilDesaController::DEFAULTS['desa_jam_operasional'],
                    'desa_whatsapp' => ProfilDesaController::DEFAULTS['desa_whatsapp'],
                    'desa_instagram' => ProfilDesaController::DEFAULTS['desa_instagram'],
                ],
            ]);
    }

    public function test_profil_desa_put_unauthenticated_returns_401(): void
    {
        $res = $this->putJson('/api/admin/profil-desa', [
            'desa_alamat' => 'Alamat Baru',
        ]);
        $res->assertStatus(401);
    }

    public function test_profil_desa_put_warga_returns_403(): void
    {
        Sanctum::actingAs($this->makeWarga());

        $res = $this->putJson('/api/admin/profil-desa', [
            'desa_alamat' => 'Alamat Baru',
        ]);
        $res->assertStatus(403)
            ->assertJson(['status' => 'error', 'message' => 'Akses ditolak.']);
    }

    public function test_profil_desa_put_admin_updates_and_normalizes_whatsapp_and_instagram(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        $res = $this->putJson('/api/admin/profil-desa', [
            'desa_alamat' => 'Jl. Veteran Baru No. 10, Pelemwatu',
            'desa_kode_pos' => '61174',
            'desa_telepon' => '031-7991234',
            'desa_email' => 'kantor@pelemwatu.desa.id',
            'desa_kepala' => 'Bapak Kepala Desa',
            'desa_jam_operasional' => 'Senin – Sabtu, 08.00 – 15.00 WIB',
            'desa_whatsapp' => '0812-3456-7890',
            'desa_instagram' => '@pelemwatu_asri',
            'desa_jumlah_kk' => 750,
        ]);

        $res->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Profil desa berhasil diperbarui.',
                'data' => [
                    'desa_alamat' => 'Jl. Veteran Baru No. 10, Pelemwatu',
                    'desa_kode_pos' => '61174',
                    'desa_telepon' => '031-7991234',
                    'desa_email' => 'kantor@pelemwatu.desa.id',
                    'desa_kepala' => 'Bapak Kepala Desa',
                    'desa_jam_operasional' => 'Senin – Sabtu, 08.00 – 15.00 WIB',
                    'desa_whatsapp' => '6281234567890',
                    'desa_instagram' => 'pelemwatu_asri',
                    'desa_jumlah_kk' => 750,
                ],
            ]);

        $this->assertEquals('6281234567890', Setting::get('desa_whatsapp'));
        $this->assertEquals('pelemwatu_asri', Setting::get('desa_instagram'));
        $this->assertEquals('750', (string) Setting::get('desa_jumlah_kk'));
    }

    public function test_homepage_and_daftar_potensi_display_updated_settings_and_link(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        // Update profil desa
        $this->putJson('/api/admin/profil-desa', [
            'desa_alamat' => 'Jl. Veteran Baru No. 10, Pelemwatu',
            'desa_email' => 'kantor@pelemwatu.desa.id',
            'desa_jam_operasional' => 'Senin – Sabtu, 08.00 – 15.00 WIB',
            'desa_whatsapp' => '0812-3456-7890',
            'desa_instagram' => 'https://instagram.com/pelemwatu_keren',
            'desa_jumlah_kk' => 820,
            'desa_telepon' => '031-7998888',
        ])->assertStatus(200);

        // Pastikan cache segar
        Cache::forget('profil_desa_settings');

        // Cek Homepage
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);
        $homeRes->assertSee('Jl. Veteran Baru No. 10, Pelemwatu');
        $homeRes->assertSee('kantor@pelemwatu.desa.id');
        $homeRes->assertSee('Senin – Sabtu, 08.00 – 15.00 WIB');
        $homeRes->assertSee('wa.me/6281234567890');
        $homeRes->assertSee('0812-3456-7890');
        $homeRes->assertSee('instagram.com/pelemwatu_keren');
        $homeRes->assertSee('@pelemwatu_keren');
        $homeRes->assertSee('820');
        $homeRes->assertSee('031-7998888');

        // Cek Halaman Daftar Potensi
        $daftarRes = $this->get('/daftar-potensi');
        $daftarRes->assertStatus(200);
        $daftarRes->assertSee('Jl. Veteran Baru No. 10, Pelemwatu');
        $daftarRes->assertSee('wa.me/6281234567890');
        $daftarRes->assertSee('Senin – Sabtu, 08.00 – 15.00 WIB');
    }

    public function test_validation_errors_for_invalid_data(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        // Email tidak valid
        $resEmail = $this->putJson('/api/admin/profil-desa', ['desa_email' => 'bukan-email']);
        $resEmail->assertStatus(422)
            ->assertJsonValidationErrors(['desa_email']);

        // Kode pos bukan 5 digit
        $resPos = $this->putJson('/api/admin/profil-desa', ['desa_kode_pos' => '123']);
        $resPos->assertStatus(422)
            ->assertJsonValidationErrors(['desa_kode_pos']);

        // WhatsApp mengandung huruf
        $resWa = $this->putJson('/api/admin/profil-desa', ['desa_whatsapp' => '08123abc456']);
        $resWa->assertStatus(422)
            ->assertJsonValidationErrors(['desa_whatsapp']);

        // Instagram karakter tidak valid
        $resIg = $this->putJson('/api/admin/profil-desa', ['desa_instagram' => 'username dengan spasi!']);
        $resIg->assertStatus(422);

        // Jumlah KK negatif
        $resKk = $this->putJson('/api/admin/profil-desa', ['desa_jumlah_kk' => -5]);
        $resKk->assertStatus(422)
            ->assertJsonValidationErrors(['desa_jumlah_kk']);
    }

    public function test_html_and_xss_tags_are_rejected(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        $res = $this->putJson('/api/admin/profil-desa', [
            'desa_alamat' => '<script>alert("xss")</script>Jl. Pelemwatu',
        ]);

        $res->assertStatus(422)
            ->assertJson(['status' => 'error', 'message' => 'Input tidak boleh mengandung tag HTML.']);
    }

    public function test_empty_values_revert_to_defaults(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        // 1. Set data kustom
        $this->putJson('/api/admin/profil-desa', [
            'desa_alamat' => 'Alamat Sementara',
            'desa_whatsapp' => '08999999999',
        ])->assertStatus(200);

        $this->assertEquals('Alamat Sementara', Setting::get('desa_alamat'));

        // 2. Kosongkan kembali
        $res = $this->putJson('/api/admin/profil-desa', [
            'desa_alamat' => '',
            'desa_whatsapp' => '',
        ]);

        $res->assertStatus(200);
        $this->assertNull(Setting::get('desa_alamat'));
        $this->assertEquals(ProfilDesaController::DEFAULTS['desa_alamat'], $res->json('data.desa_alamat'));
        $this->assertEquals(ProfilDesaController::DEFAULTS['desa_whatsapp'], $res->json('data.desa_whatsapp'));

        // 3. Homepage menampilkan default
        Cache::forget('profil_desa_settings');
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);
        $homeRes->assertSee(ProfilDesaController::DEFAULTS['desa_alamat']);
    }

    public function test_keterhubungan_profil_desa_admin_ke_homepage_lengkap(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        $this->putJson('/api/admin/profil-desa', [
            'desa_kode_pos' => '61174',
            'desa_kepala' => 'Kepala Uji Coba',
            'desa_telepon' => '031-1234567',
            'desa_jumlah_kk' => 1234,
            'desa_alamat' => 'Jl. Uji Coba No. 1',
            'desa_email' => 'ujicoba@desa.id',
            'desa_whatsapp' => '081234567890',
            'desa_instagram' => 'ujicoba_desa',
        ])->assertOk();

        $res = $this->get('/')->assertOk();
        $html = $res->getContent();

        // 1. Kepala desa muncul di Identitas Desa sebelum baris Status
        $this->assertStringContainsString('Kepala Uji Coba', $html);
        $posKepala = strpos($html, 'Kepala Uji Coba');
        $posStatus = strpos($html, 'Desa Aktif');
        $this->assertTrue($posKepala !== false && $posStatus !== false && $posKepala < $posStatus, 'Kepala desa harus muncul sebelum baris Status');

        // 2. Kode pos muncul di kartu Lokasi dan TIDAK muncul di bagian Kontak/footer
        $posLokasi = strpos($html, 'id="tab-lokasi"');
        $posKontak = strpos($html, 'id="kontak-section"');
        $this->assertTrue($posLokasi !== false && $posKontak !== false);

        $posKodePos = strpos($html, '61174');
        $this->assertTrue($posKodePos !== false && $posKodePos > $posLokasi && $posKodePos < $posKontak, 'Kode pos harus muncul di kartu Lokasi');

        $kontakAndFooter = substr($html, $posKontak);
        $this->assertFalse(strpos($kontakAndFooter, '61174'), 'Kode pos TIDAK boleh muncul di bagian Kontak atau footer');

        // 3. Telepon muncul di bagian Kontak dengan href tel:
        $res->assertSee('href="tel:031-1234567"', false);
        $res->assertSee('Telp. Kantor:');

        // 4. KK muncul di #statKK dalam format angka Indonesia (1.234)
        $res->assertSee('id="statKK">1.234<', false);
    }

    public function test_field_profil_desa_dikosongkan_baris_hilang_dan_kk_menampilkan_strip(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        // Kosongkan field opsional
        $this->putJson('/api/admin/profil-desa', [
            'desa_kode_pos' => '',
            'desa_kepala' => '',
            'desa_telepon' => '',
            'desa_jumlah_kk' => '',
        ])->assertOk();

        $res = $this->get('/')->assertOk();
        $html = $res->getContent();

        // Baris Kepala Desa tidak boleh tampil di tabel Identitas Desa
        $this->assertStringNotContainsString('<td>Kepala Desa</td>', $html);

        // Baris Kode Pos tidak boleh tampil di kartu Lokasi
        $this->assertStringNotContainsString('<h6 class="lokasi-label">Kode Pos</h6>', $html);

        // Baris Telepon Kantor tidak boleh tampil di kartu WhatsApp
        $this->assertStringNotContainsString('Telp. Kantor:', $html);

        // KK menampilkan tanda strip
        $res->assertSee('id="statKK">—<', false);
    }

    public function test_cache_otomatis_dibersihkan_saat_put_kedua_langsung_tampil_nilai_baru(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        // PUT pertama
        $this->putJson('/api/admin/profil-desa', [
            'desa_kepala' => 'Kepala Versi Pertama',
        ])->assertOk();

        $res1 = $this->get('/')->assertOk();
        $res1->assertSee('Kepala Versi Pertama');

        // PUT kedua (tanpa manual Cache::forget)
        $this->putJson('/api/admin/profil-desa', [
            'desa_kepala' => 'Kepala Versi Kedua',
        ])->assertOk();

        $res2 = $this->get('/')->assertOk();
        $res2->assertSee('Kepala Versi Kedua');
        $res2->assertDontSee('Kepala Versi Pertama');
    }

    public function test_karakter_khusus_dan_xss_ter_escape_di_homepage(): void
    {
        // Masukkan data dengan tag HTML langsung di DB untuk simulasi escape Blade {{ }}
        Setting::set('desa_kepala', '<script>alert("xss")</script>');
        Cache::forget('profil_desa_settings');

        $res = $this->get('/')->assertOk();

        // Tidak boleh muncul sebagai script aktif / raw HTML
        $res->assertDontSee('<script>alert("xss")</script>', false);
        // Harus ter-escape secara aman
        $res->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false);
    }
}
