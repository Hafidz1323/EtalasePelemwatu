<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /**
     * Memproses dan menyimpan gambar:
     * - Resize sisi terpanjang maksimal 1600px (mempertahankan rasio aspek).
     * - Konversi ke format WebP kualitas 80 menggunakan GD bila didukung.
     * - Fallback: menyimpan format asli jika GD / WebP tidak tersedia atau gagal.
     * - Penyimpanan fisik diserahkan ke MediaStorage (mendukung lokal dan Supabase).
     *
     * @return string Nama file di dalam media storage
     */
    public static function processAndSave(UploadedFile $file, string $prefix = ''): string
    {
        $random = Str::lower(Str::random(24));
        $prefix = $prefix ? rtrim($prefix, '-').'-' : '';

        // Cek ketersediaan ekstensi GD
        if (extension_loaded('gd') && function_exists('imagewebp') && function_exists('imagecreatetruecolor')) {
            $realPath = $file->getRealPath();
            $imageInfo = @getimagesize($realPath);

            if ($imageInfo !== false) {
                $srcWidth = $imageInfo[0];
                $srcHeight = $imageInfo[1];
                $mime = $imageInfo['mime'] ?? '';

                $srcImage = null;
                switch ($mime) {
                    case 'image/jpeg':
                        $srcImage = @imagecreatefromjpeg($realPath);
                        break;
                    case 'image/png':
                        $srcImage = @imagecreatefrompng($realPath);
                        break;
                    case 'image/webp':
                        if (function_exists('imagecreatefromwebp')) {
                            $srcImage = @imagecreatefromwebp($realPath);
                        }
                        break;
                }

                if ($srcImage) {
                    // Hitung dimensi baru jika melebihi 1600px
                    $maxEdge = 1600;
                    $targetWidth = $srcWidth;
                    $targetHeight = $srcHeight;

                    if ($srcWidth > $maxEdge || $srcHeight > $maxEdge) {
                        if ($srcWidth >= $srcHeight) {
                            $targetWidth = $maxEdge;
                            $targetHeight = (int) round(($srcHeight / $srcWidth) * $maxEdge);
                        } else {
                            $targetHeight = $maxEdge;
                            $targetWidth = (int) round(($srcWidth / $srcHeight) * $maxEdge);
                        }
                    }

                    $dstImage = imagecreatetruecolor($targetWidth, $targetHeight);

                    // Jaga transparansi untuk PNG & WebP
                    imagealphablending($dstImage, false);
                    imagesavealpha($dstImage, true);
                    $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
                    imagefilledrectangle($dstImage, 0, 0, $targetWidth, $targetHeight, $transparent);

                    imagecopyresampled(
                        $dstImage,
                        $srcImage,
                        0, 0, 0, 0,
                        $targetWidth,
                        $targetHeight,
                        $srcWidth,
                        $srcHeight
                    );

                    $filename = $prefix.$random.'.webp';
                    $tempPath = tempnam(sys_get_temp_dir(), 'opt_');

                    if ($tempPath && @imagewebp($dstImage, $tempPath, 80)) {
                        imagedestroy($srcImage);
                        imagedestroy($dstImage);

                        $content = file_get_contents($tempPath);
                        @unlink($tempPath);

                        MediaStorage::put($content, $filename);

                        return $filename;
                    }

                    if ($tempPath && file_exists($tempPath)) {
                        @unlink($tempPath);
                    }

                    imagedestroy($srcImage);
                    imagedestroy($dstImage);
                }
            }
        }

        // Fallback jika GD tidak tersedia atau gagal memproses
        $ext = $file->extension() ?: 'jpg';
        $filename = $prefix.$random.'.'.$ext;
        $content = file_get_contents($file->getRealPath());
        MediaStorage::put($content, $filename);

        return $filename;
    }
}
