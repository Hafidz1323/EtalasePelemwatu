<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MigrateMediaToSupabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:migrate-to-supabase {--dry-run : Lakukan simulasi tanpa benar-benar mengunggah berkas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menyalin berkas media lokal dari public/uploads ke bucket Supabase Storage (idempoten)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $uploadsDir = public_path('uploads');

        if (! File::isDirectory($uploadsDir)) {
            $this->info('Direktori public/uploads tidak ditemukan.');

            return self::SUCCESS;
        }

        $allFiles = File::files($uploadsDir);
        $mediaFiles = [];

        foreach ($allFiles as $file) {
            $filename = $file->getFilename();

            // Abaikan berkas tersembunyi dan sistem
            if (str_starts_with($filename, '.') || in_array($filename, ['.gitkeep', '.gitignore', '.htaccess', 'web.config'], true)) {
                continue;
            }

            $mediaFiles[] = $file;
        }

        $total = count($mediaFiles);
        if ($total === 0) {
            $this->info('Tidak ada berkas media di public/uploads yang perlu dimigrasi.');

            return self::SUCCESS;
        }

        if ($isDryRun) {
            $this->warn('--- MODE DRY-RUN DIAKTIFKAN: Tidak ada berkas yang diunggah ke Supabase ---');
        }

        $this->info("Menemukan {$total} berkas media di public/uploads.");

        $disk = Storage::disk('supabase');
        $uploaded = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($mediaFiles as $file) {
            $filename = $file->getFilename();

            try {
                // Cek apakah berkas sudah ada di bucket (idempoten)
                $alreadyExists = false;
                try {
                    $alreadyExists = $disk->exists($filename);
                } catch (Throwable) {
                    $alreadyExists = false;
                }

                if ($alreadyExists) {
                    $skipped++;
                    $this->line(" [LEWAT] {$filename} (sudah ada di bucket)");

                    continue;
                }

                if ($isDryRun) {
                    $uploaded++;
                    $this->line(" [DRY-RUN] Akan diunggah: {$filename}");
                } else {
                    $content = file_get_contents($file->getRealPath());
                    $success = $disk->put($filename, $content, 'public');

                    if ($success) {
                        $uploaded++;
                        $this->info(" [BERHASIL] Diunggah: {$filename}");
                    } else {
                        $failed++;
                        $this->error(" [GAGAL] Gagal mengunggah: {$filename}");
                    }
                }
            } catch (Throwable $e) {
                $failed++;
                $this->error(" [ERROR] {$filename}: ".$e->getMessage());
            }
        }

        $this->newLine();
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Berkas Ditemukan', $total],
                [$isDryRun ? 'Akan Diunggah (Simulasi)' : 'Berhasil Diunggah', $uploaded],
                ['Dilewati (Sudah Ada)', $skipped],
                ['Gagal', $failed],
            ]
        );

        if ($failed > 0) {
            $this->warn('Migrasi selesai dengan beberapa galat. Silakan periksa koneksi atau kredensial Supabase Storage.');

            return self::FAILURE;
        }

        $this->info($isDryRun ? 'Simulasi migrasi selesai tanpa galat.' : 'Migrasi media ke Supabase Storage berhasil.');

        return self::SUCCESS;
    }
}
