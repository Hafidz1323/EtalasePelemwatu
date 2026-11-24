<?php

namespace Tests\Feature;

use App\Models\Potensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WargaFeatureTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helper ───────────────────────────────────────────────
    private function makeWarga(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Warga Test',
            'phone' => '081234567890',
            'password' => Hash::make('password123'),
            'role' => 'warga',
        ], $overrides));
    }

    private function makePotensi(User $user, array $overrides = []): Potensi
    {
        return Potensi::create(array_merge([
            'user_id' => $user->id,
            'name' => 'Usaha Test '.uniqid(),
            'owner_name' => $user->name,
            'whatsapp' => $user->phone,
            'address' => 'Jl. Desa No. 1 Pelemwatu',
            'description' => 'Deskripsi usaha yang cukup panjang minimal dua puluh karakter',
            'status' => 'pending',
        ], $overrides));
    }

    // ─── Register ─────────────────────────────────────────────

    public function test_warga_bisa_daftar_akun(): void
    {
        $res = $this->postJson('/api/warga/register', [
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $res->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['token', 'user' => ['id', 'name', 'phone', 'role']]]);

        $this->assertDatabaseHas('users', ['phone' => '081234567890', 'role' => 'warga']);
    }

    public function test_register_memvalidasi_field_wajib(): void
    {
        $res = $this->postJson('/api/warga/register', []);
        $res->assertStatus(422)->assertJsonValidationErrors(['name', 'phone', 'password']);
    }

    public function test_register_nomor_hp_duplikat_ditolak(): void
    {
        $this->makeWarga(['phone' => '081234567890']);

        $res = $this->postJson('/api/warga/register', [
            'name' => 'Warga Lain',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $res->assertStatus(422)->assertJsonValidationErrors(['phone']);
    }

    public function test_register_password_min_8_karakter(): void
    {
        $res = $this->postJson('/api/warga/register', [
            'name' => 'Warga Test',
            'phone' => '081234567890',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $res->assertStatus(422)->assertJsonValidationErrors(['password']);
    }

    // ─── Login ────────────────────────────────────────────────

    public function test_warga_bisa_login_dengan_nomor_hp(): void
    {
        $this->makeWarga(['phone' => '081234567890']);

        $res = $this->postJson('/api/warga/login', [
            'login' => '081234567890',
            'password' => 'password123',
        ]);

        $res->assertOk()->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['token', 'user']]);
    }

    public function test_warga_bisa_login_dengan_email(): void
    {
        $this->makeWarga(['phone' => '081234567891', 'email' => 'budi@gmail.com']);

        $res = $this->postJson('/api/warga/login', [
            'login' => 'budi@gmail.com',
            'password' => 'password123',
        ]);

        $res->assertOk()->assertJsonPath('status', 'success');
    }

    public function test_login_password_salah_ditolak(): void
    {
        $this->makeWarga();

        $res = $this->postJson('/api/warga/login', [
            'login' => '081234567890',
            'password' => 'wrongpassword',
        ]);

        $res->assertStatus(401);
    }

    public function test_admin_tidak_bisa_login_melalui_endpoint_warga(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'phone' => '081234567892',
            'password' => Hash::make('adminpass'),
            'role' => 'admin',
        ]);

        $res = $this->postJson('/api/warga/login', [
            'login' => '081234567892',
            'password' => 'adminpass',
        ]);

        $res->assertStatus(401); // Role bukan warga
    }

    // ─── IDOR: warga tidak bisa akses data orang lain ─────────

    public function test_warga_hanya_melihat_potensi_miliknya(): void
    {
        $warga1 = $this->makeWarga(['phone' => '081111111111']);
        $warga2 = $this->makeWarga(['phone' => '082222222222']);

        $this->makePotensi($warga1, ['name' => 'Potensi Warga Satu']);
        $this->makePotensi($warga2, ['name' => 'Potensi Warga Dua']);

        $token = $warga1->createToken('t')->plainTextToken;
        $res = $this->withToken($token)->getJson('/api/warga/potensi');

        $res->assertOk();
        $names = collect($res->json('data'))->pluck('name');
        $this->assertContains('Potensi Warga Satu', $names);
        $this->assertNotContains('Potensi Warga Dua', $names);
    }

    public function test_warga_tidak_bisa_edit_potensi_orang_lain(): void
    {
        $warga1 = $this->makeWarga(['phone' => '081111111111']);
        $warga2 = $this->makeWarga(['phone' => '082222222222']);
        $p = $this->makePotensi($warga2);

        $token = $warga1->createToken('t')->plainTextToken;
        $res = $this->withToken($token)->postJson('/api/warga/potensi/'.$p->id, [
            'name' => 'Nama yang dicuri',
        ]);

        $res->assertStatus(404); // IDOR → terlihat sebagai not found
    }

    public function test_warga_tidak_bisa_hapus_potensi_orang_lain(): void
    {
        $warga1 = $this->makeWarga(['phone' => '081111111111']);
        $warga2 = $this->makeWarga(['phone' => '082222222222']);
        $p = $this->makePotensi($warga2);

        $token = $warga1->createToken('t')->plainTextToken;
        $res = $this->withToken($token)->deleteJson('/api/warga/potensi/'.$p->id);

        $res->assertStatus(404); // IDOR
        $this->assertDatabaseHas('potensi', ['id' => $p->id]);
    }

    // ─── Edit & Hapus milik sendiri ───────────────────────────

    public function test_warga_bisa_edit_potensi_pending_miliknya(): void
    {
        $warga = $this->makeWarga();
        $p = $this->makePotensi($warga, ['status' => 'pending']);

        $token = $warga->createToken('t')->plainTextToken;
        $res = $this->withToken($token)->postJson('/api/warga/potensi/'.$p->id, [
            'name' => 'Nama Baru',
            'description' => 'Deskripsi baru yang sudah diperbarui dan lebih dari dua puluh karakter',
            'whatsapp' => '081234567890',
            'address' => 'Jl. Baru No. 99 Pelemwatu',
        ]);

        $res->assertOk()->assertJsonPath('status', 'success');
        $this->assertDatabaseHas('potensi', ['id' => $p->id, 'name' => 'Nama Baru']);
    }

    public function test_edit_potensi_rejected_mengembalikan_status_pending(): void
    {
        $warga = $this->makeWarga();
        $p = $this->makePotensi($warga, ['status' => 'rejected', 'rejection_reason' => 'Tidak lengkap']);

        $token = $warga->createToken('t')->plainTextToken;
        $this->withToken($token)->postJson('/api/warga/potensi/'.$p->id, [
            'name' => 'Nama Sudah Diperbaiki',
            'description' => 'Deskripsi sudah dilengkapi dengan informasi yang lebih lengkap ya',
            'whatsapp' => '081234567890',
            'address' => 'Jl. Sudah Diperbaiki No. 1',
        ])->assertOk();

        $this->assertDatabaseHas('potensi', [
            'id' => $p->id,
            'status' => 'pending',
            'rejection_reason' => null,
        ]);
    }

    public function test_warga_tidak_bisa_edit_potensi_yang_sudah_approved(): void
    {
        $warga = $this->makeWarga();
        $p = $this->makePotensi($warga, ['status' => 'approved']);

        $token = $warga->createToken('t')->plainTextToken;
        $res = $this->withToken($token)->postJson('/api/warga/potensi/'.$p->id, [
            'name' => 'Coba Edit',
        ]);

        $res->assertStatus(422);
    }

    public function test_warga_bisa_hapus_potensi_pending_miliknya(): void
    {
        $warga = $this->makeWarga();
        $p = $this->makePotensi($warga, ['status' => 'pending']);

        $token = $warga->createToken('t')->plainTextToken;
        $this->withToken($token)->deleteJson('/api/warga/potensi/'.$p->id)->assertOk();

        $this->assertDatabaseMissing('potensi', ['id' => $p->id]);
    }

    public function test_warga_tidak_bisa_hapus_potensi_approved_miliknya(): void
    {
        $warga = $this->makeWarga();
        $p = $this->makePotensi($warga, ['status' => 'approved']);

        $token = $warga->createToken('t')->plainTextToken;
        $this->withToken($token)->deleteJson('/api/warga/potensi/'.$p->id)->assertStatus(422);
        $this->assertDatabaseHas('potensi', ['id' => $p->id]);
    }

    // ─── Proteksi endpoint admin dari warga ───────────────────

    public function test_warga_tidak_bisa_mengakses_endpoint_admin(): void
    {
        $warga = $this->makeWarga();
        $token = $warga->createToken('t')->plainTextToken;

        // Endpoint admin: hanya admin yang bisa
        $this->withToken($token)->getJson('/api/stats')->assertStatus(403);
        $this->withToken($token)->getJson('/api/admin/profile')->assertStatus(403);
        $this->withToken($token)->postJson('/api/upload', [])->assertStatus(403);
    }

    // ─── Profil warga ─────────────────────────────────────────

    public function test_warga_bisa_ambil_profilnya(): void
    {
        $warga = $this->makeWarga();
        $token = $warga->createToken('t')->plainTextToken;

        $this->withToken($token)->getJson('/api/warga/profile')
            ->assertOk()
            ->assertJsonPath('data.role', 'warga')
            ->assertJsonPath('data.phone', '081234567890');
    }

    public function test_warga_bisa_perbarui_profil(): void
    {
        $warga = $this->makeWarga();
        $token = $warga->createToken('t')->plainTextToken;

        $this->withToken($token)->postJson('/api/warga/profile', [
            'name' => 'Nama Baru Warga',
            'phone' => '089876543210',
        ])->assertOk()->assertJsonPath('data.name', 'Nama Baru Warga');

        $this->assertDatabaseHas('users', ['id' => $warga->id, 'name' => 'Nama Baru Warga']);
    }

    // ─── Potensi terhubung ke warga saat submit ───────────────

    public function test_potensi_terhubung_ke_user_id_saat_warga_login_submit(): void
    {
        $warga = $this->makeWarga();
        $token = $warga->createToken('t')->plainTextToken;

        $res = $this->withToken($token)->postJson('/api/potensi', [
            'name' => 'Usaha Warga Terhubung',
            'owner_name' => 'Warga Test',
            'whatsapp' => '081234567890',
            'address' => 'Jl. Desa No. 2 Pelemwatu',
            'description' => 'Deskripsi usaha warga yang terhubung dengan akun warga',
        ]);

        $res->assertCreated();
        $this->assertDatabaseHas('potensi', [
            'name' => 'Usaha Warga Terhubung',
            'user_id' => $warga->id,
        ]);
    }

    public function test_potensi_tanpa_login_tidak_punya_user_id(): void
    {
        $res = $this->postJson('/api/potensi', [
            'name' => 'Usaha Anonim',
            'owner_name' => 'Anonim',
            'whatsapp' => '081234567890',
            'address' => 'Jl. Anonim No. 1 Pelemwatu',
            'description' => 'Deskripsi usaha yang didaftarkan tanpa akun warga',
        ]);

        $res->assertCreated();
        $this->assertDatabaseHas('potensi', ['name' => 'Usaha Anonim', 'user_id' => null]);
    }

    // ─── Warga token tidak memberi akses admin ke GET /api/potensi ──

    public function test_warga_token_tidak_memberi_akses_data_pending_publik(): void
    {
        $warga = $this->makeWarga();
        Potensi::create([
            'name' => 'Potensi Pending Rahasia',
            'owner_name' => 'Orang Lain',
            'whatsapp' => '089999999999',
            'address' => 'Jl. Rahasia No. 99',
            'description' => 'Potensi ini masih pending dan seharusnya tidak tampil ke warga biasa',
            'status' => 'pending',
        ]);

        $token = $warga->createToken('t')->plainTextToken;
        $res = $this->withToken($token)->getJson('/api/potensi');

        $res->assertOk();
        $names = collect($res->json('data'))->pluck('name');
        $this->assertNotContains('Potensi Pending Rahasia', $names);
    }
}
