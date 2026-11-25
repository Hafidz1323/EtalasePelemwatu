<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class TestSupabaseStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:test-supabase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menguji koneksi, unggah berkas, akses URL publik, dan penghapusan di Supabase Storage';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pengujian koneksi Supabase Storage...');

        // 1. Validasi keberadaan konfigurasi dasar
        $endpoint = config('filesystems.disks.supabase.endpoint');
        $bucket = config('filesystems.disks.supabase.bucket');
        $key = config('filesystems.disks.supabase.key');
        $secret = config('filesystems.disks.supabase.secret');

        if (empty($endpoint) || empty($bucket) || empty($key) || empty($secret)) {
            $this->error('GAGAL: Konfigurasi Supabase Storage belum lengkap.');
            $this->line('Pastikan variabel berikut telah diisi di .env:');
            $this->line('- SUPABASE_S3_ENDPOINT');
            $this->line('- SUPABASE_S3_BUCKET');
            $this->line('- SUPABASE_S3_KEY');
            $this->line('- SUPABASE_S3_SECRET');

            return self::FAILURE;
        }

        $disk = Storage::disk('supabase');
        $randomId = Str::lower(Str::random(12));
        $testFilename = 'test-connection-'.$randomId.'.txt';
        $testContent = 'Supabase Storage Test Connection for EtalasePelemwatu - '.now()->toIso8601String();

        // 2. Uji Unggah Berkas
        $this->line("1. Mengunggah file uji ({$testFilename})...");
        try {
            $uploaded = $disk->put($testFilename, $testContent, 'public');
            if (! $uploaded) {
                $this->error('GAGAL: Driver storage mengembalikan false saat mengunggah berkas.');

                return self::FAILURE;
            }
            $this->info('   -> Berhasil mengunggah file uji.');
        } catch (Throwable $e) {
            $this->error('GAGAL saat mengunggah berkas ke Supabase: '.$this->sanitizeErrorMessage($e->getMessage()));
            $this->line('Periksa kembali endpoint, bucket name, dan kredensial S3 Anda.');

            return self::FAILURE;
        }

        // 3. Uji Akses URL Publik
        $this->line('2. Memeriksa keterjangkauan URL publik...');
        $baseUrl = config('filesystems.disks.supabase.url') ?: env('SUPABASE_PUBLIC_URL');
        if (! empty($baseUrl)) {
            $publicUrl = rtrim($baseUrl, '/').'/'.$testFilename;
        } else {
            $publicUrl = $disk->url($testFilename);
        }

        if (! $publicUrl || ! filter_var($publicUrl, FILTER_VALIDATE_URL)) {
            $this->error('GAGAL: URL publik yang dihasilkan tidak valid: '.$publicUrl);
            $this->cleanup($testFilename);

            return self::FAILURE;
        }

        try {
            $response = Http::timeout(10)->get($publicUrl);
            if ($response->successful()) {
                $body = trim($response->body());
                if ($body === $testContent) {
                    $this->info('   -> URL publik berhasil diakses (HTTP 200) dan konten sesuai.');
                } else {
                    $this->warn('   -> URL publik merespons HTTP 200, namun konten berkas tidak sama persis.');
                }
            } else {
                $this->error("GAGAL: URL publik mengembalikan status HTTP {$response->status()}.");
                $this->line("URL: {$publicUrl}");
                $this->line('Penyebab umum:');
                $this->line('- Bucket di Supabase Storage belum diatur sebagai "Public".');
                $this->line('- SUPABASE_PUBLIC_URL belum sesuai dengan URL publik bucket.');
                $this->cleanup($testFilename);

                return self::FAILURE;
            }
        } catch (Throwable $e) {
            $this->error('GAGAL: Terjadi galat saat memverifikasi URL publik: '.$this->sanitizeErrorMessage($e->getMessage()));
            $this->cleanup($testFilename);

            return self::FAILURE;
        }

        // 4. Uji Penghapusan Berkas
        $this->line('3. Menghapus file uji dari bucket...');
        try {
            $deleted = $disk->delete($testFilename);
            if ($deleted) {
                $this->info('   -> File uji berhasil dihapus dari bucket.');
            } else {
                $this->warn('   -> File uji gagal dihapus otomatis (driver mengembalikan false).');
            }
        } catch (Throwable $e) {
            $this->warn('   -> File uji gagal dihapus: '.$this->sanitizeErrorMessage($e->getMessage()));
        }

        $this->newLine();
        $this->info('Hasil Uji Koneksi Supabase Storage: BERHASIL');

        return self::SUCCESS;
    }

    /**
     * Membersihkan file uji saat terjadi kegagalan.
     */
    private function cleanup(string $filename): void
    {
        try {
            Storage::disk('supabase')->delete($filename);
        } catch (Throwable) {
            // Abaikan kesalahan pembersihan saat penanganan error
        }
    }

    /**
     * Menyaring pesan galat untuk mencegah kebocoran kunci rahasia.
     */
    private function sanitizeErrorMessage(string $message): string
    {
        $secret = config('filesystems.disks.supabase.secret');
        $key = config('filesystems.disks.supabase.key');

        if (! empty($secret)) {
            $message = str_replace($secret, '***SECRET***', $message);
        }
        if (! empty($key)) {
            $message = str_replace($key, '***KEY***', $message);
        }

        return $message;
    }
}
