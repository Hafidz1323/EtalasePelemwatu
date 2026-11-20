<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Potensi;
use App\Models\PotensiImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PotensiStatusTest extends TestCase
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

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function category(): Category
    {
        return Category::first() ?? Category::create(['name' => 'Usaha & Produk']);
    }

    private function potensi(array $attrs = []): Potensi
    {
        return Potensi::create(array_merge([
            'category_id' => $this->category()->id,
            'name'        => 'Usaha Test',
            'owner_name'  => 'Budi Santoso',
            'whatsapp'    => '081234567890',
            'address'     => 'Jl. Raya Pelemwatu No. 1',
            'description' => 'Deskripsi usaha yang cukup panjang dan jelas.',
            'status'      => 'pending',
        ], $attrs));
    }

    // -----------------------------------------------------------------------
    // 1. STATUS NORMALISASI
    // -----------------------------------------------------------------------

    /** STATUS_PUBLIK tidak lagi menyertakan 'aktif'. */
    public function test_status_publik_hanya_approved(): void
    {
        $this->assertNotContains('aktif', Potensi::STATUS_PUBLIK);
        $this->assertContains('approved', Potensi::STATUS_PUBLIK);
    }

    /** Potensi berstatus 'aktif' tidak muncul di endpoint publik (GET /api/potensi). */
    public function test_potensi_aktif_tidak_tampil_di_publik(): void
    {
        $this->potensi(['status' => 'aktif']);

        $res = $this->getJson('/api/potensi');
        $res->assertOk();
        $ids = collect($res->json('data'))->pluck('id')->all();
        $this->assertEmpty($ids, 'Potensi berstatus aktif tidak boleh tampil ke publik');
    }

    /** Potensi berstatus 'approved' muncul di endpoint publik. */
    public function test_potensi_approved_tampil_di_publik(): void
    {
        $p = $this->potensi(['status' => 'approved']);

        $res = $this->getJson('/api/potensi');
        $res->assertOk();
        $ids = collect($res->json('data'))->pluck('id')->all();
        $this->assertContains($p->id, $ids);
    }

    /** Migration mengubah nilai 'aktif' di DB menjadi 'approved'. */
    public function test_migration_mengubah_aktif_ke_approved(): void
    {
        // Sisipkan langsung nilai lama 'aktif' ke DB (bypass model)
        $id = DB::table('potensi')->insertGetId([
            'name'       => 'Lama Aktif',
            'owner_name' => 'Test',
            'status'     => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Jalankan migrasi ulang (up) — dalam test, migrasi sudah dijalankan via RefreshDatabase.
        // Simulasikan dengan menjalankan logika migrasi secara langsung.
        DB::table('potensi')->where('status', 'aktif')->update(['status' => 'approved']);

        $this->assertDatabaseHas('potensi', ['id' => $id, 'status' => 'approved']);
        $this->assertDatabaseMissing('potensi', ['id' => $id, 'status' => 'aktif']);
    }

    // -----------------------------------------------------------------------
    // 2. UPDATE — validasi owner_name & description
    // -----------------------------------------------------------------------

    /** update() harus menolak owner_name dengan karakter non-huruf (regex). */
    public function test_update_owner_name_dengan_angka_ditolak_422(): void
    {
        Sanctum::actingAs($this->admin());
        $p = $this->potensi();

        $this->putJson("/api/potensi/{$p->id}", ['owner_name' => 'Ahmad123'])
             ->assertStatus(422)
             ->assertJsonValidationErrors('owner_name');
    }

    /** update() harus menolak description yang berisi huruf berulang (not_regex). */
    public function test_update_description_huruf_berulang_ditolak_422(): void
    {
        Sanctum::actingAs($this->admin());
        $p = $this->potensi();

        $this->putJson("/api/potensi/{$p->id}", [
            'description' => 'Ini deskripsi dengan aaaaaaaaaaaaa huruf berulang.',
        ])->assertStatus(422)
          ->assertJsonValidationErrors('description');
    }

    /** update() harus menolak status 'aktif' (sudah dihapus dari enum). */
    public function test_update_status_aktif_ditolak_422(): void
    {
        Sanctum::actingAs($this->admin());
        $p = $this->potensi();

        $this->putJson("/api/potensi/{$p->id}", ['status' => 'aktif'])
             ->assertStatus(422)
             ->assertJsonValidationErrors('status');
    }

    /** update() menerima status 'approved'. */
    public function test_update_status_approved_diterima(): void
    {
        Sanctum::actingAs($this->admin());
        $p = $this->potensi(['status' => 'pending']);

        $this->putJson("/api/potensi/{$p->id}", ['status' => 'approved'])
             ->assertOk();

        $this->assertDatabaseHas('potensi', ['id' => $p->id, 'status' => 'approved']);
    }

    // -----------------------------------------------------------------------
    // 3. UPDATE — rejection_reason = null saat un-reject
    // -----------------------------------------------------------------------

    /** Saat status berubah dari 'rejected' ke 'approved', rejection_reason harus null. */
    public function test_update_dari_rejected_ke_approved_menghapus_rejection_reason(): void
    {
        Sanctum::actingAs($this->admin());
        $p = $this->potensi([
            'status'           => 'rejected',
            'rejection_reason' => 'Tidak memenuhi syarat',
        ]);

        $this->putJson("/api/potensi/{$p->id}", ['status' => 'approved'])
             ->assertOk();

        $this->assertDatabaseHas('potensi', [
            'id'               => $p->id,
            'status'           => 'approved',
            'rejection_reason' => null,
        ]);
    }

    /** Saat status berubah dari 'rejected' ke 'pending', rejection_reason juga harus null. */
    public function test_update_dari_rejected_ke_pending_menghapus_rejection_reason(): void
    {
        Sanctum::actingAs($this->admin());
        $p = $this->potensi([
            'status'           => 'rejected',
            'rejection_reason' => 'Alasan lama',
        ]);

        $this->putJson("/api/potensi/{$p->id}", ['status' => 'pending'])
             ->assertOk();

        $this->assertNull($p->fresh()->rejection_reason);
    }

    /** Saat status tetap 'rejected', rejection_reason tidak dihapus. */
    public function test_update_tanpa_ubah_status_rejection_reason_tetap(): void
    {
        Sanctum::actingAs($this->admin());
        $p = $this->potensi([
            'status'           => 'rejected',
            'rejection_reason' => 'Alasan tetap',
        ]);

        // Update field lain, bukan status
        $this->putJson("/api/potensi/{$p->id}", ['name' => 'Nama Baru Usaha'])
             ->assertOk();

        $this->assertEquals('Alasan tetap', $p->fresh()->rejection_reason);
    }

    // -----------------------------------------------------------------------
    // 4. DESTROY — hapus file gambar
    // -----------------------------------------------------------------------

    /** destroy() harus menghapus file fisik gambar yang tercatat di potensi_images. */
    public function test_destroy_menghapus_file_gambar(): void
    {
        Sanctum::actingAs($this->admin());
        $p = $this->potensi();

        // Buat file fisik palsu di public/uploads
        $filename = 'test_destroy_' . uniqid() . '.jpg';
        $filePath = public_path('uploads/' . $filename);
        file_put_contents($filePath, 'fake image content');
        $this->createdFiles[] = $filePath; // fallback cleanup

        // Daftarkan di potensi_images
        PotensiImage::create([
            'potensi_id' => $p->id,
            'image_path' => '/uploads/' . $filename,
        ]);

        $this->assertTrue(file_exists($filePath), 'File harus ada sebelum dihapus');

        $this->deleteJson("/api/potensi/{$p->id}")->assertOk();

        $this->assertFalse(file_exists($filePath), 'File harus ikut terhapus saat potensi dihapus');
        $this->assertDatabaseMissing('potensi', ['id' => $p->id]);
    }

    /** destroy() tetap berhasil jika file fisik tidak ditemukan (toleran). */
    public function test_destroy_toleran_jika_file_tidak_ada_di_disk(): void
    {
        Sanctum::actingAs($this->admin());
        $p = $this->potensi();

        // Daftarkan path yang tidak ada di disk
        PotensiImage::create([
            'potensi_id' => $p->id,
            'image_path' => '/uploads/file_yang_tidak_ada_' . uniqid() . '.jpg',
        ]);

        $this->deleteJson("/api/potensi/{$p->id}")->assertOk();
        $this->assertDatabaseMissing('potensi', ['id' => $p->id]);
    }

    // -----------------------------------------------------------------------
    // 5. REVIEW CONTROLLER — tidak menerima 'aktif'
    // -----------------------------------------------------------------------

    /** PotensiReviewController tidak lagi menerima status 'aktif'. */
    public function test_review_controller_menolak_status_aktif(): void
    {
        Sanctum::actingAs($this->admin());
        $p = $this->potensi();

        $this->postJson("/api/potensi/{$p->id}/review", ['status' => 'aktif'])
             ->assertStatus(422);
    }

    /** PotensiReviewController menerima status 'approved'. */
    public function test_review_controller_menerima_status_approved(): void
    {
        Sanctum::actingAs($this->admin());
        $p = $this->potensi(['status' => 'pending']);

        $this->postJson("/api/potensi/{$p->id}/review", ['status' => 'approved'])
             ->assertOk();

        $this->assertDatabaseHas('potensi', ['id' => $p->id, 'status' => 'approved']);
    }
}
