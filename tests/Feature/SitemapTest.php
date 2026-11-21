<?php

namespace Tests\Feature;

use Tests\TestCase;

class SitemapTest extends TestCase
{
    public function test_sitemap_xml_returns_valid_xml_structure_and_routes(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));

        $content = $response->getContent();
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $content);
        $this->assertStringContainsString('<loc>'.url('/').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.url('/daftar-potensi').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.url('/login').'</loc>', $content);
        $this->assertStringContainsString('<loc>'.url('/register').'</loc>', $content);
    }
}
