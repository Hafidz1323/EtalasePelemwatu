<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Potensi;
use App\Models\PotensiImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PotensiParitasDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_paritas_data_field_publik_muncul_dan_field_privat_tidak_bocor(): void
    {
        $category = Category::create(['name' => 'Kuliner Nusantara Unik']);

        $potensi = Potensi::create([
            'category_id' => $category->id,
            'name' => 'Warung Sate Barokah Unik 123',
            'jenis' => 'Makanan Khas Tradisional',
            'location' => 'Jl. Pelemwatu Tengah RT 03 RW 01',
            'jam_operasional' => '07.30 - 21.30 WIB',
            'whatsapp' => '081299887766',
            'email' => 'sulaiman.barokah@privat.desa.id',
            'owner_name' => 'Haji Sulaiman Pemilik',
            'address' => 'Dusun Krajan RT 01 Pelemwatu (Alamat Rumah Privat)',
            'description' => 'Sate kambing dan ayam bumbu kacang khas resep keluarga turun temurun.',
            'latitude' => -7.245678,
            'longitude' => 112.584712,
            'status' => 'approved',
            'rejection_reason' => 'Catatan internal verifikasi',
        ]);

        PotensiImage::create([
            'potensi_id' => $potensi->id,
            'image_path' => 'uploads/potensi/sate-barokah-123.jpg',
        ]);

        // ─────────────────────────────────────────────
        // 1. Uji Endpoint Publik INDEX: GET /api/potensi
        // ─────────────────────────────────────────────
        $res = $this->getJson('/api/potensi')->assertOk();
        $items = collect($res->json('data'));
        $item = $items->firstWhere('id', $potensi->id);

        $this->assertNotNull($item, 'Potensi harus ditemukan pada GET /api/potensi');

        // Field Publik HARUS MUNCUL dengan nilai yang sesuai
        $this->assertEquals('Warung Sate Barokah Unik 123', $item['name']);
        $this->assertEquals('Kuliner Nusantara Unik', $item['category']['name']);
        $this->assertEquals('Makanan Khas Tradisional', $item['jenis']);
        $this->assertEquals('Jl. Pelemwatu Tengah RT 03 RW 01', $item['location']);
        $this->assertEquals('07.30 - 21.30 WIB', $item['jam_operasional']);
        $this->assertEquals('081299887766', $item['whatsapp']);
        $this->assertEquals('Sate kambing dan ayam bumbu kacang khas resep keluarga turun temurun.', $item['description']);
        $this->assertNotEmpty($item['images']);
        $this->assertEquals('uploads/potensi/sate-barokah-123.jpg', $item['images'][0]['path']);

        // Field Privat SENGAJA DISEMBUNYIKAN (TIDAK BOLEH MUNCUL)
        $this->assertArrayNotHasKey('email', $item);
        $this->assertArrayNotHasKey('owner_name', $item);
        $this->assertArrayNotHasKey('address', $item);
        $this->assertArrayNotHasKey('status', $item);
        $this->assertArrayNotHasKey('rejection_reason', $item);
        $this->assertArrayNotHasKey('reviewed_by', $item);
        $this->assertArrayNotHasKey('reviewed_at', $item);

        // ─────────────────────────────────────────────
        // 2. Uji Endpoint Publik DETAIL: GET /api/potensi/{id}
        // ─────────────────────────────────────────────
        $detailRes = $this->getJson('/api/potensi/'.$potensi->id)->assertOk();
        $detail = $detailRes->json('data');

        $this->assertNotNull($detail);
        $this->assertEquals('Warung Sate Barokah Unik 123', $detail['name']);
        $this->assertEquals('Kuliner Nusantara Unik', $detail['category']['name']);
        $this->assertEquals('Makanan Khas Tradisional', $detail['jenis']);
        $this->assertEquals('Jl. Pelemwatu Tengah RT 03 RW 01', $detail['location']);
        $this->assertEquals('07.30 - 21.30 WIB', $detail['jam_operasional']);
        $this->assertEquals('081299887766', $detail['whatsapp']);
        $this->assertEquals('Sate kambing dan ayam bumbu kacang khas resep keluarga turun temurun.', $detail['description']);
        $this->assertEquals('uploads/potensi/sate-barokah-123.jpg', $detail['images'][0]['path']);

        // Field Privat pada detail TIDAK BOLEH MUNCUL
        $this->assertArrayNotHasKey('email', $detail);
        $this->assertArrayNotHasKey('owner_name', $detail);
        $this->assertArrayNotHasKey('address', $detail);
        $this->assertArrayNotHasKey('status', $detail);
        $this->assertArrayNotHasKey('rejection_reason', $detail);
        $this->assertArrayNotHasKey('reviewed_by', $detail);
        $this->assertArrayNotHasKey('reviewed_at', $detail);

        // ─────────────────────────────────────────────
        // 3. Uji Endpoint Admin (Admin Berhak Melihat Semua Field)
        // ─────────────────────────────────────────────
        $admin = User::create([
            'name' => 'Admin Desa',
            'phone' => '089900112233',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        Sanctum::actingAs($admin);

        $adminRes = $this->getJson('/api/potensi/'.$potensi->id)->assertOk();
        $adminData = $adminRes->json('data');

        $this->assertEquals('sulaiman.barokah@privat.desa.id', $adminData['email']);
        $this->assertEquals('Haji Sulaiman Pemilik', $adminData['owner_name']);
        $this->assertEquals('Dusun Krajan RT 01 Pelemwatu (Alamat Rumah Privat)', $adminData['address']);
        $this->assertEquals('approved', $adminData['status']);
        $this->assertEquals('Catatan internal verifikasi', $adminData['rejection_reason']);
    }
}
