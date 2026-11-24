<?php

namespace Tests\Feature;

use App\Services\MysqlDumpRunner;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
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

    public function test_mysql_backup_fails_and_cleans_up_zip_when_binary_invalid(): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'pelemwatu_db',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => 'secret',
            'backup.mysqldump_path' => '/nonexistent/path/to/mysqldump',
        ]);

        $this->artisan('backup:run')
            ->expectsOutputToContain('Gagal')
            ->expectsOutputToContain('BACKUP_MYSQLDUMP_PATH')
            ->assertFailed();

        // Memastikan file ZIP yang tidak lengkap telah dihapus
        $archives = File::glob($this->backupDir.DIRECTORY_SEPARATOR.'backup-*.zip');
        $this->assertEmpty($archives, 'Arsip ZIP parsial harus dihapus jika backup gagal.');
    }

    public function test_mysql_dump_invokes_process_with_array_args_and_password_in_env(): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'pelemwatu_production',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.username' => 'db_user',
            'database.connections.mysql.password' => 'super_secret_pwd',
            'backup.mysqldump_path' => 'mysqldump',
        ]);

        $mockRunner = $this->createMock(MysqlDumpRunner::class);
        $mockRunner->expects($this->once())
            ->method('run')
            ->with(
                $this->callback(function (array $command) {
                    $hasBinary = $command[0] === 'mysqldump';
                    $hasHost = in_array('--host=127.0.0.1', $command);
                    $hasPort = in_array('--port=3306', $command);
                    $hasUser = in_array('--user=db_user', $command);
                    $hasSingleTx = in_array('--single-transaction', $command);
                    $hasRoutines = in_array('--routines', $command);
                    $hasNoTablespaces = in_array('--no-tablespaces', $command);
                    $hasDatabase = in_array('pelemwatu_production', $command);
                    $noPasswordArg = ! collect($command)->contains(fn ($arg) => str_starts_with($arg, '--password'));

                    return $hasBinary && $hasHost && $hasPort && $hasUser && $hasSingleTx && $hasRoutines && $hasNoTablespaces && $hasDatabase && $noPasswordArg;
                }),
                $this->callback(function (array $env) {
                    return isset($env['MYSQL_PWD']) && $env['MYSQL_PWD'] === 'super_secret_pwd';
                })
            )
            ->willReturnCallback(function () {
                $mockProc = $this->createMock(Process::class);
                $mockProc->method('isSuccessful')->willReturn(true);
                $mockProc->method('getOutput')->willReturn("-- MySQL dump test snapshot\nCREATE TABLE potensi_test (id INT);");

                return $mockProc;
            });

        $this->app->instance(MysqlDumpRunner::class, $mockRunner);

        $this->artisan('backup:run')->assertSuccessful();

        $archives = File::glob($this->backupDir.DIRECTORY_SEPARATOR.'backup-*.zip');
        $this->assertNotEmpty($archives);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($archives[0]));
        $dumpContent = $zip->getFromName('database/dump.sql');
        $this->assertIsString($dumpContent);
        $this->assertStringContainsString('-- MySQL dump test snapshot', $dumpContent);
        $zip->close();
    }

    public function test_unsupported_database_driver_fails_and_leaves_no_zip(): void
    {
        config(['database.default' => 'pgsql']);

        $this->artisan('backup:run')
            ->expectsOutputToContain("Driver database 'pgsql' belum didukung")
            ->assertFailed();

        $archives = File::glob($this->backupDir.DIRECTORY_SEPARATOR.'backup-*.zip');
        $this->assertEmpty($archives, 'Arsip ZIP tidak boleh dibuat untuk driver yang tidak didukung.');
    }
}
