<?php

namespace Tests\Feature;

use App\Models\Galeri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GaleriTest extends TestCase
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
            'phone' => '081234567890',
            'password' => Hash::make('password123'),
            'role' => 'warga',
        ]);
    }

    private function trackFile(?string $path): void
    {
        if ($path) {
            $this->createdFiles[] = public_path(ltrim($path, '/\\'));
        }
    }

    public function test_get_galeri_publik_berisi_data(): void
    {
        $res = $this->getJson('/api/galeri');

        $res->assertStatus(200)
            ->assertJson(['status' => 'success'])
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => ['id', 'image_path', 'caption', 'description', 'sort_order', 'created_at', 'updated_at'],
                ],
            ]);

        // Default seeder/migration memasukkan 5 item
        $this->assertGreaterThanOrEqual(5, count($res->json('data')));
    }

    public function test_post_galeri_ditolak_tanpa_token(): void
    {
        $res = $this->postJson('/api/galeri', [
            'caption' => 'Kegiatan Tanpa Token',
        ]);

        $res->assertStatus(401);
    }

    public function test_post_galeri_ditolak_untuk_warga(): void
    {
        Sanctum::actingAs($this->makeWarga());

        $file = UploadedFile::fake()->image('kegiatan.jpg');

        $res = $this->postJson('/api/galeri', [
            'image' => $file,
            'caption' => 'Kegiatan Oleh Warga',
            'description' => 'Mencoba upload sebagai warga biasa',
        ]);

        $res->assertStatus(403)
            ->assertJson(['status' => 'error', 'message' => 'Akses ditolak.']);
    }

    public function test_put_galeri_ditolak_tanpa_token(): void
    {
        $galeri = Galeri::first();
        $res = $this->putJson('/api/galeri/'.$galeri->id, [
            'caption' => 'Ubah Caption Tanpa Token',
        ]);

        $res->assertStatus(401);
    }

    public function test_put_galeri_ditolak_untuk_warga(): void
    {
        Sanctum::actingAs($this->makeWarga());
        $galeri = Galeri::first();

        $res = $this->putJson('/api/galeri/'.$galeri->id, [
            'caption' => 'Ubah Caption Oleh Warga',
        ]);

        $res->assertStatus(403)
            ->assertJson(['status' => 'error', 'message' => 'Akses ditolak.']);
    }

    public function test_delete_galeri_ditolak_tanpa_token(): void
    {
        $galeri = Galeri::first();
        $res = $this->deleteJson('/api/galeri/'.$galeri->id);

        $res->assertStatus(401);
    }

    public function test_delete_galeri_ditolak_untuk_warga(): void
    {
        Sanctum::actingAs($this->makeWarga());
        $galeri = Galeri::first();

        $res = $this->deleteJson('/api/galeri/'.$galeri->id);

        $res->assertStatus(403)
            ->assertJson(['status' => 'error', 'message' => 'Akses ditolak.']);
    }

    public function test_admin_berhasil_menambah_foto_galeri(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        $file = UploadedFile::fake()->image('kegiatan-bersih-desa.png', 800, 600);

        $res = $this->postJson('/api/galeri', [
            'image' => $file,
            'caption' => 'Kegiatan Bersih Desa',
            'description' => 'Seluruh warga membersihkan saluran air dan jalan utama.',
        ]);

        $res->assertStatus(201)
            ->assertJson(['status' => 'success'])
            ->assertJsonPath('data.caption', 'Kegiatan Bersih Desa');

        $uploadedPath = $res->json('data.image_path');
        $this->trackFile($uploadedPath);

        $this->assertDatabaseHas('galeri', [
            'caption' => 'Kegiatan Bersih Desa',
            'image_path' => $uploadedPath,
        ]);

        $this->assertFileExists(public_path(ltrim($uploadedPath, '/\\')));
    }

    public function test_admin_berhasil_mengubah_caption_dan_deskripsi_galeri(): void
    {
        Sanctum::actingAs($this->makeAdmin());
        $galeri = Galeri::first();

        $res = $this->putJson('/api/galeri/'.$galeri->id, [
            'caption' => 'Judul Diperbarui Admin',
            'description' => 'Deskripsi baru yang telah disesuaikan oleh administrator.',
        ]);

        $res->assertStatus(200)
            ->assertJson(['status' => 'success'])
            ->assertJsonPath('data.caption', 'Judul Diperbarui Admin');

        $this->assertDatabaseHas('galeri', [
            'id' => $galeri->id,
            'caption' => 'Judul Diperbarui Admin',
            'description' => 'Deskripsi baru yang telah disesuaikan oleh administrator.',
        ]);
    }

    public function test_admin_berhasil_menghapus_galeri_dan_file_fisik_ikut_terhapus(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        // Buat file fisik di public/uploads
        $filename = 'test-delete-'.uniqid().'.jpg';
        $fullPath = public_path('uploads/'.$filename);
        file_put_contents($fullPath, 'fake image content');
        $this->trackFile('/uploads/'.$filename);

        $galeri = Galeri::create([
            'image_path' => '/uploads/'.$filename,
            'caption' => 'Foto Untuk Dihapus',
            'description' => 'Foto testing hapus file fisik',
            'sort_order' => 99,
        ]);

        $this->assertFileExists($fullPath);

        $res = $this->deleteJson('/api/galeri/'.$galeri->id);

        $res->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseMissing('galeri', ['id' => $galeri->id]);
        $this->assertFileDoesNotExist($fullPath);
    }

    public function test_menghapus_galeri_bawaan_tidak_menghapus_file_di_assets_img(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        $galeri = Galeri::where('image_path', 'like', 'assets/img/%')->first();
        $this->assertNotNull($galeri);

        $assetPath = public_path($galeri->image_path);
        // Pastikan file aset memang ada di repositori sebelum dihapus
        $fileExisted = file_exists($assetPath);

        $res = $this->deleteJson('/api/galeri/'.$galeri->id);

        $res->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseMissing('galeri', ['id' => $galeri->id]);

        if ($fileExisted) {
            $this->assertFileExists($assetPath);
        }
    }

    public function test_file_non_gambar_ditolak(): void
    {
        Sanctum::actingAs($this->makeAdmin());

        $file = UploadedFile::fake()->create('dokumen.pdf', 500, 'application/pdf');

        $res = $this->postJson('/api/galeri', [
            'image' => $file,
            'caption' => 'Upload Dokumen PDF',
            'description' => 'Harus ditolak karena bukan gambar',
        ]);

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['image']);
    }
}
