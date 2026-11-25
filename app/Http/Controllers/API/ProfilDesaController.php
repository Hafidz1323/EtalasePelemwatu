<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProfilDesaController extends Controller
{
    /**
     * Default nilai profil desa jika belum disimpan di database.
     */
    public const DEFAULTS = [
        'desa_alamat' => 'Jl. Raya Pelemwatu No. 128, Kecamatan Menganti, Kabupaten Gresik, Jawa Timur',
        'desa_kode_pos' => '',
        'desa_telepon' => '',
        'desa_email' => 'info@etalasepelemwatu.id',
        'desa_kepala' => '',
        'desa_jam_operasional' => 'Senin – Jumat, 08.00 – 16.00 WIB',
        'desa_whatsapp' => '6282245730256',
        'desa_instagram' => 'official_pelemwatu',
        'desa_jumlah_kk' => '',
        'desa_hero_judul' => "Selamat Datang di\nDesa Pelemwatu",
        'desa_hero_deskripsi' => 'Desa Pelemwatu berada di Kecamatan Menganti, Kabupaten Gresik. Warganya menjalankan usaha kuliner, laundry, kost, dan masih banyak lagi. Semuanya dikumpulkan di satu etalase supaya mudah ditemukan.',
        'desa_footer_deskripsi' => 'Platform digital untuk mengenal dan mempromosikan potensi lokal masyarakat Desa Pelemwatu, Kecamatan Menganti, Kabupaten Gresik, Jawa Timur.',
        'desa_tentang_p1' => 'adalah katalog digital resmi Desa Pelemwatu, Kecamatan Menganti, Kabupaten Gresik. Kami hadir untuk menjadi jembatan antara potensi lokal warga dengan pasar yang lebih luas — dari kuliner rumahan, kerajinan tangan, hingga jasa dan hunian sewa.',
        'desa_tentang_p2' => 'Setiap usaha yang terdaftar ditampilkan lengkap dengan foto, deskripsi, dan informasi kontak pemilik. Pengunjung dapat mencari dan menyaring berdasarkan kategori, sehingga potensi warga lebih mudah ditemukan oleh siapa saja.',
    ];

    /**
     * GET /api/admin/profil-desa
     * Mengambil konfigurasi profil desa (khusus admin).
     */
    public function show(Request $request)
    {
        $keys = array_keys(self::DEFAULTS);
        $settings = Setting::whereIn('key', $keys)->get()->keyBy('key');

        $data = [];
        $latest = null;
        foreach (self::DEFAULTS as $k => $def) {
            $row = $settings->get($k);
            $val = $row?->value;
            $data[$k] = ($val !== null && $val !== '') ? $val : $def;
            if ($row && $row->updated_at && ($latest === null || $row->updated_at->gt($latest))) {
                $latest = $row->updated_at;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'updated_at' => $latest?->toIso8601String(),
        ]);
    }

    /**
     * PUT /api/admin/profil-desa
     * Menyimpan pembaruan konfigurasi profil desa (khusus admin).
     */
    public function update(Request $request)
    {
        // Tolak input yang mengandung tag HTML / potensi XSS
        foreach ($request->only(array_keys(self::DEFAULTS)) as $field => $val) {
            if (is_string($val) && strip_tags($val) !== $val) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Input tidak boleh mengandung tag HTML.',
                    'errors' => [
                        $field => ['Input tidak boleh mengandung tag HTML.'],
                    ],
                ], 422);
            }
        }

        $request->validate([
            'desa_alamat' => ['nullable', 'string', 'max:255'],
            'desa_kode_pos' => ['nullable', 'regex:/^[0-9]{5}$/'],
            'desa_telepon' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]{1,20}$/'],
            'desa_whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]{1,20}$/'],
            'desa_email' => ['nullable', 'email', 'max:100'],
            'desa_kepala' => ['nullable', 'string', 'max:100'],
            'desa_jam_operasional' => ['nullable', 'string', 'max:100'],
            'desa_instagram' => ['nullable', 'string', 'max:100'],
            'desa_jumlah_kk' => ['nullable', 'integer', 'min:0'],
            'desa_hero_judul' => ['nullable', 'string', 'max:150'],
            'desa_hero_deskripsi' => ['nullable', 'string', 'max:500'],
            'desa_footer_deskripsi' => ['nullable', 'string', 'max:500'],
            'desa_tentang_p1' => ['nullable', 'string', 'max:1000'],
            'desa_tentang_p2' => ['nullable', 'string', 'max:1000'],
        ], [
            'desa_alamat.max' => 'Alamat maksimal 255 karakter.',
            'desa_kode_pos.regex' => 'Kode pos harus berupa 5 digit angka.',
            'desa_telepon.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, +, dan -.',
            'desa_telepon.max' => 'Nomor telepon maksimal 20 karakter.',
            'desa_whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, +, dan -.',
            'desa_whatsapp.max' => 'Nomor WhatsApp maksimal 20 karakter.',
            'desa_email.email' => 'Format email tidak valid.',
            'desa_email.max' => 'Email maksimal 100 karakter.',
            'desa_kepala.max' => 'Nama kepala desa maksimal 100 karakter.',
            'desa_jam_operasional.max' => 'Jam operasional maksimal 100 karakter.',
            'desa_instagram.max' => 'Akun Instagram maksimal 100 karakter.',
            'desa_jumlah_kk.integer' => 'Jumlah KK harus berupa angka bilangan bulat.',
            'desa_jumlah_kk.min' => 'Jumlah KK tidak boleh bernilai negatif.',
            'desa_hero_judul.max' => 'Judul hero maksimal 150 karakter.',
            'desa_hero_deskripsi.max' => 'Deskripsi hero maksimal 500 karakter.',
            'desa_footer_deskripsi.max' => 'Deskripsi footer maksimal 500 karakter.',
            'desa_tentang_p1.max' => 'Teks tentang etalase paragraf 1 maksimal 1000 karakter.',
            'desa_tentang_p2.max' => 'Teks tentang etalase paragraf 2 maksimal 1000 karakter.',
        ]);

        // Normalisasi WhatsApp: ubah 08xx atau +628xx menjadi 628xx
        $normalizedWa = null;
        if ($request->filled('desa_whatsapp')) {
            $rawWa = trim($request->input('desa_whatsapp'));
            $waDigits = preg_replace('/\D/', '', $rawWa);
            if ($waDigits !== '') {
                if (str_starts_with($waDigits, '0')) {
                    $normalizedWa = '62'.substr($waDigits, 1);
                } elseif (str_starts_with($waDigits, '62')) {
                    $normalizedWa = $waDigits;
                } else {
                    $normalizedWa = '62'.$waDigits;
                }
            }
        }

        // Normalisasi Instagram: ekstrak username dari @username atau URL instagram
        $normalizedIg = null;
        if ($request->filled('desa_instagram')) {
            $rawIg = trim($request->input('desa_instagram'));
            if (preg_match('/(?:https?:\/\/)?(?:www\.)?instagram\.com\/([A-Za-z0-9._]+)/i', $rawIg, $matches)) {
                $normalizedIg = $matches[1];
            } else {
                $normalizedIg = ltrim($rawIg, '@');
                $normalizedIg = trim(explode('?', $normalizedIg)[0], '/');
            }

            if (! preg_match('/^[A-Za-z0-9._]{1,30}$/', $normalizedIg)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format username Instagram tidak valid.',
                    'errors' => [
                        'desa_instagram' => ['Username Instagram hanya boleh berisi huruf, angka, titik, dan garis bawah, maksimal 30 karakter.'],
                    ],
                ], 422);
            }
        }

        $rawKk = $request->input('desa_jumlah_kk');
        $payload = [
            'desa_alamat' => $request->has('desa_alamat') ? trim($request->input('desa_alamat') ?? '') : null,
            'desa_kode_pos' => $request->has('desa_kode_pos') ? trim($request->input('desa_kode_pos') ?? '') : null,
            'desa_telepon' => $request->has('desa_telepon') ? trim($request->input('desa_telepon') ?? '') : null,
            'desa_email' => $request->has('desa_email') ? trim($request->input('desa_email') ?? '') : null,
            'desa_kepala' => $request->has('desa_kepala') ? trim($request->input('desa_kepala') ?? '') : null,
            'desa_jam_operasional' => $request->has('desa_jam_operasional') ? trim($request->input('desa_jam_operasional') ?? '') : null,
            'desa_whatsapp' => $normalizedWa,
            'desa_instagram' => $normalizedIg,
            'desa_jumlah_kk' => ($rawKk !== null && $rawKk !== '') ? (string) (int) $rawKk : null,
            'desa_hero_judul' => $request->has('desa_hero_judul') ? trim($request->input('desa_hero_judul') ?? '') : null,
            'desa_hero_deskripsi' => $request->has('desa_hero_deskripsi') ? trim($request->input('desa_hero_deskripsi') ?? '') : null,
            'desa_footer_deskripsi' => $request->has('desa_footer_deskripsi') ? trim($request->input('desa_footer_deskripsi') ?? '') : null,
            'desa_tentang_p1' => $request->has('desa_tentang_p1') ? trim($request->input('desa_tentang_p1') ?? '') : null,
            'desa_tentang_p2' => $request->has('desa_tentang_p2') ? trim($request->input('desa_tentang_p2') ?? '') : null,
        ];

        foreach ($payload as $key => $val) {
            if ($val === null || $val === '') {
                Setting::where('key', $key)->delete();
            } else {
                Setting::set($key, (string) $val);
            }
        }

        Cache::forget('profil_desa_settings');

        // Ambil data terbaru lengkap dengan fallback default
        $data = [];
        foreach (self::DEFAULTS as $key => $default) {
            $val = Setting::get($key);
            $data[$key] = ($val !== null && $val !== '') ? $val : $default;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Profil desa berhasil diperbarui.',
            'data' => $data,
            'updated_at' => now()->toIso8601String(),
        ]);
    }
}
