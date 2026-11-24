# Analisis Migrasi Penyimpanan Token ke Cookie HttpOnly (Sanctum SPA)

Dokumen ini menganalisis rencana arsitektur dan dampak teknis jika aplikasi **EtalasePelemwatu** bermigrasi dari skema autentikasi berbasis token Bearer di `localStorage` / `sessionStorage` ke cookie sesi `HttpOnly` berbasis Laravel Sanctum SPA.

---

## 1. Latar Belakang & Kondisi Saat Ini

Saat ini, autentikasi admin dan warga menggunakan token Sanctum yang disimpan di sisi browser klien:
- **Admin**: Disimpan di `localStorage` atau `sessionStorage` dengan key `'ep_admin_session'`, `'adminLoggedIn'`, dan `'adminUser'`.
- **Warga**: Disimpan di `localStorage` dengan key `'ep_warga_token'` dan `'ep_warga_user'`.
- Setiap panggilan AJAX/Fetch menyertakan header `Authorization: Bearer <token>`.

### Kelebihan Saat Ini:
- Sederhana diimplementasikan untuk arsitektur static/SPA.
- Tidak bergantung pada domain cookie dan konfigurasi CORS/SameSite.

### Kerentanan / Kelemahan:
- **Risiko XSS**: Jika terjadi celah Cross-Site Scripting (XSS), script jahat dapat membaca `localStorage`/`sessionStorage` dan mencuri token autentikasi.

---

## 2. Gambaran Solusi: Sanctum SPA (Cookie HttpOnly + CSRF)

Dengan pendekatan Sanctum SPA:
1. Klien pertama-tama meminta cookie CSRF melalui `GET /sanctum/csrf-cookie`.
2. Login dilakukan via `POST /login` (admin) atau `POST /warga/login` (warga) menggunakan session driver bawaan Laravel.
3. Server membalas dengan cookie session berflag `HttpOnly`, `SameSite=Lax` (atau `Strict`), dan `Secure` (pada HTTPS). JavaScript tidak dapat mengakses cookie ini.
4. Setiap request berikutnya otomatis membawa cookie sesi via browser (`credentials: 'same-origin'` atau `'include'`) beserta header `X-XSRF-TOKEN` untuk proteksi CSRF.

---

## 3. File yang Terdampak

### Frontend:
- `public/assets/js/auth.js`:
  - Mengubah fungsi helper `getAdminToken()` dan penyimpanan sesi.
  - Menambahkan request awal ke `/sanctum/csrf-cookie`.
  - Mengubah opsi `fetch` untuk menyertakan `credentials: 'same-origin'` dan header `X-XSRF-TOKEN`.
- `public/assets/js/warga-auth.js`:
  - Mengubah logika penyimpanan token warga (`ep_warga_token`).
  - Menyesuaikan fungsi verifikasi sesi (`/api/warga/verify`).
- `resources/views/admin.blade.php`:
  - Skrip inline `<script>` di `<head>` yang memeriksa `localStorage.getItem('adminLoggedIn')` dan `verify` harus disesuaikan untuk mengandalkan handshake session server-side atau verifikasi endpoint berbasis cookie.
- `resources/views/login.blade.php`:
  - Mengganti penyimpanan `localStorage.setItem('ep_admin_session', ...)` dengan redirect langsung setelah session dibuat.
- `resources/views/profil-warga.blade.php`:
  - Menghapus pembacaan token Bearer; memanggil API profil dengan cookie credentials.

### Backend:
- `config/sanctum.php`:
  - Menentukan `stateful` domains (misal `localhost`, `127.0.0.1`, `pelemwatu.desa.id`).
- `bootstrap/app.php` / Kernel HTTP:
  - Memastikan middleware `EnsureFrontendRequestsAreStateful` aktif untuk grup rute `api`.
- `app/Http/Controllers/API/AuthController.php` & `WargaAuthController.php`:
  - Menambahkan dukungan untuk `Auth::login($user)` berbasis web session selain pembuatan token API.
- `routes/api.php`:
  - Memastikan rute terautentikasi mendukung middleware sesi web/Sanctum stateful.

---

## 4. Risiko Regresi

1. **Pengembangan Lokal & Cross-Origin**:
   - Jika pengembang membuka via Live Server (port 5500), Vite (port 5173), atau IP LAN, cookie browser akan diblokir karena beda origin atau domain tanpa konfigurasi CORS + Sanctum domain yang tepat.
2. **Session Expiry & Inactivity**:
   - Cookie session memiliki batas waktu inaktivitas (default 120 menit). User admin yang sedang mengisi formulir panjang bisa mengalami `419 Page Expired` jika CSRF token kedaluwarsa.
3. **Penyelarasan Multi-Role (Admin vs Warga)**:
   - Laravel default guard `web` hanya menyimpan 1 user login aktif. Jika admin dan warga login di browser yang sama secara bersamaan, session akan saling menimpa kecuali dibuat multi-guard (`admin` guard dan `warga` guard terpisah).

---

## 5. Rencana Migrasi Bertahap

- **Tahap 1 (Persiapan & Konfigurasi Backend)**:
  - Konfigurasikan multi-guard (guard `admin` dan guard `warga`) di `config/auth.php`.
  - Aktifkan `EnsureFrontendRequestsAreStateful` di Sanctum middleware.
  - Sediakan endpoint dual-support (menerima cookie session ATAU token Bearer).
- **Tahap 2 (Fetch Client Abstraction)**:
  - Buat satu API client wrapper terpusat di `public/assets/js/api.js` yang otomatis menangani pengambilan cookie CSRF dan penambahan header `X-XSRF-TOKEN`.
- **Tahap 3 (Transisi Frontend & Pengujian Staging)**:
  - Ubah login admin dan warga untuk menggunakan session cookie.
  - Hapus pembacaan token dari `localStorage`.
  - Uji seluruh alur pada environment staging dengan domain HTTPS nyata.
- **Tahap 4 (Pembersihan)**:
  - Hapus kode penyimpanan token lama di localStorage.
  - Cabut token personal access yang tidak lagi digunakan.

---

## 6. Estimasi Usaha (Effort Estimation)

| Komponen | Estimasi Waktu | Kompleksitas |
|----------|---------------|--------------|
| Konfigurasi Multi-Guard & Sanctum Stateful Backend | 1 - 2 Hari | Sedang |
| Refactor Frontend API Client & Auth Guard Script | 2 - 3 Hari | Sedang |
| Penyesuaian Multi-Role Admin & Warga di Browser Sama | 1 - 2 Hari | Cukup Tinggi |
| Pengujian Komprehensif & Edge Cases (CORS, Expired CSRF) | 2 Hari | Sedang |
| **Total Estimasi** | **6 - 9 Hari Kerja** | **Sedang - Tinggi** |

**Rekomendasi**: Karena aplikasi saat ini telah dilengkapi dengan audit ketat XSS (`escapeHtml`, `sanitizeSrc`, Content-Security-Policy/Sanitasi input), rate limiting ketat, dan token Sanctum yang di-prune berkala, skema Bearer Token saat ini sudah memadai untuk fase rilis awal, dengan migrasi ke Sanctum SPA dijadwalkan pada roadmap versi berikutnya.
