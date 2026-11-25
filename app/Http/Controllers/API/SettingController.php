<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\MediaStorage;
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

        $url = MediaStorage::url($path);

        // Tambahkan query ?v= agar browser tidak memakai cache logo lama
        if ($path !== self::DEFAULT_LOGO) {
            $row = Setting::where('key', 'site_logo')->first();
            if ($row) {
                $url .= '?v='.$row->updated_at->timestamp;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => ['path' => $url],
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

        // Simpan file baru via MediaStorage (lokal atau supabase)
        // Gunakan ekstensi dari ISI file (bukan nama klien) + nama acak
        $file = $request->file('logo');
        $filename = 'logo-'.Str::lower(Str::random(24)).'.'.$file->extension();
        $newPath = MediaStorage::putFile($file, $filename);

        // Hapus file logo lama (hanya jika bukan logo bawaan); MediaStorage melindungi berkas di assets/
        $oldPath = Setting::get('site_logo');
        if ($oldPath && $oldPath !== self::DEFAULT_LOGO) {
            MediaStorage::delete($oldPath);
        }

        // Simpan path baru ke settings
        Setting::set('site_logo', $newPath);

        return response()->json([
            'status' => 'success',
            'message' => 'Logo berhasil diperbarui.',
            'data' => ['path' => MediaStorage::url($newPath)],
        ]);
    }
}
