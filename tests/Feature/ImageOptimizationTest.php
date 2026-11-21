<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PotensiImage;
use App\Services\ImageOptimizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ImageOptimizationTest extends TestCase
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
            if (File::exists($path)) {
                @unlink($path);
            }
        }
        parent::tearDown();
    }

    public function test_image_optimizer_resizes_large_image_and_converts_to_webp(): void
    {
        // Buat file gambar resolusi tinggi (2000x1200)
        $file = UploadedFile::fake()->image('large-photo.jpg', 2000, 1200);

        $savedFilename = ImageOptimizer::processAndSave($file, 'test');
        $savedPath = public_path('uploads/'.$savedFilename);
        $this->createdFiles[] = $savedPath;

        $this->assertFileExists($savedPath);

        // Jika ekstensi GD webp tersedia, cek format webp dan resolusi maksimal 1600px
        if (extension_loaded('gd') && function_exists('imagewebp')) {
            $this->assertStringEndsWith('.webp', $savedFilename);
            $size = getimagesize($savedPath);
            $this->assertLessThanOrEqual(1600, $size[0]);
            $this->assertLessThanOrEqual(1600, $size[1]);
        }
    }

    public function test_potensi_store_saves_optimized_image(): void
    {
        $cat = Category::firstOrCreate(['name' => 'Usaha & Produk']);
        $file = UploadedFile::fake()->image('produk-jumbo.png', 1800, 1000);

        $res = $this->postJson('/api/potensi', [
            'name' => 'Produk Keripik Jaya',
            'owner_name' => 'Suryanto',
            'category_id' => $cat->id,
            'whatsapp' => '081234567890',
            'address' => 'Dusun Krajan RT 01 RW 01 Pelemwatu',
            'description' => 'Produksi keripik singkong renyah aneka rasa khas desa.',
            'images' => [$file],
        ])->assertCreated();

        $potensiId = $res->json('data.id');
        $img = PotensiImage::where('potensi_id', $potensiId)->first();
        $this->assertNotNull($img);

        $diskPath = public_path(ltrim($img->image_path, '/\\'));
        $this->createdFiles[] = $diskPath;
        $this->assertFileExists($diskPath);

        if (extension_loaded('gd') && function_exists('imagewebp')) {
            $this->assertStringEndsWith('.webp', $img->image_path);
        }
    }
}
