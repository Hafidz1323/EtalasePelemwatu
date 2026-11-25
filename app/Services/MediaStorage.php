<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaStorage
{
    /**
     * Memeriksa apakah penyimpanan aktif menggunakan Supabase Storage.
     */
    public static function isSupabase(): bool
    {
        return config('filesystems.default') === 'supabase'
            || env('FILESYSTEM_DISK') === 'supabase';
    }

    /**
     * Menyimpan konten file ke media storage (lokal atau supabase).
     * Selalu mengembalikan path berformat '/uploads/<filename>' untuk disimpan di database.
     */
    public static function put(string $content, string $filename): string
    {
        // Sanitasi nama file untuk mencegah path traversal
        $cleanFilename = basename(str_replace('\\', '/', $filename));

        if (self::isSupabase()) {
            Storage::disk('supabase')->put($cleanFilename, $content, 'public');
        } else {
            $dest = public_path('uploads');
            if (! is_dir($dest)) {
                mkdir($dest, 0755, true);
            }
            file_put_contents($dest.DIRECTORY_SEPARATOR.$cleanFilename, $content);
        }

        return '/uploads/'.$cleanFilename;
    }

    /**
     * Menyimpan berkas UploadedFile atau path berkas ke media storage.
     */
    public static function putFile(UploadedFile|string $file, string $filename): string
    {
        $realPath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $content = file_get_contents($realPath);

        return self::put($content, $filename);
    }

    /**
     * Menghapus file media.
     * Hanya boleh menghapus path berawalan '/uploads/', menolak '..' dan path absolut,
     * serta tidak pernah menyentuh direktori 'assets/'.
     */
    public static function delete(?string $path): bool
    {
        if (! $path || ! is_string($path)) {
            return false;
        }

        // Tolak jika mengandung path traversal '..'
        if (str_contains($path, '..')) {
            return false;
        }

        // Tidak pernah menyentuh berkas di assets/
        if (str_starts_with($path, 'assets/') || str_starts_with($path, '/assets/')) {
            return false;
        }

        // Tolak path absolut sistem operasi Windows (mis. C:\ atau D:/)
        if (preg_match('/^[a-zA-Z]:[\\\\\/]/', $path)) {
            return false;
        }

        // Wajib berawalan '/uploads/' atau 'uploads/'
        $normalized = '/'.ltrim($path, '/\\');
        if (! str_starts_with($normalized, '/uploads/')) {
            return false;
        }

        $subPath = substr($normalized, strlen('/uploads/'));
        $subPath = ltrim($subPath, '/\\');
        if ($subPath === '') {
            return false;
        }

        if (self::isSupabase()) {
            $key = str_replace('\\', '/', $subPath);

            return Storage::disk('supabase')->delete($key);
        }

        $fullPath = public_path('uploads'.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $subPath));
        if (file_exists($fullPath) && is_file($fullPath)) {
            return @unlink($fullPath);
        }

        return false;
    }

    /**
     * Menyusun URL publik untuk gambar.
     * - Jika path sudah berupa URL http(s), kembalikan apa adanya.
     * - Jika path berada di 'assets/', kembalikan apa adanya.
     * - Di mode supabase, susun URL publik penuh dari SUPABASE_PUBLIC_URL.
     * - Di mode lokal, kembalikan path relatif/lokal apa adanya.
     */
    public static function url(?string $path): ?string
    {
        if (! $path || ! is_string($path)) {
            return null;
        }

        // Jika sudah berupa URL lengkap http(s)
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Aset statis bawaan di assets/
        if (str_starts_with($path, 'assets/') || str_starts_with($path, '/assets/')) {
            return $path;
        }

        // Mode Supabase
        if (self::isSupabase()) {
            $cleanKey = ltrim($path, '/\\');
            if (str_starts_with($cleanKey, 'uploads/')) {
                $cleanKey = substr($cleanKey, strlen('uploads/'));
            }
            $cleanKey = ltrim($cleanKey, '/\\');

            $publicUrl = config('filesystems.disks.supabase.url') ?: env('SUPABASE_PUBLIC_URL');
            if ($publicUrl) {
                return rtrim($publicUrl, '/').'/'.str_replace('\\', '/', $cleanKey);
            }

            return Storage::disk('supabase')->url(str_replace('\\', '/', $cleanKey));
        }

        // Mode lokal
        return $path;
    }
}
