<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Message;
use App\Models\Potensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['email' => 'admin@test.id', 'password' => 'Rahasia123']);
    }

    private function potensi(string $status = 'aktif', array $extra = []): Potensi
    {
        return Potensi::create(array_merge([
            'name' => 'Usaha Uji', 'owner_name' => 'Budi Santoso', 'whatsapp' => '081234567890',
            'address' => 'Jl. Melati No. 12', 'description' => 'Deskripsi usaha untuk pengujian.',
            'status' => $status,
        ], $extra));
    }

    private function payload(array $o = []): array
    {
        return array_merge([
            'name' => 'Warung Baru', 'owner_name' => 'Siti Aminah', 'whatsapp' => '081298765432',
            'address' => 'Jl. Raya Pelemwatu 45', 'description' => 'Warung makan rumahan dengan menu harian.',
        ], $o);
    }

    // ---------- Proteksi rute ----------

    public function test_rute_tulis_ditolak_tanpa_token(): void
    {
        $p = $this->potensi();
        $c = Category::create(['name' => 'Jasa']);

        $this->putJson("/api/potensi/{$p->id}", ['name' => 'Diretas'])->assertStatus(401);
        $this->deleteJson("/api/potensi/{$p->id}")->assertStatus(401);
        $this->postJson('/api/categories', ['name' => 'X'])->assertStatus(401);
        $this->putJson("/api/categories/{$c->id}", ['name' => 'X'])->assertStatus(401);
        $this->deleteJson("/api/categories/{$c->id}")->assertStatus(401);
        $this->postJson("/api/potensi/{$p->id}/review", ['status' => 'approved'])->assertStatus(401);
        $this->postJson('/api/upload', [])->assertStatus(401);
        $this->getJson('/api/messages')->assertStatus(401);
        $this->getJson('/api/stats')->assertStatus(401);

        $this->assertSame('Usaha Uji', $p->fresh()->name);
        $this->assertNotNull(Category::find($c->id));
    }

    public function test_pesan_masuk_tidak_bocor_ke_publik(): void
    {
        Message::create(['name' => 'A', 'email' => 'a@b.id', 'message' => 'rahasia']);
        $this->getJson('/api/messages')->assertStatus(401)->assertDontSee('rahasia');
    }

    public function test_rute_publik_tetap_bisa_diakses(): void
    {
        $this->getJson('/api/status')->assertOk();
        $this->getJson('/api/categories')->assertOk();
        $this->getJson('/api/potensi')->assertOk();
        $this->getJson('/api/settings/logo')->assertOk();
        $this->postJson('/api/messages', ['name' => 'A', 'email' => 'a@b.id', 'message' => 'Halo'])->assertStatus(201);
    }

    // ---------- Login ----------

    public function test_login_hardcoded_admin_tidak_berlaku(): void
    {
        $this->postJson('/api/login', ['email' => 'admin@pelemwatu.desa.id', 'password' => 'admin123'])
            ->assertStatus(401);
    }

    public function test_login_user_db_berhasil_dan_salah_password_gagal(): void
    {
        $this->admin();
        $this->postJson('/api/auth/login', ['email' => 'ADMIN@test.id', 'password' => 'Rahasia123'])
            ->assertOk()->assertJsonStructure(['data' => ['token', 'user']])
            ->assertJsonMissing(['password']);
        $this->postJson('/api/auth/login', ['email' => 'admin@test.id', 'password' => 'salah'])->assertStatus(401);
    }

    public function test_login_tanpa_input_tidak_500(): void
    {
        $this->postJson('/api/login', [])->assertStatus(422);
        $this->postJson('/api/login', ['email' => ['x'], 'password' => 'y'])->assertStatus(422);
    }

    public function test_login_dibatasi_throttle(): void
    {
        $last = null;
        for ($i = 0; $i < 12; $i++) {
            $last = $this->postJson('/api/login', ['email' => 'x@y.id', 'password' => 'salah'])->status();
        }
        $this->assertSame(429, $last);
    }

    // ---------- Verify & logout ----------

    public function test_verify_menolak_token_palsu(): void
    {
        $this->getJson('/api/auth/verify', ['Authorization' => 'Bearer palsu'])->assertStatus(401);
        $this->getJson('/api/auth/verify')->assertStatus(401);
    }

    public function test_verify_menerima_token_asli(): void
    {
        $token = $this->admin()->createToken('t')->plainTextToken;
        $this->getJson('/api/auth/verify', ['Authorization' => "Bearer $token"])->assertOk();
    }

    public function test_logout_hanya_cabut_token_saat_ini(): void
    {
        $u = $this->admin();
        $t1 = $u->createToken('a')->plainTextToken;
        $u->createToken('b');
        $this->postJson('/api/auth/logout', [], ['Authorization' => "Bearer $t1"])->assertOk();
        $this->assertSame(1, $u->tokens()->count());
    }

    public function test_logout_tanpa_token_ditolak(): void
    {
        $this->postJson('/api/auth/logout')->assertStatus(401);
    }

    public function test_logout_sesi_transient_tidak_500(): void
    {
        Sanctum::actingAs($this->admin());
        $this->postJson('/api/auth/logout')->assertOk();
    }

    // ---------- Validasi / mass assignment ----------

    public function test_kategori_validasi_dan_mass_assignment(): void
    {
        Sanctum::actingAs($this->admin());
        $this->postJson('/api/categories', [])->assertStatus(422);
        $this->postJson('/api/categories', ['name' => str_repeat('a', 300)])->assertStatus(422);
        $r = $this->postJson('/api/categories', ['name' => 'Baru', 'id' => 999, 'created_at' => '2000-01-01'])->assertStatus(201);
        $this->assertNotSame(999, $r->json('data.id'));
        $c = Category::find($r->json('data.id'));
        $this->assertNotSame('2000', $c->created_at->format('Y'));
        $this->putJson("/api/categories/{$c->id}", ['name' => ''])->assertStatus(422);
        $this->putJson("/api/categories/{$c->id}", ['name' => 'Ubah'])->assertOk();
    }

    public function test_update_potensi_divalidasi_dan_tidak_bisa_set_kolom_terlarang(): void
    {
        $admin = $this->admin();
        Sanctum::actingAs($admin);
        $p = $this->potensi();

        $this->putJson("/api/potensi/{$p->id}", ['status' => 'hacked'])->assertStatus(422);
        $this->putJson("/api/potensi/{$p->id}", ['whatsapp' => 'abc'])->assertStatus(422);
        $this->putJson("/api/potensi/{$p->id}", ['category_id' => 9999])->assertStatus(422);
        $this->putJson("/api/potensi/{$p->id}", ['name' => 'Nama Baru', 'reviewed_by' => 999, 'id' => 500])->assertOk();

        $p = $p->fresh();
        $this->assertSame('Nama Baru', $p->name);
        $this->assertNotSame(999, $p->reviewed_by);
        $this->assertSame(1, Potensi::count());
    }

    public function test_update_potensi_menerima_payload_form_admin(): void
    {
        Sanctum::actingAs($this->admin());
        $cat = Category::create(['name' => 'Jasa']);
        $p = $this->potensi();
        $this->putJson("/api/potensi/{$p->id}", [
            'name' => 'Edit', 'owner_name' => 'Budi', 'category_id' => $cat->id, 'whatsapp' => '081234567890',
            'email' => null, 'address' => 'Jl. Melati No. 12', 'description' => 'Deskripsi cukup panjang sekali.',
            'location' => null, 'jenis' => null, 'jam_operasional' => null, 'status' => 'aktif',
        ])->assertOk();
    }

    // ---------- Upload ----------

    public function test_upload_menolak_file_berbahaya(): void
    {
        Sanctum::actingAs($this->admin());
        $this->postJson('/api/upload', ['image' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php')])->assertStatus(422);
        $this->postJson('/api/upload', ['image' => UploadedFile::fake()->create('a.html', 10, 'text/html')])->assertStatus(422);
        $this->postJson('/api/upload', ['image' => UploadedFile::fake()->image('besar.jpg')->size(5000)])->assertStatus(422);
        $this->postJson('/api/upload', [])->assertStatus(422);
    }

    public function test_upload_gambar_sah_dan_ekstensi_dari_isi_file(): void
    {
        Sanctum::actingAs($this->admin());
        // PNG sungguhan dengan nama .php -> ekstensi harus mengikuti isi file
        $file = UploadedFile::fake()->image('evil.php', 20, 20);
        $res = $this->postJson('/api/upload', ['image' => $file]);
        if ($res->status() === 200) {
            $this->assertStringNotContainsString('.php', $res->json('path'));
            @unlink(public_path($res->json('path')));
        } else {
            $res->assertStatus(422);
        }
        $ok = $this->postJson('/api/upload', ['image' => UploadedFile::fake()->image('foto.png', 20, 20)])->assertOk();
        $this->assertMatchesRegularExpression('#^/uploads/[A-Za-z0-9_\-]+\.png$#', $ok->json('path'));
        @unlink(public_path($ok->json('path')));
    }

    // ---------- Status & publikasi ----------

    public function test_pendaftaran_publik_default_pending(): void
    {
        $r = $this->postJson('/api/potensi', $this->payload(['status' => 'aktif']))->assertStatus(201);
        $this->assertSame('pending', $r->json('data.status'));
        $this->assertSame('pending', Potensi::first()->status);
    }

    public function test_daftar_publik_hanya_yang_disetujui(): void
    {
        $this->potensi('aktif', ['name' => 'Aktif Satu']);
        $this->potensi('approved', ['name' => 'Disetujui']);
        $this->potensi('pending', ['name' => 'Pending Satu']);
        $this->potensi('rejected', ['name' => 'Ditolak Satu']);

        $names = collect($this->getJson('/api/potensi')->assertOk()->json('data'))->pluck('name')->all();
        $this->assertEqualsCanonicalizing(['Aktif Satu', 'Disetujui'], $names);

        // guest tidak bisa memaksa filter status
        $names = collect($this->getJson('/api/potensi?status=pending')->json('data'))->pluck('name')->all();
        $this->assertNotContains('Pending Satu', $names);
    }

    public function test_detail_publik_tidak_menampilkan_yang_belum_disetujui(): void
    {
        $p = $this->potensi('pending');
        $a = $this->potensi('aktif');
        $this->getJson("/api/potensi/{$p->id}")->assertStatus(404);
        $this->getJson("/api/potensi/{$a->id}")->assertOk();
    }

    public function test_admin_melihat_semua_status_dan_bisa_filter(): void
    {
        $this->potensi('aktif');
        $this->potensi('pending');
        $this->potensi('rejected');
        $token = $this->admin()->createToken('t')->plainTextToken;
        $h = ['Authorization' => "Bearer $token"];

        $this->assertCount(3, $this->getJson('/api/potensi', $h)->json('data'));
        $this->assertCount(1, $this->getJson('/api/potensi?status=pending', $h)->json('data'));
        $this->getJson('/api/potensi/' . Potensi::where('status', 'pending')->value('id'), $h)->assertOk();
    }

    // ---------- Review & stats ----------

    public function test_review_mengisi_reviewed_by_dan_validasi(): void
    {
        $admin = $this->admin();
        Sanctum::actingAs($admin);
        $p = $this->potensi('pending');

        $this->postJson("/api/potensi/{$p->id}/review", ['status' => 'bogus'])->assertStatus(422);
        $this->postJson("/api/potensi/{$p->id}/review", ['status' => 'rejected'])->assertStatus(422); // alasan wajib
        $this->postJson("/api/potensi/{$p->id}/review", ['status' => 'rejected', 'reason' => 'Data tidak lengkap'])->assertOk();
        $p = $p->fresh();
        $this->assertSame('rejected', $p->status);
        $this->assertSame($admin->id, $p->reviewed_by);
        $this->assertNotNull($p->reviewed_at);

        $this->postJson("/api/potensi/{$p->id}/review", ['status' => 'approved'])->assertOk();
        $p = $p->fresh();
        $this->assertNull($p->rejection_reason);
        $this->postJson('/api/potensi/99999/review', ['status' => 'approved'])->assertStatus(404);
    }

    public function test_stats_pending_dihitung_benar(): void
    {
        Sanctum::actingAs($this->admin());
        $this->potensi('aktif');
        $this->potensi('approved');
        $this->potensi('pending');
        $this->potensi('pending');
        $this->potensi('rejected');

        $d = $this->getJson('/api/stats')->assertOk()->json('data');
        $this->assertSame(5, $d['totalPotensi']);
        $this->assertSame(2, $d['pendingReviews']);
    }

    // ---------- Throttle pendaftaran & pesan ----------

    public function test_pendaftaran_dan_pesan_dibatasi_throttle(): void
    {
        $s = null;
        for ($i = 0; $i < 15; $i++) {
            $s = $this->postJson('/api/potensi', $this->payload())->status();
        }
        $this->assertSame(429, $s);

        for ($i = 0; $i < 15; $i++) {
            $s = $this->postJson('/api/messages', ['name' => 'A', 'email' => 'a@b.id', 'message' => 'Halo'])->status();
        }
        $this->assertSame(429, $s);
    }

    public function test_pesan_divalidasi_panjangnya(): void
    {
        $this->postJson('/api/messages', ['name' => 'A', 'email' => 'bukan-email', 'message' => 'x'])->assertStatus(422);
        $this->postJson('/api/messages', ['name' => 'A', 'email' => 'a@b.id', 'message' => str_repeat('x', 6000)])->assertStatus(422);
    }

    // ---------- Profil / password ----------

    public function test_profil_dan_password_butuh_login(): void
    {
        $this->getJson('/api/admin/profile')->assertStatus(401);
        $this->putJson('/api/admin/password', [])->assertStatus(401);
        Sanctum::actingAs($this->admin());
        $this->getJson('/api/admin/profile')->assertOk()->assertJsonPath('data.email', 'admin@test.id');
    }

    public function test_ganti_password_alur_lengkap(): void
    {
        $u = $this->admin();
        Sanctum::actingAs($u);
        $this->putJson('/api/admin/password', ['current_password' => 'salah', 'password' => 'Baru12345', 'password_confirmation' => 'Baru12345'])->assertStatus(422);
        $this->putJson('/api/admin/password', ['current_password' => 'Rahasia123', 'password' => 'lemah', 'password_confirmation' => 'lemah'])->assertStatus(422);
        $this->putJson('/api/admin/password', ['current_password' => 'Rahasia123', 'password' => 'Baru12345', 'password_confirmation' => 'Baru12345'])->assertOk();
        $this->assertTrue(\Hash::check('Baru12345', $u->fresh()->password));
    }

    public function test_seeder_tidak_memakai_password_bawaan(): void
    {
        config(['admin.email' => 'seed@test.id', 'admin.password' => 'SeedPass123']);
        $this->seed();
        $u = User::where('email', 'seed@test.id')->first();
        $this->assertNotNull($u);
        $this->assertTrue(\Hash::check('SeedPass123', $u->password));
        $this->assertFalse(\Hash::check('admin123', $u->password));
        $this->assertNull(User::where('email', 'admin@pelemwatu.desa.id')->first());
    }
}
