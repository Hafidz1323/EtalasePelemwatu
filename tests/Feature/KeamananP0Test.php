<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Potensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * KeamananP0Test
 *
 * Memastikan semua item keamanan kritis (P0) sudah diimplementasikan
 * sebelum website dipublikasikan.
 */
class KeamananP0Test extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────
    // 1. Autentikasi & Otorisasi
    // ─────────────────────────────────────────

    /** Halaman admin tanpa token harus redirect ke /login */
    public function test_halaman_admin_tanpa_token_redirect_ke_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    /** Endpoint API tanpa token harus mengembalikan 401 */
    public function test_api_profil_tanpa_token_ditolak(): void
    {
        $response = $this->getJson('/api/profile');
        $response->assertStatus(401);
    }

    /** Warga biasa yang sudah login tidak bisa mengakses halaman /admin */
    public function test_warga_tidak_bisa_akses_halaman_admin(): void
    {
        $warga = User::factory()->create(['role' => 'warga']);
        $token = $warga->createToken('test')->plainTextToken;

        // Cookie ep-token dikirim oleh JS setelah login
        $response = $this->withCookie('ep-token', $token)->get('/admin');
        $response->assertRedirect('/');
    }

    /** Admin dengan token valid bisa mengakses halaman /admin */
    public function test_admin_dengan_token_valid_bisa_akses_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->withCookie('ep-token', $token)->get('/admin');
        // Admin bisa masuk: 200 OK (bukan redirect)
        $response->assertStatus(200);
    }

    // ─────────────────────────────────────────
    // 2. Kepemilikan Data Potensi
    // ─────────────────────────────────────────

    /** Warga tidak bisa mengedit potensi milik warga lain */
    public function test_warga_tidak_bisa_edit_potensi_milik_orang_lain(): void
    {
        $pemilik = User::factory()->create(['role' => 'warga']);
        $penyusup = User::factory()->create(['role' => 'warga']);

        $potensi = Potensi::factory()->create(['user_id' => $pemilik->id]);
        $token = $penyusup->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->putJson("/api/potensi/{$potensi->id}", [
            'nama_potensi' => 'Hacked',
            'deskripsi'    => 'Ini tidak boleh berhasil',
            'kategori'     => 'pertanian',
        ]);

        $response->assertStatus(403);
    }

    /** Warga tidak bisa menghapus potensi milik warga lain */
    public function test_warga_tidak_bisa_hapus_potensi_milik_orang_lain(): void
    {
        $pemilik = User::factory()->create(['role' => 'warga']);
        $penyusup = User::factory()->create(['role' => 'warga']);

        $potensi = Potensi::factory()->create(['user_id' => $pemilik->id]);
        $token = $penyusup->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->deleteJson("/api/potensi/{$potensi->id}");
        $response->assertStatus(403);
    }

    // ─────────────────────────────────────────
    // 3. Password Hashed (Tidak Disimpan Plaintext)
    // ─────────────────────────────────────────

    /** Registrasi menyimpan password dalam format hashed, bukan plaintext */
    public function test_registrasi_menyimpan_password_hashed(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Test Warga',
            'email'                 => 'testwarga@example.com',
            'password'              => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'testwarga@example.com')->first();
        $this->assertNotNull($user);
        // Password di database TIDAK boleh sama dengan plaintext
        $this->assertNotEquals('rahasia123', $user->password);
        // Password harus bisa diverifikasi dengan Hash::check
        $this->assertTrue(Hash::check('rahasia123', $user->password));
    }

    // ─────────────────────────────────────────
    // 4. Validasi Upload File
    // ─────────────────────────────────────────

    /** Upload file PHP (executable) harus ditolak */
    public function test_upload_file_php_ditolak(): void
    {
        Storage::fake('public');
        $warga = User::factory()->create(['role' => 'warga']);
        $token = $warga->createToken('test')->plainTextToken;

        $filePHP = UploadedFile::fake()->create('shell.php', 100, 'application/x-php');

        $response = $this->withToken($token)->postJson('/api/upload', [
            'foto' => $filePHP,
        ]);

        $response->assertStatus(422);
    }

    /** Upload file yang lebih besar dari 5MB harus ditolak */
    public function test_upload_file_terlalu_besar_ditolak(): void
    {
        Storage::fake('public');
        $warga = User::factory()->create(['role' => 'warga']);
        $token = $warga->createToken('test')->plainTextToken;

        // 6MB — melebihi batas 5120KB
        $fileBesar = UploadedFile::fake()->create('foto-besar.jpg', 6144, 'image/jpeg');

        $response = $this->withToken($token)->postJson('/api/upload', [
            'foto' => $fileBesar,
        ]);

        $response->assertStatus(422);
    }

    // ─────────────────────────────────────────
    // 5. Validasi Input Formulir
    // ─────────────────────────────────────────

    /** Registrasi dengan email duplikat harus ditolak */
    public function test_registrasi_email_duplikat_ditolak(): void
    {
        User::factory()->create(['email' => 'duplikat@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'User Lain',
            'email'                 => 'duplikat@example.com',
            'password'              => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /** Login dengan password salah harus ditolak */
    public function test_login_password_salah_ditolak(): void
    {
        User::factory()->create([
            'email'    => 'warga@example.com',
            'password' => Hash::make('passwordbenar'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'warga@example.com',
            'password' => 'passwordsalah',
        ]);

        $response->assertStatus(401);
    }

    /** Input nama dengan tag HTML harus disanitasi (strip_tags) */
    public function test_registrasi_nama_dengan_html_tag_disanitasi(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => '<script>alert("xss")</script>Warga Baik',
            'email'                 => 'wargaxss@example.com',
            'password'              => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'wargaxss@example.com')->first();
        $this->assertNotNull($user);
        // Tag script harus sudah dihapus
        $this->assertStringNotContainsString('<script>', $user->name);
        $this->assertStringContainsString('Warga Baik', $user->name);
    }
}
