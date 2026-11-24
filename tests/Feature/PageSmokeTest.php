<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_semua_halaman_web_terbuka(): void
    {
        foreach (['/', '/index.html', '/login', '/login.html', '/admin', '/admin.html', '/daftar-potensi', '/daftar-potensi.html'] as $url) {
            $this->followingRedirects()->get($url)->assertOk();
        }
    }

    public function test_halaman_tidak_ada_404(): void
    {
        $this->get('/tidak-ada')->assertNotFound();
    }
}
