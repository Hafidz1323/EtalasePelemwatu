<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;
use ZipArchive;

class BackupCommandTest extends TestCase
{
    private string $backupDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->backupDir = storage_path('app/backups');
        if (! File::isDirectory($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true, true);
        }
    }

    protected function tearDown(): void
    {
        // Bersihkan file tes di folder backups
        $files = File::glob($this->backupDir.DIRECTORY_SEPARATOR.'backup-*.zip');
        foreach ($files as $file) {
            @unlink($file);
        }
        parent::tearDown();
    }

    public function test_backup_command_creates_valid_zip_archive(): void
    {
        $this->artisan('backup:run')->assertSuccessful();

        $archives = File::glob($this->backupDir.DIRECTORY_SEPARATOR.'backup-*.zip');
        $this->assertNotEmpty($archives);

        $latestZip = $archives[0];
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($latestZip));
        $this->assertGreaterThanOrEqual(1, $zip->numFiles);
        $zip->close();
    }

    public function test_backup_command_prunes_older_archives_beyond_limit_of_7(): void
    {
        // Buat 10 file backup dummy dengan timestamp berbeda
        for ($i = 1; $i <= 10; $i++) {
            $name = sprintf('backup-2026-01-%02d-000000.zip', $i);
            $path = $this->backupDir.DIRECTORY_SEPARATOR.$name;
            file_put_contents($path, 'dummy zip content '.$i);
            touch($path, time() - (100 - $i) * 60);
        }

        $beforeCount = count(File::glob($this->backupDir.DIRECTORY_SEPARATOR.'backup-*.zip'));
        $this->assertSame(10, $beforeCount);

        // Jalankan backup:run
        $this->artisan('backup:run')->assertSuccessful();

        // Jumlah backup yang tersisa harus tepat 7
        $afterArchives = File::glob($this->backupDir.DIRECTORY_SEPARATOR.'backup-*.zip');
        $this->assertCount(7, $afterArchives);
    }
}
