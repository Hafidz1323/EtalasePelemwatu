# EtalasePelemwatu

Aplikasi web untuk menampilkan dan mengelola potensi desa (usaha, jasa, hunian, barang) berbasis Laravel 13. Warga dapat mendaftarkan potensi secara mandiri; admin desa memverifikasi dan mempublikasikannya melalui panel administrasi.

---

## Daftar Isi
- [Fitur Utama](#fitur-utama)
- [Tangkapan Layar](#tangkapan-layar)
- [Kebutuhan Sistem](#kebutuhan-sistem)
- [Instalasi & Menjalankan](#instalasi--menjalankan)
- [Struktur Proyek](#struktur-proyek)
- [Daftar Endpoint API Utama](#daftar-endpoint-api-utama)
- [Pengelolaan Profil & Kontak Desa](#pengelolaan-profil--kontak-desa)
- [Pemulihan Akun Admin](#pemulihan-akun-admin)
- [Cadangan Data (Backup)](#cadangan-data-backup)
- [Panduan Deploy (Produksi)](#panduan-deploy-produksi)
- [Keamanan Server Web (Nginx)](#keamanan-server-web-nginx)
- [Menjalankan Test](#menjalankan-test)
- [Lisensi](#lisensi)

---

## Fitur Utama

- **Katalog Potensi Publik**: Menampilkan potensi lokal dengan pencarian teks, filter kategori, slider, dan detail modal interaktif.
- **Pendaftaran Mandiri & Autentikasi Warga**: Warga desa dapat mendaftar akun, mengelola profil, melihat status verifikasi, mengedit data, serta mengajukan penghapusan potensi.
- **Panel Administrator Lengkap**:
  - Verifikasi dan persetujuan potensi baru.
  - Pengelolaan permohonan hapus dari warga.
  - CRUD Galeri Kegiatan Desa (foto, judul, deskripsi, urutan).
  - Pengaturan Profil Desa (alamat, jam operasional, WhatsApp, telepon, email, jumlah KK) yang tersinkronisasi otomatis ke website publik.
  - Penggantian logo portal desa dinamis.
  - Keamanan akun dan ganti kata sandi.
- **Optimasi Gambar Otomatis**: Setiap gambar yang diunggah di-resize maksimal 1600px dan dikonversi ke WebP kualitas 80 (menggunakan GD) dengan nama acak aman.
- **Aset Terpadu (Self-Hosted)**: Bootstrap 5.3.3 dan Bootstrap Icons 1.11.3 dibundel lokal menggunakan npm dan Vite (bebas ketergantungan CDN eksternal).
- **SEO & Peta Situs**: Meta deskripsi, tag Open Graph, Twitter Cards, `robots.txt`, dan rute `/sitemap.xml` dinamis.
- **Keamanan Ketat**: Proteksi XSS (sanitasi atribut URL dan teks), pembatasan rate limit login warga dan admin, .htaccess proteksi folder uploads, serta token Sanctum yang di-prune secara terjadwal.

---

## Tangkapan Layar

Simpan tangkapan layar antarmuka aplikasi pada folder `docs/screenshots/`:
- `docs/screenshots/homepage.png` — Tampilan Beranda & Katalog
- `docs/screenshots/daftar-potensi.png` — Formulir Pendaftaran Potensi
- `docs/screenshots/admin-dashboard.png` — Dashboard Administrator
- `docs/screenshots/profil-warga.png` — Halaman Profil & Potensi Warga

---

## Kebutuhan Sistem

| Komponen | Versi Minimum | Keterangan |
|----------|--------------|------------|
| PHP | 8.3 | Ekstensi: `pdo_sqlite`, `gd`, `zip`, `mbstring`, `fileinfo` |
| Composer | 2.x | Manajer dependensi PHP |
| Node.js & npm | 20.x / 10.x | Untuk build aset Vite (Bootstrap & Icons) |
| Database | SQLite 3.x / MySQL 8.x | SQLite bawaan untuk kemudahan deployment |

---

## Instalasi & Menjalankan

### 1. Salin Repositori & Install Dependensi

```bash
# Clone repositori
git clone <url-repo> EtalasePelemwatu
cd EtalasePelemwatu

# Install dependensi PHP
composer install

# Install dependensi Node.js & build aset
npm install
npm run build
```

### 2. Konfigurasi Environment

File `.env.example` telah dikonfigurasi dengan pengaturan pengembangan bawaan (`APP_DEBUG=true`, `DB_CONNECTION=sqlite`).

```bash
cp .env.example .env
php artisan key:generate
```

Buka `.env` dan sesuaikan akun admin awal:

```env
APP_URL=http://localhost:8000
ADMIN_NAME="Administrator Desa"
ADMIN_EMAIL=admin@pelemwatu.desa.id
ADMIN_PASSWORD=kata-sandi-aman
```

> **Catatan:** `ADMIN_PASSWORD` di `.env` wajib diisi sebelum menjalankan `db:seed`.

### 3. Migrasi & Seeder Database

```bash
php artisan migrate
php artisan db:seed
```

### 4. Menjalankan Server Lokal

```bash
php artisan serve
```

Buka <http://localhost:8000> di browser:
- **Katalog Publik**: `/`
- **Pendaftaran Potensi**: `/daftar-potensi`
- **Panel Admin**: `/admin`
- **Login Admin**: `/login`
- **Portal Warga**: `/register` (Masuk / Daftar Akun) dan `/profil-warga`

---

## Struktur Proyek

```
EtalasePelemwatu/
├── app/
│   ├── Console/Commands/        # Command CLI: RunBackup, AdminResetPassword
│   ├── Http/Controllers/        # Web & Sitemap Controller
│   │   └── API/                 # API Controllers (Auth, Potensi, Galeri, ProfilDesa, dll.)
│   ├── Models/                  # Eloquent Models (User, Potensi, Galeri, Setting, dll.)
│   └── Services/                # Service Layer (ImageOptimizer)
├── database/
│   ├── migrations/              # Skema database
│   └── seeders/                 # DatabaseSeeder & data awal
├── public/
│   ├── assets/                  # CSS, JS, dan Gambar statis bawaan
│   │   └── js/                  # utils.js, site-logo.js, auth.js, dll.
│   ├── uploads/                 # File media dinamis (.htaccess dilindungi)
│   └── robots.txt               # Aturan perayap mesin pencari
├── resources/
│   ├── css/ & js/               # Sumber daya Vite (Bootstrap, Icons, App)
│   └── views/                   # Template Blade (index, admin, login, register, dll.)
├── routes/
│   ├── api.php                  # Endpoint REST API publik, warga, dan admin
│   ├── console.php              # Penjadwalan task (prune token, backup harian)
│   └── web.php                  # Halaman web dan redirect 301 legacy URL
└── tests/                       # Rangkaian automated test PHPUnit Feature
```

---

## Daftar Endpoint API Utama

### Rute Publik
- `GET /api/potensi` — Daftar potensi yang telah disetujui admin.
- `GET /api/potensi/{id}` — Detail potensi terverifikasi.
- `POST /api/potensi` — Pendaftaran potensi baru oleh pengunjung/warga (`pending`).
- `GET /api/categories` — Daftar kategori potensi.
- `GET /api/galeri` — Foto kegiatan galeri desa (diurutkan berdasarkan `sort_order`).
- `GET /api/stats/public` — Statistik ringkas publik (jumlah UMKM berstatus aktif).
- `GET /api/settings/logo` — URL logo situs resmi desa terkini.
- `GET /sitemap.xml` — Peta situs XML dinamis.

### Rute Autentikasi
- `POST /api/login` — Autentikasi admin (rate limit: 5x/menit per email+IP).
- `POST /api/warga/register` — Registrasi akun warga desa.
- `POST /api/warga/login` — Login akun warga (rate limit: 5x/menit per nomor HP+IP).
- `GET /api/auth/verify` & `POST /api/auth/logout` — Verifikasi dan pencabutan token.

### Rute Khusus Warga (`auth:sanctum`, `role:warga`)
- `GET /api/warga/profile` & `POST /api/warga/profile` — Data dan pembaruan profil warga.
- `PUT /api/warga/password` — Ganti password akun warga.
- `GET /api/warga/potensi` — Daftar potensi yang didaftarkan oleh akun bersangkutan.
- `POST /api/warga/potensi/{id}` — Pembaruan informasi potensi milik sendiri.
- `POST /api/warga/potensi/{id}/request-deletion` — Pengajuan permohonan hapus potensi ke admin.

### Rute Administrator (`auth:sanctum`, `role:admin`)
- `POST /api/potensi/{id}/review` — Menyetujui atau menolak pendaftaran potensi.
- `GET /api/admin/deletion-requests` — Daftar permohonan penghapusan potensi.
- `POST /api/admin/deletion-requests/{id}/approve` — Menyetujui penghapusan permanen.
- `GET /api/admin/profil-desa` & `PUT /api/admin/profil-desa` — Membaca dan memperbarui info kontak desa.
- `POST /api/galeri` & `DELETE /api/galeri/{id}` — Tambah dan hapus dokumentasi galeri.
- `POST /api/settings/logo` — Unggah logo resmi portal desa.

---

## Pengelolaan Profil & Kontak Desa

Administrator desa dapat memperbarui informasi kontak desa langsung dari Panel Admin tanpa menyentuh kode program:
1. Buka **Panel Admin** (`/admin`) dan masuk ke bagian **Profil Desa**.
2. Perbarui field:
   - Alamat Lengkap & Kode Pos
   - Nomor Telepon Kantor
   - Nomor WhatsApp Resmi Desa (otomatis dinormalisasi ke format `628xxxxxxxxxx`)
   - Email Resmi & Nama Kepala Desa
   - Jam Operasional Kantor
   - Akun Instagram Desa (contoh: `@official_pelemwatu`)
   - Estimasi Jumlah Kepala Keluarga (KK)
3. Klik **Simpan Perubahan**.
4. Seluruh pembaruan langsung tercermin secara instan di kartu lokasi beranda, bagian kontak, footer, dan formulir pendaftaran.

---

## Pemulihan Akun Admin

Jika administrator lupa kata sandi dan tidak dapat masuk ke Panel Admin, gunakan artisan command pemulihan:

```bash
php artisan admin:reset-password admin@pelemwatu.desa.id
```

Command ini akan:
1. Menghasilkan kata sandi acak baru sepanjang 16 karakter yang kuat.
2. Menampilkan kata sandi baru sekali di terminal.
3. Mencabut seluruh token Sanctum aktif akun tersebut demi keamanan.

---

## Cadangan Data (Backup)

Aplikasi dilengkapi command pencadangan otomatis untuk membackup basis data dan file unggahan:

```bash
php artisan backup:run
```

- Arsip `.zip` disimpan di `storage/app/backups/backup-YYYY-MM-DD-His.zip`.
- Berisi salinan database SQLite (`database/database.sqlite`) atau dump SQL, serta seluruh berkas di folder `public/uploads`.
- **Rotasi Otomatis**: Sistem secara otomatis hanya menyimpan 7 arsip cadangan terakhir dan menghapus arsip yang lebih lama.
- Cadangan dijadwalkan berjalan otomatis setiap hari via Laravel Scheduler (`routes/console.php`).

---

## Panduan Deploy (Produksi)

### 1. Konfigurasi Wajib Produksi di `.env`

Pastikan variabel berikut diubah dari nilai bawaan pengembangan:

```env
APP_NAME=EtalasePelemwatu
APP_ENV=production
APP_DEBUG=false
APP_URL=https://etalase.pelemwatu.desa.id

# Koneksi Database Produksi (Bisa tetap SQLite atau MySQL)
DB_CONNECTION=sqlite

# Kunci Sesi & Keamanan
APP_KEY=base64:...

# Token Sanctum Kedaluwarsa (Menit)
SANCTUM_EXPIRATION=480

# Reverse Proxy (Jika menggunakan Cloudflare/Nginx)
TRUSTED_PROXIES=*
```

### 2. Perintah Build & Optimasi Produksi

```bash
composer install --optimize-autoloader --no-dev
npm ci
npm run build

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Izin Akses Folder (File Permissions)

```bash
chmod -R 775 storage bootstrap/cache public/uploads
chown -R www-data:www-data storage bootstrap/cache public/uploads
```

---

## Keamanan Server Web (Nginx)

Untuk mencegah eksekusi skrip PHP yang diunggah ke folder publik, tambahkan konfigurasi blok berikut di konfigurasi virtual host Nginx Anda:

```nginx
server {
    server_name etalase.pelemwatu.desa.id;
    root /var/www/EtalasePelemwatu/public;

    index index.php index.html;

    # Blokir eksekusi skrip berbahaya di folder uploads
    location ~* ^/uploads/.*\.php$ {
        deny all;
        return 404;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

---

## Menjalankan Test

Jalankan pengujian otomatis untuk memvalidasi seluruh fungsionalitas:

```bash
php artisan test
```

Pemeriksaan gaya penulisan kode (PHP CS):

```bash
vendor/bin/pint --test
```

---

## Lisensi

Proprietary — Pemerintah Desa Pelemwatu. Seluruh hak cipta dilindungi undang-undang.
