<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_html_urls_redirect_301_to_clean_urls(): void
    {
        $redirects = [
            '/index.html' => '/',
            '/admin.html' => '/admin',
            '/daftar-potensi.html' => '/daftar-potensi',
            '/login.html' => '/login',
            '/register.html' => '/register',
            '/profil-warga.html' => '/profil-warga',
        ];

        foreach ($redirects as $legacy => $expected) {
            $response = $this->get($legacy);
            $response->assertStatus(301);
            $response->assertRedirect($expected);
        }
    }

    public function test_clean_urls_render_views_successfully(): void
    {
        $cleanUrls = [
            '/' => 200,
            '/admin' => 200,
            '/daftar-potensi' => 200,
            '/login' => 200,
            '/register' => 200,
            '/profil-warga' => 200,
        ];

        foreach ($cleanUrls as $url => $status) {
            $this->get($url)->assertStatus($status);
        }
    }
}
