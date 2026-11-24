<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PotensiImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    /** File yang dibuat selama test, dibersihkan di tearDown */
    private array $createdFiles = [];

    protected function setUp(): void
    {
        parent::setUp();
        // Pastikan direktori uploads ada
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

    private function admin(): User
    {
        return User::factory()->admin()->create(['email' => 'admin@test.id', 'password' => 'Rahasia123']);
    }

    /** Catat file yang perlu dibersihkan berdasarkan path response JSON */
    private function trackUploadedFile(?string $jsonPath): void
    {
        if ($jsonPath) {
            $this->createdFiles[] = public_path(ltrim($jsonPath, '/'));
        }
    }

    // -----------------------------------------------------------------------
    // Avatar (ProfileController::update)
    // -----------------------------------------------------------------------

    /**
     * PNG asli dengan nama evil.php: validator Laravel menolak ekstensi .php
     * karena rule mimes:jpg,jpeg,png,webp hanya mengizinkan ekstensi gambar.
     * Ini adalah lapisan pertahanan pertama — file berbahaya sudah ditolak di sini.
     */
    public function test_avatar_png_bernama_evil_php_tidak_tersimpan_berekstensi_php(): void
    {
        $admin = $this->admin();
        Sanctum::actingAs($admin);

        // File PNG nyata dengan nama evil.php → ditolak validator (mimes)
        $evilFile = UploadedFile::fake()->image('evil.php', 20, 20);
        $this->postJson('/api/admin/profile', [
            'name' => $admin->name,
            'email' => $admin->email,
            'avatar' => $evilFile,
        ])->assertStatus(422); // ← file .php DITOLAK, tidak pernah tersimpan

        // File PNG dengan ekstensi sah → lolos, tetapi controller harus menyimpan
        // dengan ekstensi dari ISI file (via $file->extension()), bukan nama klien.
        $pngFile = UploadedFile::fake()->image('foto.png', 20, 20);
        $this->postJson('/api/admin/profile', [
            'name' => $admin->name,
            'email' => $admin->email,
            'avatar' => $pngFile,
        ])->assertOk();

        $savedPath = $admin->fresh()->avatar;
        $this->assertNotNull($savedPath, 'Avatar harus tersimpan di DB');
        $this->trackUploadedFile($savedPath);

        // Nama file harus acak (tidak mengandung "foto")
        $this->assertStringNotContainsString('foto', basename($savedPath));

        // Ekstensi dari isi file PNG adalah .png
        $this->assertMatchesRegularExpression(
            '#\.(png|jpg|jpeg|webp)$#i',
            $savedPath,
            'Ekstensi harus berasal dari isi file (png/jpg/jpeg/webp)'
        );
        $this->assertStringNotContainsString('.php', $savedPath);
    }

    /**
     * File non-gambar yang diunggah sebagai avatar harus ditolak (422).
     */
    public function test_avatar_file_bukan_gambar_ditolak_422(): void
    {
        $admin = $this->admin();
        Sanctum::actingAs($admin);

        $this->postJson('/api/admin/profile', [
            'name' => $admin->name,
            'email' => $admin->email,
            'avatar' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ])->assertStatus(422);
    }

    /**
     * File PHP murni (teks) yang diunggah sebagai avatar harus ditolak (422).
     */
    public function test_avatar_file_php_murni_ditolak_422(): void
    {
        $admin = $this->admin();
        Sanctum::actingAs($admin);

        $this->postJson('/api/admin/profile', [
            'name' => $admin->name,
            'email' => $admin->email,
            'avatar' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php'),
        ])->assertStatus(422);
    }

    // -----------------------------------------------------------------------
    // Logo (SettingController::uploadLogo)
    // -----------------------------------------------------------------------

    /**
     * PNG asli dengan nama evil.php: ditolak validator (mimes).
     * PNG dengan ekstensi sah: disimpan dengan ekstensi dari ISI file (bukan nama klien).
     */
    public function test_logo_png_bernama_evil_php_tidak_tersimpan_berekstensi_php(): void
    {
        Sanctum::actingAs($this->admin());

        // File dengan ekstensi .php → ditolak
        $this->postJson('/api/settings/logo', [
            'logo' => UploadedFile::fake()->image('evil.php', 20, 20),
        ])->assertStatus(422);

        // File PNG sah → lolos; pastikan path yang tersimpan tidak berekstensi .php
        $response = $this->postJson('/api/settings/logo', [
            'logo' => UploadedFile::fake()->image('logo.png', 20, 20),
        ])->assertOk();

        $savedPath = strtok($response->json('data.path') ?? '', '?');
        $this->assertNotEmpty($savedPath);
        $this->trackUploadedFile($savedPath);

        // Nama acak (tidak mengandung "logo.png")
        $this->assertStringNotContainsString('logo.png', basename($savedPath));

        // Ekstensi dari isi file
        $this->assertMatchesRegularExpression('#\.(png|jpg|jpeg|webp)$#i', $savedPath);
        $this->assertStringNotContainsString('.php', $savedPath);
    }

    /**
     * File non-gambar yang diunggah sebagai logo harus ditolak (422).
     */
    public function test_logo_file_bukan_gambar_ditolak_422(): void
    {
        Sanctum::actingAs($this->admin());

        $this->postJson('/api/settings/logo', [
            'logo' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ])->assertStatus(422);
    }

    /**
     * File PHP murni (teks) yang diunggah sebagai logo harus ditolak (422).
     */
    public function test_logo_file_php_murni_ditolak_422(): void
    {
        Sanctum::actingAs($this->admin());

        $this->postJson('/api/settings/logo', [
            'logo' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php'),
        ])->assertStatus(422);
    }

    // -----------------------------------------------------------------------
    // Nama file acak (tidak mengandung nama asli klien)
    // -----------------------------------------------------------------------

    /**
     * Nama file yang tersimpan harus berupa string acak,
     * bukan memakai nama asli yang dikirim klien.
     */
    public function test_nama_file_avatar_adalah_acak_bukan_nama_klien(): void
    {
        $admin = $this->admin();
        Sanctum::actingAs($admin);

        $file = UploadedFile::fake()->image('fotosaya.png', 20, 20);

        $this->postJson('/api/admin/profile', [
            'name' => $admin->name,
            'email' => $admin->email,
            'avatar' => $file,
        ])->assertOk();

        $savedPath = $admin->fresh()->avatar;
        $this->trackUploadedFile($savedPath);

        // Nama file tidak boleh mengandung "fotosaya"
        $this->assertStringNotContainsString(
            'fotosaya',
            basename($savedPath ?? ''),
            'Nama file di server tidak boleh mencerminkan nama yang dikirim klien'
        );
    }

    // -----------------------------------------------------------------------
    // POST /api/potensi — upload foto publik (tanpa login)
    // -----------------------------------------------------------------------

    /** Helper: data potensi minimal yang valid */
    private function potensiPayload(array $override = []): array
    {
        $cat = Category::first() ?? Category::create(['name' => 'Usaha & Produk']);

        return array_merge([
            'name' => 'Usaha Test Upload',
            'owner_name' => 'Budi Santoso',
            'whatsapp' => '081234567890',
            'address' => 'Jl. Raya Pelemwatu No. 1 Desa Pelemwatu',
            'description' => 'Deskripsi potensi yang cukup panjang dan jelas untuk memenuhi validasi.',
            'category_id' => $cat->id,
        ], $override);
    }

    /**
     * POST /api/potensi dengan foto valid (tanpa login): foto tersimpan di
     * potensi_images, ekstensi dari isi file, nama acak.
     */
    public function test_potensi_store_dengan_foto_valid_tersimpan(): void
    {
        $payload = $this->potensiPayload();
        $payload['images'] = [UploadedFile::fake()->image('foto1.jpg', 100, 100)];

        $response = $this->post('/api/potensi', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['id']]);

        $potensiId = $response->json('data.id');

        // Pastikan record di potensi_images ada
        $this->assertDatabaseHas('potensi_images', ['potensi_id' => $potensiId]);

        // Ambil path, track untuk cleanup
        $imgRecord = PotensiImage::where('potensi_id', $potensiId)->first();
        $this->assertNotNull($imgRecord);
        $this->trackUploadedFile($imgRecord->image_path);

        // Ekstensi tidak boleh .php
        $this->assertStringNotContainsString('.php', $imgRecord->image_path);
        // Ekstensi dari isi file (jpg/jpeg/png/webp)
        $this->assertMatchesRegularExpression('#\.(jpg|jpeg|png|webp)$#i', $imgRecord->image_path);
        // Nama acak (tidak mengandung "foto1")
        $this->assertStringNotContainsString('foto1', basename($imgRecord->image_path));
    }

    /**
     * POST /api/potensi dengan foto > 2 MB ditolak 422.
     */
    public function test_potensi_store_foto_terlalu_besar_ditolak_422(): void
    {
        $payload = $this->potensiPayload();
        $payload['images'] = [UploadedFile::fake()->create('besar.jpg', 3000, 'image/jpeg')]; // 3 MB

        $this->post('/api/potensi', $payload)->assertStatus(422);
    }

    /**
     * POST /api/potensi dengan > 3 foto ditolak 422.
     */
    public function test_potensi_store_lebih_dari_3_foto_ditolak_422(): void
    {
        $payload = $this->potensiPayload();
        $payload['images'] = [
            UploadedFile::fake()->image('a.jpg', 10, 10),
            UploadedFile::fake()->image('b.jpg', 10, 10),
            UploadedFile::fake()->image('c.jpg', 10, 10),
            UploadedFile::fake()->image('d.jpg', 10, 10),
        ];

        $this->post('/api/potensi', $payload)->assertStatus(422);
    }

    /**
     * POST /api/potensi dengan foto bukan gambar (pdf) ditolak 422.
     */
    public function test_potensi_store_foto_bukan_gambar_ditolak_422(): void
    {
        $payload = $this->potensiPayload();
        $payload['images'] = [UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf')];

        $this->post('/api/potensi', $payload)->assertStatus(422);
    }

    /**
     * POST /api/potensi tanpa foto tetap berhasil (foto opsional).
     */
    public function test_potensi_store_tanpa_foto_berhasil(): void
    {
        $this->postJson('/api/potensi', $this->potensiPayload())
            ->assertStatus(201)
            ->assertJsonPath('status', 'success');
    }
}
