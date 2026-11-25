<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Galeri;
use App\Models\Potensi;
use App\Models\User;
use App\Services\MediaStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SupabaseStorageTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@pelemwatu.test',
        ]);
    }

    private function createWarga(): User
    {
        return User::factory()->create([
            'role' => 'warga',
            'phone' => '081234567890',
            'email' => 'warga@pelemwatu.test',
        ]);
    }

    private function createCategory(): Category
    {
        return Category::create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);
    }

    /**
     * Mode lokal: MediaStorage menyimpan ke public/uploads dan mengembalikan format /uploads/xxx
     */
    public function test_media_storage_local_mode_put_url_and_delete(): void
    {
        Config::set('filesystems.default', 'local');

        $filename = 'unit-test-local-'.uniqid().'.webp';
        $content = 'fake-webp-content-local';

        $savedPath = MediaStorage::put($content, $filename);
        $this->assertSame('/uploads/'.$filename, $savedPath);

        $localFile = public_path('uploads/'.$filename);
        $this->assertFileExists($localFile);
        $this->assertSame($content, file_get_contents($localFile));

        // URL di mode lokal harus mengembalikan path relatif
        $url = MediaStorage::url($savedPath);
        $this->assertSame('/uploads/'.$filename, $url);

        // Hapus berkas
        $deleted = MediaStorage::delete($savedPath);
        $this->assertTrue($deleted);
        $this->assertFileDoesNotExist($localFile);
    }

    /**
     * Mode Supabase: MediaStorage menyimpan ke disk supabase dan url() menghasilkan URL publik
     */
    public function test_media_storage_supabase_mode_put_url_and_delete(): void
    {
        Storage::fake('supabase');
        Config::set('filesystems.default', 'supabase');
        Config::set('filesystems.disks.supabase.url', 'https://mock-proj.supabase.co/storage/v1/object/public/etalase-media');

        $filename = 'unit-test-s3-'.uniqid().'.webp';
        $content = 'fake-webp-content-s3';

        $savedPath = MediaStorage::put($content, $filename);
        $this->assertSame('/uploads/'.$filename, $savedPath);

        // Pastikan tersimpan di disk fake supabase
        Storage::disk('supabase')->assertExists($filename);

        // URL publik harus tersusun dari SUPABASE_PUBLIC_URL
        $url = MediaStorage::url($savedPath);
        $this->assertSame('https://mock-proj.supabase.co/storage/v1/object/public/etalase-media/'.$filename, $url);

        // Hapus dari bucket supabase
        $deleted = MediaStorage::delete($savedPath);
        $this->assertTrue($deleted);
        Storage::disk('supabase')->assertMissing($filename);
    }

    /**
     * MediaStorage::delete() menolak path traversal, direktori assets/, dan path absolut
     */
    public function test_media_storage_delete_security_restrictions(): void
    {
        // Path traversal
        $this->assertFalse(MediaStorage::delete('/uploads/../secret.txt'));
        $this->assertFalse(MediaStorage::delete('../../etc/passwd'));

        // Folder assets
        $this->assertFalse(MediaStorage::delete('assets/img/logo-desa.png'));
        $this->assertFalse(MediaStorage::delete('/assets/img/logo-desa.png'));

        // Path absolut Windows
        $this->assertFalse(MediaStorage::delete('C:\\Windows\\system32\\calc.exe'));
        $this->assertFalse(MediaStorage::delete('D:/uploads/gambar.webp'));

        // Path non-uploads
        $this->assertFalse(MediaStorage::delete('/storage/app/backups/test.zip'));
        $this->assertFalse(MediaStorage::delete(''));
        $this->assertFalse(MediaStorage::delete(null));
    }

    /**
     * Upload potensi dalam mode Supabase menyimpan foto ke bucket supabase
     */
    public function test_potensi_upload_and_destroy_in_supabase_mode(): void
    {
        Storage::fake('supabase');
        Config::set('filesystems.default', 'supabase');
        Config::set('filesystems.disks.supabase.url', 'https://mock-proj.supabase.co/storage/v1/object/public/etalase-media');

        $cat = $this->createCategory();
        $admin = $this->createAdmin();
        Sanctum::actingAs($admin);

        $fakeImage = UploadedFile::fake()->image('warung.jpg', 600, 400);

        $response = $this->postJson('/api/potensi', [
            'name' => 'Warung Supabase',
            'owner_name' => 'Pak Supa',
            'category_id' => $cat->id,
            'whatsapp' => '081234567890',
            'address' => 'Jl. Pelemwatu Timur No. 10',
            'description' => 'Warung kuliner enak dan bersih di pusat Pelemwatu.',
            'images' => [$fakeImage],
        ]);

        $response->assertStatus(201);
        $potensiId = $response->json('data.id');

        $potensi = Potensi::with('images')->find($potensiId);
        $this->assertCount(1, $potensi->images);

        $imagePath = $potensi->images->first()->image_path;
        $this->assertStringStartsWith('/uploads/', $imagePath);

        $filename = basename($imagePath);
        Storage::disk('supabase')->assertExists($filename);

        // GET potensi harus mengembalikan URL publik Supabase di resource images
        $showRes = $this->getJson('/api/potensi/'.$potensiId)->assertOk();
        $imagesData = $showRes->json('data.images');
        $this->assertCount(1, $imagesData);
        $this->assertStringStartsWith('https://mock-proj.supabase.co/storage/v1/object/public/etalase-media/', $imagesData[0]['path']);

        // Hapus potensi -> foto di bucket supabase harus ikut terhapus
        $delRes = $this->deleteJson('/api/potensi/'.$potensiId)->assertOk();
        $delRes->assertJson(['status' => 'success']);

        Storage::disk('supabase')->assertMissing($filename);
    }

    /**
     * Upload galeri dalam mode Supabase menyimpan foto ke bucket dan mengembalikan URL publik
     */
    public function test_galeri_upload_index_and_destroy_in_supabase_mode(): void
    {
        Storage::fake('supabase');
        Config::set('filesystems.default', 'supabase');
        Config::set('filesystems.disks.supabase.url', 'https://mock-proj.supabase.co/storage/v1/object/public/etalase-media');

        $admin = $this->createAdmin();
        Sanctum::actingAs($admin);

        $fakeImage = UploadedFile::fake()->image('kegiatan.jpg', 800, 600);

        $postRes = $this->postJson('/api/galeri', [
            'image' => $fakeImage,
            'caption' => 'Kerja Bakti Supabase',
            'description' => 'Warga bergotong royong.',
        ])->assertStatus(201);

        $galeriId = $postRes->json('data.id');
        $item = Galeri::find($galeriId);
        $this->assertNotNull($item);
        $this->assertStringStartsWith('/uploads/', $item->image_path);

        $filename = basename($item->image_path);
        Storage::disk('supabase')->assertExists($filename);

        // GET /api/galeri mengembalikan URL Supabase
        $getRes = $this->getJson('/api/galeri')->assertOk();
        $firstItem = collect($getRes->json('data'))->firstWhere('id', $galeriId);
        $this->assertNotNull($firstItem);
        $this->assertStringStartsWith('https://mock-proj.supabase.co/storage/v1/object/public/etalase-media/', $firstItem['image_path']);

        // DELETE /api/galeri/{id} menghapus foto dari bucket
        $this->deleteJson('/api/galeri/'.$galeriId)->assertOk();
        Storage::disk('supabase')->assertMissing($filename);
    }

    /**
     * Avatar profil admin dalam mode Supabase
     */
    public function test_admin_avatar_in_supabase_mode(): void
    {
        Storage::fake('supabase');
        Config::set('filesystems.default', 'supabase');
        Config::set('filesystems.disks.supabase.url', 'https://mock-proj.supabase.co/storage/v1/object/public/etalase-media');

        $admin = $this->createAdmin();
        Sanctum::actingAs($admin);

        $avatarFile = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $updateRes = $this->postJson('/api/admin/profile', [
            'name' => 'Admin Baru',
            'email' => 'admin@pelemwatu.test',
            'avatar' => $avatarFile,
        ])->assertOk();

        $avatarUrl = $updateRes->json('data.avatar');
        $this->assertStringStartsWith('https://mock-proj.supabase.co/storage/v1/object/public/etalase-media/', $avatarUrl);

        $savedDbPath = $admin->fresh()->avatar;
        $this->assertStringStartsWith('/uploads/', $savedDbPath);
        $oldFilename = basename($savedDbPath);
        Storage::disk('supabase')->assertExists($oldFilename);

        // Upload avatar kedua -> avatar lama di bucket harus terhapus
        $avatar2 = UploadedFile::fake()->image('avatar2.jpg', 200, 200);
        $updateRes2 = $this->postJson('/api/admin/profile', [
            'name' => 'Admin Baru',
            'email' => 'admin@pelemwatu.test',
            'avatar' => $avatar2,
        ])->assertOk();

        Storage::disk('supabase')->assertMissing($oldFilename);
        $newFilename = basename($admin->fresh()->avatar);
        Storage::disk('supabase')->assertExists($newFilename);

        // GET /api/admin/profile mengembalikan URL publik
        $showRes = $this->getJson('/api/admin/profile')->assertOk();
        $this->assertStringStartsWith('https://mock-proj.supabase.co/storage/v1/object/public/etalase-media/', $showRes->json('data.avatar'));
    }

    /**
     * Command media:migrate-to-supabase menyalin berkas lokal ke bucket
     */
    public function test_migrate_media_command(): void
    {
        Storage::fake('supabase');
        Config::set('filesystems.default', 'supabase');

        $uploadsDir = public_path('uploads');
        if (! is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        $dummyFilename = 'dummy-migrasi-'.uniqid().'.txt';
        $dummyPath = $uploadsDir.DIRECTORY_SEPARATOR.$dummyFilename;
        file_put_contents($dummyPath, 'isi dummy untuk migrasi');

        try {
            // 1. Dry run: tidak mengunggah
            $this->artisan('media:migrate-to-supabase --dry-run')
                ->expectsOutputToContain('MODE DRY-RUN DIAKTIFKAN')
                ->assertSuccessful();

            Storage::disk('supabase')->assertMissing($dummyFilename);

            // 2. Real run: berhasil mengunggah
            $this->artisan('media:migrate-to-supabase')
                ->expectsOutputToContain('Migrasi media ke Supabase Storage berhasil')
                ->assertSuccessful();

            Storage::disk('supabase')->assertExists($dummyFilename);

            // 3. Idempoten: dijalankan ulang melewati berkas yang sudah ada
            $this->artisan('media:migrate-to-supabase')
                ->expectsOutputToContain('sudah ada di bucket')
                ->assertSuccessful();
        } finally {
            if (file_exists($dummyPath)) {
                @unlink($dummyPath);
            }
        }
    }

    /**
     * Command media:test-supabase gagal jika konfigurasi kosong
     */
    public function test_test_supabase_command_fails_when_unconfigured(): void
    {
        Config::set('filesystems.disks.supabase.key', '');
        Config::set('filesystems.disks.supabase.secret', '');

        $this->artisan('media:test-supabase')
            ->expectsOutputToContain('GAGAL: Konfigurasi Supabase Storage belum lengkap')
            ->assertFailed();
    }

    /**
     * Command media:test-supabase berhasil bila upload, URL publik, dan delete berhasil
     */
    public function test_test_supabase_command_success(): void
    {
        Storage::fake('supabase');
        Config::set('filesystems.disks.supabase.endpoint', 'https://mock.supabase.co/storage/v1/s3');
        Config::set('filesystems.disks.supabase.bucket', 'etalase-media');
        Config::set('filesystems.disks.supabase.key', 'fake-key');
        Config::set('filesystems.disks.supabase.secret', 'fake-secret');
        Config::set('filesystems.disks.supabase.url', 'https://mock.supabase.co/storage/v1/object/public/etalase-media');

        // Fake HTTP client untuk verifikasi URL publik: kembalikan konten persis dari storage fake
        Http::fake(function ($request) {
            $filename = basename(parse_url($request->url(), PHP_URL_PATH));

            return Http::response(
                Storage::disk('supabase')->get($filename),
                200
            );
        });

        $this->artisan('media:test-supabase')
            ->expectsOutputToContain('Hasil Uji Koneksi Supabase Storage: BERHASIL')
            ->assertSuccessful();
    }
}
