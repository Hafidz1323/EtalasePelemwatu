<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    /** Default logo — jangan pernah dihapus */
    private const DEFAULT_LOGO = '/assets/img/logo-desa.png';

    /**
     * GET /api/settings/logo  (publik)
     */
    public function getLogo()
    {
        $path = Setting::get('site_logo', self::DEFAULT_LOGO);

        // Tambahkan query ?v= agar browser tidak memakai cache logo lama
        if ($path !== self::DEFAULT_LOGO) {
            $row = Setting::where('key', 'site_logo')->first();
            if ($row) {
                $path .= '?v='.$row->updated_at->timestamp;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => ['path' => $path],
        ]);
    }

    /**
     * POST /api/settings/logo  (auth:sanctum)
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ]);

        // Simpan file baru ke public/uploads/
        // Gunakan ekstensi dari ISI file (bukan nama klien) + nama acak
        $file = $request->file('logo');
        $filename = 'logo-'.Str::lower(Str::random(24)).'.'.$file->extension();
        $file->move(public_path('uploads'), $filename);
        $newPath = '/uploads/'.$filename;

        // Hapus file logo lama (hanya jika bukan logo bawaan); validasi path berada di dalam public/uploads
        $oldPath = Setting::get('site_logo');
        if ($oldPath && $oldPath !== self::DEFAULT_LOGO) {
            $oldAbsolute = public_path(ltrim($oldPath, '/'));
            $uploadsDir = realpath(public_path('uploads'));
            $realOld = file_exists($oldAbsolute) ? realpath($oldAbsolute) : null;
            if ($realOld && $uploadsDir && str_starts_with($realOld, $uploadsDir.DIRECTORY_SEPARATOR)) {
                @unlink($realOld);
            }
        }

        // Simpan path baru ke settings
        Setting::set('site_logo', $newPath);

        return response()->json([
            'status' => 'success',
            'message' => 'Logo berhasil diperbarui.',
            'data' => ['path' => $newPath],
        ]);
    }
}
