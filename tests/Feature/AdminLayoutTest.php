<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Tests\TestCase;

class AdminLayoutTest extends TestCase
{
    private function getAdminXPath(): DOMXPath
    {
        $path = resource_path('views/admin.blade.php');
        $this->assertFileExists($path);

        $content = file_get_contents($path);

        // Bersihkan tag script, style, komentar, dan blade directives untuk parsing DOM murni
        $clean = preg_replace('/<script\b[^>]*>[\s\S]*?<\/script>/i', '', $content);
        $clean = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $clean);
        $clean = preg_replace('/<!--[\s\S]*?-->/', '', $clean);
        $clean = preg_replace('/\{\{[\s\S]*?\}\}/', '', $clean);
        $clean = preg_replace('/\{!![\s\S]*?!!\}/', '', $clean);
        $clean = preg_replace('/@[a-zA-Z_]+\b[^\n]*/', '', $clean);

        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>'.$clean, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        return new DOMXPath($doc);
    }

    public function test_admin_content_is_direct_child_of_admin_main(): void
    {
        $xpath = $this->getAdminXPath();

        $adminMain = $xpath->query('//*[@id="adminMain"]');
        $this->assertSame(1, $adminMain->length, 'Elemen #adminMain harus ditemukan tepat satu.');

        $adminContent = $xpath->query('//*[@id="adminMain"]/div[contains(concat(" ", normalize-space(@class), " "), " admin-content ")]');
        $this->assertSame(1, $adminContent->length, 'Elemen .admin-content harus menjadi anak langsung dari #adminMain.');
    }

    public function test_all_admin_sections_are_direct_children_of_admin_content(): void
    {
        $xpath = $this->getAdminXPath();

        $expectedSections = [
            'section-dashboard',
            'section-kelola-potensi',
            'section-verifikasi',
            'section-permintaan-hapus',
            'section-galeri',
            'section-perangkat',
            'section-profil-desa-admin',
            'section-pengaturan',
        ];

        foreach ($expectedSections as $sectionId) {
            $directChildQuery = '//*[@id="adminMain"]/div[contains(concat(" ", normalize-space(@class), " "), " admin-content ")]/div[@id="'.$sectionId.'"]';
            $nodes = $xpath->query($directChildQuery);

            $this->assertSame(
                1,
                $nodes->length,
                "Section {$sectionId} harus menjadi anak langsung dari div.admin-content di dalam #adminMain."
            );

            /** @var \DOMElement $node */
            $node = $nodes->item(0);
            $class = $node->getAttribute('class');
            $this->assertStringContainsString('admin-section', $class, "Section {$sectionId} harus memiliki class 'admin-section'.");
        }
    }

    public function test_no_admin_section_is_placed_outside_admin_content(): void
    {
        $xpath = $this->getAdminXPath();

        // Cari semua elemen dengan class admin-section di luar div.admin-content
        $outsideSections = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " admin-section ")][not(parent::div[contains(concat(" ", normalize-space(@class), " "), " admin-content ")])]');

        $this->assertSame(
            0,
            $outsideSections->length,
            'Tidak boleh ada .admin-section yang berada di luar div.admin-content.'
        );
    }
}
