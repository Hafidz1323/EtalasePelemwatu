# EtalasePelemwatu

Aplikasi web untuk menampilkan dan mengelola potensi desa (usaha, jasa, hunian, barang) berbasis Laravel 13. Warga dapat mendaftarkan potensi secara mandiri; admin desa memverifikasi dan mempublikasikannya melalui panel administrasi.

## Fitur Utama

- Katalog potensi publik dengan filter kategori dan pencarian
- Pendaftaran potensi oleh warga (tanpa akun) beserta foto (maks 3, 2 MB, jpg/png/webp)
- Panel admin: verifikasi, penolakan, dan pengelolaan potensi
- REST API berbasis Laravel Sanctum (token dengan masa berlaku)
- Throttle login per email+IP; proxy trust yang dapat dikonfigurasi

## Kebutuhan Sistem

| Komponen | Versi Minimum |
|----------|--------------|
| PHP | 8.3 |
| Composer | 2.x |
| SQLite / MySQL | 3.x / 8.x |

## Instalasi

```bash
# 1. Clone repositori
git clone <url-repo> EtalasePelemwatu
cd EtalasePelemwatu

# 2. Install dependensi PHP
composer install

# 3. Salin dan konfigurasi file environment
cp .env.example .env
php artisan key:generate
```

Buka `.env` dan sesuaikan nilai berikut sebelum melanjutkan:

```env
APP_URL=http://localhost:8000
ADMIN_NAME="Nama Administrator"
ADMIN_EMAIL=admin@pelemwatu.desa.id
ADMIN_PASSWORD=kata-sandi-aman
```

```bash
# 4. Jalankan migrasi dan seeder
php artisan migrate
php artisan db:seed
```

> **Penting:** `ADMIN_PASSWORD` di `.env` wajib diisi sebelum `db:seed`. Seeder akan membuat akun admin awal dengan nilai tersebut.

## Menjalankan Aplikasi

```bash
php artisan serve
```

Buka <http://localhost:8000> di browser.

| Halaman | URL |
|---------|-----|
| Katalog publik | `/` |
| Daftar potensi | `/daftar-potensi` |
| Panel admin | `/admin` |
| Login admin | `/login` |

## Menjalankan Test

```bash
php artisan test
```

Atau langsung via PHPUnit:

```bash
php vendor/phpunit/phpunit/phpunit --configuration phpunit.xml
```

Test suite mencakup autentikasi, CRUD potensi, upload gambar, throttle, dan pagination.

## Panduan Deploy (Produksi)

### 1. Variabel Environment

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.id
```

### 2. Instalasi Dependensi

```bash
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
```

### 3. Cache Konfigurasi & Route

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Permission

```bash
chmod -R 775 storage bootstrap/cache
chmod -R 775 public/uploads
chown -R www-data:www-data storage bootstrap/cache public/uploads
```

### 5. Proxy & Keamanan

Jika aplikasi berada di belakang load balancer atau reverse proxy (Nginx, Cloudflare), atur:

```env
TRUSTED_PROXIES=IP_PROXY_ANDA
```

Gunakan `*` hanya jika semua traffic sudah melalui proxy tepercaya.

Token Sanctum kedaluwarsa setelah:

```env
SANCTUM_EXPIRATION=480   # menit (default 8 jam)
```

## Lisensi

Proprietary — Desa Pelemwatu. Semua hak cipta dilindungi.
