<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Potensi;
use App\Models\PotensiImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BugFixTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────
    // BUG-01: image_path harus muncul di respons
    // ─────────────────────────────────────────────

    public function test_foto_path_muncul_di_respons_publik(): void
    {
        $cat = Category::create(['name' => 'Usaha']);
        $p   = Potensi::create([
            'name'        => 'Warung Pak Budi',
            'owner_name'  => 'Budi Santoso',
            'whatsapp'    => '081234567890',
            'address'     => 'Jl. Desa No. 1 Pelemwatu',
            'description' => 'Warung makan enak dan murah di desa',
            'status'      => 'approved',
            'category_id' => $cat->id,
        ]);
        PotensiImage::create([
            'potensi_id' => $p->id,
            'image_path' => '/uploads/foto-test-abc123.jpg',
        ]);

        $res = $this->getJson('/api/potensi');

        $res->assertOk()
            ->assertJsonPath('data.0.images.0.path', '/uploads/foto-test-abc123.jpg');
    }

    public function test_foto_path_muncul_di_respons_admin(): void
    {
        $admin = User::factory()->create();
        $cat   = Category::create(['name' => 'Jasa']);
        $p     = Potensi::create([
            'name'        => 'Jasa Jahit Bu Ani',
            'owner_name'  => 'Ani Wulandari',
            'whatsapp'    => '081234567891',
            'address'     => 'Jl. Desa No. 2 Pelemwatu',
            'description' => 'Jasa jahit baju dengan harga terjangkau',
            'status'      => 'pending',
            'category_id' => $cat->id,
        ]);
        PotensiImage::create([
            'potensi_id' => $p->id,
            'image_path' => '/uploads/foto-jahit-xyz789.jpg',
        ]);

        $token = $admin->createToken('test')->plainTextToken;

        $res = $this->withToken($token)->getJson('/api/potensi');

        $res->assertOk()
            ->assertJsonPath('data.0.images.0.path', '/uploads/foto-jahit-xyz789.jpg');
    }

    public function test_foto_path_muncul_di_show_publik(): void
    {
        $cat = Category::create(['name' => 'Barang']);
        $p   = Potensi::create([
            'name'        => 'Kerajinan Anyaman',
            'owner_name'  => 'Sari Dewi',
            'whatsapp'    => '081234567892',
            'address'     => 'Jl. Desa No. 3 Pelemwatu',
            'description' => 'Kerajinan anyaman bambu khas Desa Pelemwatu',
            'status'      => 'approved',
            'category_id' => $cat->id,
        ]);
        PotensiImage::create([
            'potensi_id' => $p->id,
            'image_path' => '/uploads/foto-anyaman-def456.webp',
        ]);

        $res = $this->getJson('/api/potensi/' . $p->id);

        $res->assertOk()
            ->assertJsonPath('data.images.0.path', '/uploads/foto-anyaman-def456.webp');
    }

    // ─────────────────────────────────────────────
    // BUG-05: default status baru harus 'pending'
    // ─────────────────────────────────────────────

    public function test_potensi_baru_default_status_pending(): void
    {
        // Insert langsung tanpa field status untuk membuktikan default DB = 'pending'
        \DB::table('potensi')->insert([
            'name'        => 'Usaha Baru Tanpa Status',
            'owner_name'  => 'Test Owner',
            'whatsapp'    => '081234567893',
            'address'     => 'Jl. Desa No. 4',
            'description' => 'Deskripsi potensi usaha yang belum punya status',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->assertDatabaseHas('potensi', [
            'name'   => 'Usaha Baru Tanpa Status',
            'status' => 'pending',
        ]);
    }

    public function test_potensi_pending_tidak_muncul_di_publik(): void
    {
        Potensi::create([
            'name'        => 'Potensi Belum Disetujui',
            'owner_name'  => 'Owner Pending',
            'whatsapp'    => '081234567894',
            'address'     => 'Jl. Desa No. 5 Pelemwatu',
            'description' => 'Ini potensi yang masih pending verifikasi admin',
            'status'      => 'pending',
        ]);

        $res = $this->getJson('/api/potensi');

        $names = collect($res->json('data'))->pluck('name');
        $this->assertNotContains('Potensi Belum Disetujui', $names);
    }

    // ─────────────────────────────────────────────
    // LOGIKA-06: WA tidak bocor ke publik (default show_contact=false)
    // ─────────────────────────────────────────────

    public function test_whatsapp_tidak_muncul_di_respons_publik_saat_show_contact_false(): void
    {
        config(['potensi.show_contact' => false]);

        Potensi::create([
            'name'        => 'Usaha Rahasia WA',
            'owner_name'  => 'Owner Privat',
            'whatsapp'    => '081999888777',
            'address'     => 'Jl. Privat No. 1 Pelemwatu',
            'description' => 'Usaha yang nomor WA-nya tidak tampil ke publik',
            'status'      => 'approved',
        ]);

        $res = $this->getJson('/api/potensi');

        $res->assertOk();
        $item = collect($res->json('data'))->first();
        $this->assertArrayNotHasKey('whatsapp', $item,
            'Nomor WhatsApp tidak boleh tampil di respons publik saat show_contact=false');
    }

    public function test_whatsapp_muncul_di_respons_publik_saat_show_contact_true(): void
    {
        config(['potensi.show_contact' => true]);

        Potensi::create([
            'name'        => 'Usaha Tampil WA',
            'owner_name'  => 'Owner Publik',
            'whatsapp'    => '082111222333',
            'address'     => 'Jl. Publik No. 2 Pelemwatu',
            'description' => 'Usaha yang nomor WA-nya boleh tampil ke publik',
            'status'      => 'approved',
        ]);

        $res = $this->getJson('/api/potensi');

        $res->assertOk();
        $item = collect($res->json('data'))->first();
        $this->assertArrayHasKey('whatsapp', $item,
            'Nomor WhatsApp harus tampil di respons publik saat show_contact=true');
    }
}
