<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Upload foto yang aman dan dioptimalkan
     */
    public function upload(Request $request)
    {
        $request->validate([
<<<<<<< HEAD
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120', // Mendukung file hingga 5MB, otomatis dikompres/dioptimasi jika > 1MB
            ],
        ], [
            'image.required' => 'File gambar wajib diunggah.',
            'image.image'    => 'File yang diunggah harus berupa gambar yang valid.',
            'image.mimes'    => 'Format gambar yang diperbolehkan hanya JPG, JPEG, PNG, dan WebP.',
            'image.max'      => 'Ukuran gambar maksimal adalah 5MB.',
        ]);

        $file = $request->file('image');
        $ext = strtolower($file->getClientOriginalExtension());
        $safeName = 'img-' . date('Ymd-His') . '-' . Str::random(8) . '.' . $ext;

        $destinationPath = public_path('uploads');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $targetFile = $destinationPath . DIRECTORY_SEPARATOR . $safeName;

        // Optimasi & kompresi gambar menggunakan fungsi GD bawaan PHP jika tersedia
        $optimized = false;
        if (function_exists('imagecreatefromjpeg') && in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            try {
                $srcImg = null;
                if ($ext === 'jpg' || $ext === 'jpeg') {
                    $srcImg = @imagecreatefromjpeg($file->getRealPath());
                } elseif ($ext === 'png') {
                    $srcImg = @imagecreatefrompng($file->getRealPath());
                } elseif ($ext === 'webp' && function_exists('imagecreatefromwebp')) {
                    $srcImg = @imagecreatefromwebp($file->getRealPath());
                }

                if ($srcImg) {
                    $origWidth = imagesx($srcImg);
                    $origHeight = imagesy($srcImg);

                    // Resize jika resolusi foto terlalu besar (maks lebar 1600px untuk desktop tajam)
                    $maxWidth = 1600;
                    if ($origWidth > $maxWidth) {
                        $newWidth = $maxWidth;
                        $newHeight = (int)($origHeight * ($maxWidth / $origWidth));

                        $destImg = imagecreatetruecolor($newWidth, $newHeight);

                        // Pertahankan transparansi untuk PNG
                        if ($ext === 'png') {
                            imagealphablending($destImg, false);
                            imagesavealpha($destImg, true);
                        }

                        imagecopyresampled($destImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                        imagedestroy($srcImg);
                        $srcImg = $destImg;
                    }

                    // Simpan dengan kompresi optimal
                    if ($ext === 'jpg' || $ext === 'jpeg') {
                        imagejpeg($srcImg, $targetFile, 82); // Kompresi kualitas 82 (sangat hemat & jernih)
                    } elseif ($ext === 'png') {
                        imagepng($srcImg, $targetFile, 7);
                    } elseif ($ext === 'webp') {
                        imagewebp($srcImg, $targetFile, 80);
                    }

                    imagedestroy($srcImg);
                    $optimized = true;
                }
            } catch (\Throwable $e) {
                $optimized = false;
            }
        }

        // Fallback jika ekstensi GD tidak aktif
        if (!$optimized) {
            $file->move($destinationPath, $safeName);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Gambar berhasil diunggah dan dioptimalkan.',
            'path'    => '/uploads/' . $safeName,
=======
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'image.required' => 'Pilih file gambar terlebih dahulu.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $file = $request->file('image');
        // extension() ditebak dari ISI file, bukan dari nama yang dikirim pengguna.
        $filename = 'image-'.Str::lower(Str::random(24)).'.'.$file->extension();
        $file->move(public_path('uploads'), $filename);

        return response()->json([
            'status' => 'success',
            'message' => 'File uploaded',
            'path' => '/uploads/'.$filename,
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
        ]);
    }
}
