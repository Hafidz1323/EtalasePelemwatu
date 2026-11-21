<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SecurityPhase1Test extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(string $email = 'admin@pelemwatu.id'): User
    {
        return User::create([
            'name' => 'Admin Test',
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
    }

    private function makeWarga(string $email = 'warga@pelemwatu.id', string $phone = '081234567890'): User
    {
        return User::create([
            'name' => 'Warga Test',
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make('password123'),
            'role' => 'warga',
        ]);
    }

    public function test_user_tanpa_role_defaultnya_adalah_warga(): void
    {
        $user = User::create([
            'name' => 'Budi Warga',
            'email' => 'budi@test.id',
            'password' => Hash::make('password123'),
        ]);

        $this->assertEquals('warga', $user->fresh()->role);
    }

    public function test_database_seeder_menghasilkan_admin_dengan_role_admin(): void
    {
        User::query()->delete();

        $this->seed(DatabaseSeeder::class);

        $adminEmail = strtolower(config('admin.email'));
        $admin = User::where('email', $adminEmail)->first();

        $this->assertNotNull($admin);
        $this->assertEquals('admin', $admin->role);
    }

    public function test_ensure_role_middleware_blocks_unauthenticated_and_wrong_role(): void
    {
        // 1. Akses admin tanpa login -> 401
        $this->getJson('/api/admin/profile')->assertStatus(401);

        // 2. Akses warga tanpa login -> 401
        $this->getJson('/api/warga/profile')->assertStatus(401);

        // 3. Akses admin dengan token warga -> 403 Akses ditolak
        Sanctum::actingAs($this->makeWarga());
        $this->getJson('/api/admin/profile')
            ->assertStatus(403)
            ->assertJson(['status' => 'error', 'message' => 'Akses ditolak.']);

        // 4. Akses warga dengan token admin -> 403 Akses ditolak
        Sanctum::actingAs($this->makeAdmin('admin2@test.id'));
        $this->getJson('/api/warga/profile')
            ->assertStatus(403)
            ->assertJson(['status' => 'error', 'message' => 'Akses ditolak.']);

        // 5. Akses admin dengan token admin -> 200
        Sanctum::actingAs($this->makeAdmin());
        $this->getJson('/api/admin/profile')->assertStatus(200);

        // 6. Akses warga dengan token warga -> 200
        Sanctum::actingAs($this->makeWarga('warga2@test.id', '081234567891'));
        $this->getJson('/api/warga/profile')->assertStatus(200);
    }

    public function test_warga_tidak_bisa_upload_logo_dan_site_logo_tidak_berubah(): void
    {
        Setting::set('site_logo', '/assets/img/logo-desa.png');

        Sanctum::actingAs($this->makeWarga());

        $file = UploadedFile::fake()->image('fake-logo.png', 100, 100);

        $res = $this->postJson('/api/settings/logo', [
            'logo' => $file,
        ]);

        $res->assertStatus(403)
            ->assertJson(['status' => 'error', 'message' => 'Akses ditolak.']);

        $this->assertEquals('/assets/img/logo-desa.png', Setting::get('site_logo'));
    }

    public function test_uploads_htaccess_dan_gitkeep_ada_dan_valid(): void
    {
        $htaccessPath = public_path('uploads/.htaccess');
        $gitkeepPath = public_path('uploads/.gitkeep');

        $this->assertFileExists($htaccessPath);
        $this->assertFileExists($gitkeepPath);

        $content = file_get_contents($htaccessPath);
        $this->assertStringContainsString('Options -Indexes -ExecCGI', $content);
        $this->assertStringContainsString('RemoveHandler', $content);
        $this->assertStringContainsString('jpg|jpeg|png|webp', $content);
    }

    public function test_rate_limiter_warga_login(): void
    {
        $userA = $this->makeWarga('warga_a@test.id', '089911111111');
        $userB = $this->makeWarga('warga_b@test.id', '089922222222');

        // 5 percobaan salah untuk user A
        for ($i = 0; $i < 5; $i++) {
            $res = $this->postJson('/api/warga/login', [
                'login' => $userA->email,
                'password' => 'password_salah',
            ]);
            $res->assertStatus(401);
        }

        // Percobaan ke-6 untuk user A -> 429
        $res6 = $this->postJson('/api/warga/login', [
            'login' => $userA->email,
            'password' => 'password_salah',
        ]);
        $res6->assertStatus(429)
            ->assertJson([
                'status' => 'error',
                'message' => 'Terlalu banyak percobaan login. Coba lagi nanti.',
            ]);

        // Percobaan untuk user B dari IP yang sama tidak terblokir
        $resB = $this->postJson('/api/warga/login', [
            'login' => $userB->email,
            'password' => 'password_salah',
        ]);
        $resB->assertStatus(401);
    }

    public function test_token_sanctum_dibatasi_maksimal_5_per_user(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);

        $admin = $this->makeAdmin('admin_tokens@test.id');

        // Login 6 kali berturut-turut
        for ($i = 1; $i <= 6; $i++) {
            $res = $this->postJson('/api/login', [
                'email' => $admin->email,
                'password' => 'password123',
            ]);
            $res->assertStatus(200);
        }

        $this->assertEquals(5, $admin->tokens()->count());

        // Uji untuk warga
        $warga = $this->makeWarga('warga_tokens@test.id', '089933333333');
        for ($i = 1; $i <= 6; $i++) {
            $res = $this->postJson('/api/warga/login', [
                'login' => $warga->phone,
                'password' => 'password123',
            ]);
            $res->assertStatus(200);
        }

        $this->assertEquals(5, $warga->tokens()->count());
    }

    public function test_artisan_command_admin_reset_password(): void
    {
        $admin = $this->makeAdmin('admin_reset@test.id');
        $admin->createToken('test-token');

        $this->assertEquals(1, $admin->tokens()->count());

        $this->artisan('admin:reset-password', ['email' => $admin->email])
            ->expectsOutputToContain('berhasil direset')
            ->expectsOutputToContain('Password baru:')
            ->assertExitCode(0);

        // Token lama harus terhapus
        $this->assertEquals(0, $admin->tokens()->count());

        // Coba user yang bukan admin
        $warga = $this->makeWarga('warga_bukan_admin@test.id', '089944444444');
        $this->artisan('admin:reset-password', ['email' => $warga->email])
            ->expectsOutputToContain('bukan akun admin')
            ->assertExitCode(1);

        // Coba user yang tidak terdaftar
        $this->artisan('admin:reset-password', ['email' => 'tidak_ada@test.id'])
            ->expectsOutputToContain('tidak ditemukan')
            ->assertExitCode(1);
    }
}
