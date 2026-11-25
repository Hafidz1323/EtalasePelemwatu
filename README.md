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
- Berisi salinan database SQLite (`database/database.sqlite`) atau dump SQL (`database/dump.sql` jika menggunakan MySQL/MariaDB), serta seluruh berkas di folder `public/uploads`.
- **Rotasi Otomatis**: Sistem secara otomatis hanya menyimpan 7 arsip cadangan terakhir dan menghapus arsip yang lebih lama.
- Cadangan dijadwalkan berjalan otomatis setiap hari via Laravel Scheduler (`routes/console.php`).

> [!WARNING]
> **Catatan Khusus Supabase Storage (`FILESYSTEM_DISK=supabase`)**:
> Perintah `backup:run` hanya mencadangkan berkas lokal di folder `public/uploads`. Jika aplikasi dikonfigurasi menggunakan Supabase Storage, foto dan aset media tersimpan langsung di cloud Supabase dan **TIDAK** ikut terarsip ke dalam file ZIP `backup:run`. Cadangkan bucket Supabase Storage secara terpisah (misalnya via S3 bucket sync atau backup cloud).

> **Penting**: Berkas arsip cadangan tersimpan di penyimpanan lokal server yang sama (`storage/app/backups/`). Untuk mengantisipasi kegagalan server fisik atau kerusakan disk, salin berkas arsip secara berkala ke lokasi penyimpanan luar (offsite/cloud storage) dan lakukan simulasi pemulihan (uji restore) minimal sekali untuk memastikan keutuhan data cadangan.

### Konfigurasi mysqldump untuk MySQL/MariaDB

Jika menggunakan driver `mysql` atau `mariadb`, pastikan binary `mysqldump` terpasang di sistem dan tentukan path-nya pada `.env`:

```env
# Contoh di Windows XAMPP
BACKUP_MYSQLDUMP_PATH="C:\xampp\mysql\bin\mysqldump.exe"

# Contoh di Linux / Server Produksi (jika sudah di PATH atau direktori bin)
BACKUP_MYSQLDUMP_PATH="/usr/bin/mysqldump"
```

Password dikirim secara aman melalui environment variable `MYSQL_PWD` (tidak tampil di argumen proses baris perintah).

### Cara Memulihkan Data (Restore)

1. Ekstrak file arsip `.zip` yang diinginkan dari `storage/app/backups/`.
2. **Restore Database**:
   - **MySQL / MariaDB**:
     ```bash
     mysql -u nama_user -p nama_database < database/dump.sql
     ```
   - **SQLite**:
     Salin berkas `database/database.sqlite` ke path database proyek Anda.
3. **Restore Berkas Unggahan**:
   Salin isi folder `uploads/` hasil ekstrak ke dalam direktori `public/uploads/`.

---

## Panduan Deploy (Produksi)

### Syarat Server
Sebelum instalasi, pastikan lingkungan server hosting memenuhi checklist berikut:
- [ ] **PHP >= 8.3**
- [ ] **Ekstensi PHP wajib**: `gd`, `zip`, `mbstring`, `intl`, dan driver database (`pdo_sqlite` atau `pdo_mysql`)
- [ ] **Node.js 20.19+ atau 22.12+** (syarat Vite 8) & NPM (untuk proses build aset Vite)
- [ ] **Koneksi HTTPS aktif** (sertifikat SSL/TLS valid)
- [ ] **Web Server** (Nginx/Apache) dengan Document Root mengarah langsung ke folder `public/`

### 1. Konfigurasi Wajib Produksi di `.env`

Pastikan variabel berikut disesuaikan dari nilai bawaan pengembangan:

```env
APP_NAME=EtalasePelemwatu
APP_ENV=production
APP_DEBUG=false
APP_URL=https://etalase.pelemwatu.id

# Koneksi Database Produksi (Bisa tetap SQLite atau MySQL)
DB_CONNECTION=sqlite

# Kunci Sesi & Keamanan
APP_KEY=base64:...

# Akun Administrator Awal (Diisi SEBELUM menjalankan seeder)
ADMIN_NAME="Administrator Desa"
# Ganti dengan email yang benar-benar Anda kuasai (dipakai untuk login admin)
ADMIN_EMAIL=admin@pelemwatu.desa.id
ADMIN_PASSWORD=ISI_PASSWORD_ACAK_MINIMAL_16_KARAKTER

# Token Sanctum Kedaluwarsa (Menit)
SANCTUM_EXPIRATION=480

# Reverse Proxy:
# Atur TRUSTED_PROXIES=* HANYA jika server benar-benar berada di balik reverse proxy tepercaya (misalnya Cloudflare atau load balancer Nginx).
# Jika server langsung melayani pengunjung tanpa reverse proxy, KOSONGKAN variabel ini (TRUSTED_PROXIES=)
# agar IP asli pengunjung tidak dapat dipalsukan dan pembatasan laju login (rate limiting) tidak dilewati.
TRUSTED_PROXIES=

# Penyimpanan Media (Default: local)
FILESYSTEM_DISK=local

# Konfigurasi Supabase Storage (Hanya jika FILESYSTEM_DISK=supabase)
SUPABASE_S3_KEY=placeholder-s3-access-key-id
SUPABASE_S3_SECRET=placeholder-s3-secret-access-key
SUPABASE_S3_REGION=ap-southeast-1
SUPABASE_S3_BUCKET=placeholder-bucket-name
SUPABASE_S3_ENDPOINT=https://your-project-id.storage.supabase.co/storage/v1/s3
SUPABASE_PUBLIC_URL=https://your-project-id.supabase.co/storage/v1/object/public/placeholder-bucket-name
```

> **Penting (Document Root & Keamanan .env)**: Document root web server (Nginx/Apache) **wajib** mengarah ke folder `public/`. Jangan pernah menaruh seluruh folder proyek langsung di dalam folder root publik web (seperti `public_html`). Uji melalui browser bahwa URL `https://etalase.pelemwatu.id/.env` mengembalikan respons **403 Forbidden** atau **404 Not Found**, bukan mengunduh atau menampilkan isi berkas.

### 2. Konfigurasi Penyimpanan Foto (Lokal vs Supabase Storage)

Secara default, aplikasi menyimpan foto di folder lokal `public/uploads/` (`FILESYSTEM_DISK=local`). Anda dapat memindahkan penyimpanan foto ke Supabase Storage (S3-compatible):

1. **Buat Bucket di Supabase**:
   - Masuk ke dashboard Supabase -> **Storage** -> **Create a new bucket**.
   - Beri nama bucket (misal: `etalase-media`) dan **WAJIB aktifkan opsi "Public bucket"**.
2. **Ambil Kredensial S3**:
   - Masuk ke **Project Settings** -> **Storage** -> **S3 Connection**.
   - Dapatkan Endpoint URL, Access Key ID, Secret Access Key, dan Region.
3. **Isi Pengaturan di `.env`**:
   ```env
   FILESYSTEM_DISK=supabase
   SUPABASE_S3_KEY=your_access_key_id
   SUPABASE_S3_SECRET=your_secret_access_key
   SUPABASE_S3_REGION=ap-southeast-1
   SUPABASE_S3_BUCKET=etalase-media
   SUPABASE_S3_ENDPOINT=https://<project-ref>.storage.supabase.co/storage/v1/s3
   SUPABASE_PUBLIC_URL=https://<project-ref>.supabase.co/storage/v1/object/public/etalase-media
   ```
4. **Uji Koneksi & Izin**:
   Jalankan command pengujian otomatis (mengunggah file kecil, memeriksa akses URL publik, lalu menghapusnya):
   ```bash
   php artisan media:test-supabase
   ```
   Pastikan menghasilkan keluaran `Hasil Uji Koneksi Supabase Storage: BERHASIL`.
5. **Migrasi Foto Lama ke Supabase Storage**:
   Untuk menyalin berkas yang sudah ada di `public/uploads/` ke bucket Supabase:
   ```bash
   # Jalankan simulasi terlebih dahulu
   php artisan media:migrate-to-supabase --dry-run

   # Eksekusi migrasi nyata (idempoten, tidak menghapus berkas lokal)
   php artisan media:migrate-to-supabase
   ```
6. **Cara Rollback ke Mode Lokal**:
   Jika ingin kembali menggunakan penyimpanan server lokal, cukup ubah kembali:
   ```env
   FILESYSTEM_DISK=local
   ```
   Karena berkas lokal di `public/uploads` tidak pernah dihapus otomatis saat migrasi dan path database tetap berformat `"/uploads/<namafile>"`, sistem akan langsung kembali bekerja secara lokal tanpa kehilangan data.

### 3. Perintah Build & Optimasi Produksi

```bash
composer install --optimize-autoloader --no-dev
npm ci
npm run build

php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> **Catatan Build Aset Vite**:
> Folder `public/build` diabaikan oleh git (`.gitignore`). Menjalankan `npm ci && npm run build` di server **wajib** dilakukan (atau lakukan build di lingkungan lokal kemudian unggah folder `public/build` ke server). Tanpa langkah ini, seluruh halaman web akan gagal dimuat dengan galat *"Vite manifest not found"*.

> **Catatan Inisialisasi Database Seeder (`db:seed --force`)**:
> - Pastikan Anda telah mengisi `ADMIN_PASSWORD` yang kuat dan menyesuaikan `ADMIN_EMAIL` di file `.env` **sebelum** menjalankan perintah seeder.
> - Seeder bersifat idempoten: jika akun admin dengan email tersebut sudah terdaftar di database, seeder **tidak akan menimpa** data nama atau password yang sudah ada.
> - Segera ganti kata sandi administrator melalui menu profil admin setelah berhasil masuk untuk pertama kali.

### 4. Izin Akses Folder (File Permissions)

```bash
chmod -R 775 storage bootstrap/cache public/uploads
chown -R www-data:www-data storage bootstrap/cache public/uploads
```

### 5. Konfigurasi Penjadwal Tugas (Cron Job Scheduler)

Jalankan perintah penjadwal Laravel setiap menit menggunakan crontab server Linux (`crontab -e -u www-data`):

```bash
* * * * * cd /path/ke/proyek && php artisan schedule:run >> /dev/null 2>&1
```

> **Penting**: Penjadwal tugas (Laravel Scheduler) bertanggung jawab menjalankan proses otomatis krusial secara berkala:
> 1. **Pencadangan Data Harian (Backup)**: Menjalankan `php artisan backup:run` setiap hari dan melakukan rotasi 7 cadangan terakhir.
> 2. **Pembersihan Token Sanctum**: Menghapus token autentikasi yang sudah kedaluwarsa dari tabel `personal_access_tokens` agar database tetap bersih dan optimal.

---

## Keamanan Server Web (Nginx)

Untuk mencegah eksekusi skrip PHP yang diunggah ke folder publik, tambahkan konfigurasi blok berikut di konfigurasi virtual host Nginx Anda:

```nginx
server {
    server_name etalase.pelemwatu.id;
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

Proprietary — Pemerintah Desa Pelemwatu. Seluruh hak cipta dilindungi undang-undang. Teks lengkap ada di berkas [LICENSE](LICENSE).
