<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EtalasePelemwatu — Portal Potensi Lokal Desa Pelemwatu</title>
  <meta name="description" content="Portal digital potensi lokal Desa Pelemwatu, Kecamatan Menganti, Kabupaten Gresik, Jawa Timur.">
  <link rel="canonical" href="{{ url()->current() }}">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:title" content="EtalasePelemwatu — Portal Potensi Lokal Desa Pelemwatu">
  <meta property="og:description" content="Portal digital potensi lokal Desa Pelemwatu, Kecamatan Menganti, Kabupaten Gresik, Jawa Timur.">
  <meta property="og:image" content="{{ asset('assets/img/foto-kantor-desa.jpg') }}">
  <meta property="og:locale" content="id_ID">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:url" content="{{ url()->current() }}">
  <meta name="twitter:title" content="EtalasePelemwatu — Portal Potensi Lokal Desa Pelemwatu">
  <meta name="twitter:description" content="Portal digital potensi lokal Desa Pelemwatu, Kecamatan Menganti, Kabupaten Gresik, Jawa Timur.">
  <meta name="twitter:image" content="{{ asset('assets/img/foto-kantor-desa.jpg') }}">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/galeri.css">
  <script>document.documentElement.classList.add('js-anim');</script>
</head>
<body>

<!-- ============================================================
     SITE HEADER
     ============================================================ -->
<header id="siteHeader">
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg" id="mainNavbar">
    <div class="container">
      <a class="navbar-brand-wrap" href="/">
        <img class="brand-logo" src="assets/img/logo-desa.png" alt="Logo Desa Pelemwatu" data-site-logo>
        <div class="brand-text-wrap">
          <span class="brand-name">EtalasePelemwatu</span>
          <span class="brand-sub">Desa Pelemwatu</span>
        </div>
      </a>
      <button class="navbar-toggler ms-auto me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav mx-auto gap-1">
          <li class="nav-item"><a class="nav-link active" href="#hero-desa">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="#profil-desa">Profil Desa</a></li>
          <li class="nav-item"><a class="nav-link" href="#pengenalan-etalase">Tentang</a></li>
          <li class="nav-item"><a class="nav-link" href="#katalog">Katalog</a></li>
          <li class="nav-item"><a class="nav-link" href="#layanan">Layanan</a></li>
          <li class="nav-item"><a class="nav-link" href="#kontak-section">Kontak</a></li>
        </ul>
        <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0 ms-lg-2 flex-wrap">
          <div id="navWargaWrap"></div>
          <div id="navAdminWrap">
            <button type="button" class="btn-nav-admin" data-bs-toggle="modal" data-bs-target="#modalLoginAdmin">
              Login Admin
            </button>
          </div>
          <a href="/daftar-potensi" class="nav-link btn-nav-daftar" id="navBtnDaftar">
            Daftarkan Potensi
            <span class="nav-cta-arrow"><i class="bi bi-arrow-up-right"></i></span>
          </a>
        </div>
      </div>
    </div>
  </nav>
</header>

<!-- ============================================================
     HERO — PENGENALAN DESA PELEMWATU
     ============================================================ -->
<div class="hero-frame-wrap">
  <section id="hero-desa">
    <div class="container position-relative" style="z-index:2;">
      <div class="hero-content">
        <h1>Selamat Datang di<br>Desa Pelemwatu</h1>
        <p class="hero-desc">
          Desa Pelemwatu berada di Kecamatan Menganti, Kabupaten Gresik.
          Warganya menjalankan usaha kuliner, laundry, kost, dan masih banyak
          lagi. Semuanya dikumpulkan di satu etalase supaya mudah ditemukan.
        </p>
        <div class="hero-cta-wrap">
          <a href="#katalog" class="btn-hero-primary">Lihat Usaha Warga</a>
          <a href="#profil-desa" class="btn-hero-outline">Kenali Desanya</a>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- ============================================================
     PROFIL DESA
     ============================================================ -->
<section id="profil-desa" class="py-5">
  <div class="container">
    <div class="section-header center reveal">
      <h2>Mengenal Desa Pelemwatu</h2>
      <p>Temukan informasi lengkap mengenai Desa Pelemwatu, mulai dari gambaran umum,
         lokasi, hingga potensi yang dimiliki masyarakatnya.</p>
    </div>

    <!-- Tab Nav -->
    <div class="profil-nav reveal">
      <button class="profil-nav-btn active" data-tab="tab-galeri">
        Galeri Kegiatan
      </button>
      <button class="profil-nav-btn" data-tab="tab-tentang">
        Tentang Desa
      </button>
      <button class="profil-nav-btn" data-tab="tab-lokasi">
        Lokasi Desa
      </button>
    </div>

    <!-- TAB: Galeri -->
    <div class="profil-tab-pane active" id="tab-galeri">
      @php
        $galeriItems = \App\Models\Galeri::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
      @endphp

      @if($galeriItems->isEmpty())
        <div class="desa-info-card py-5 text-center text-muted gs-empty">
          <i class="bi bi-images me-2" style="font-size: 2.2rem; display: block; margin-bottom: .6rem; color: var(--clr-text-light);"></i>
          <span>Belum ada foto kegiatan.</span>
        </div>
      @else
        <div class="gs-container" id="gsContainer" role="region" aria-roledescription="carousel" aria-label="Galeri Kegiatan Desa Pelemwatu" tabindex="0">
          <div class="gs-layout">
            <!-- Sisi Kiri: Rel Vertikal & Teks -->
            <div class="gs-sidebar">
              <!-- Rel Vertikal Dots -->
              <div class="gs-rail-wrapper">
                <div class="gs-rail" id="gsRail">
                  <div class="gs-rail-line" aria-hidden="true"></div>
                  <div class="gs-dots-window" id="gsDotsWindow"></div>
                </div>
                <div class="gs-counter" id="gsCounter" aria-hidden="true">
                  <span id="gsCounterCurrent">1</span> / <span id="gsCounterTotal">{{ $galeriItems->count() }}</span>
                </div>
              </div>

              <!-- Konten Teks Aktif -->
              <div class="gs-content" id="gsContent" aria-live="polite">
                <h3 class="gs-title" id="gsTitle">{{ $galeriItems->first()->caption }}</h3>
                <p class="gs-desc" id="gsDesc">{{ $galeriItems->first()->description ?: 'Dokumentasi kegiatan masyarakat dan aktivitas di Desa Pelemwatu.' }}</p>
                <div class="gs-actions">
                  <button type="button" class="btn-primary-custom gs-btn-view" id="gsBtnView" aria-label="Lihat foto: {{ $galeriItems->first()->caption }}">
                    <i class="bi bi-arrows-fullscreen me-2"></i>Lihat Foto
                  </button>
                  <div class="gs-nav-arrows">
                    <button type="button" class="gs-arrow gs-arrow-prev" id="gsPrev" aria-label="Foto sebelumnya" disabled>
                      <i class="bi bi-arrow-left"></i>
                    </button>
                    <button type="button" class="gs-arrow gs-arrow-next" id="gsNext" aria-label="Foto berikutnya" {{ $galeriItems->count() <= 1 ? 'disabled' : '' }}>
                      <i class="bi bi-arrow-right"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Sisi Kanan: Viewport & Track Kartu Horizontal -->
            <div class="gs-viewport" id="gsViewport">
              <div class="gs-track" id="gsTrack">
                @foreach($galeriItems as $idx => $g)
                <div class="gs-card {{ $idx === 0 ? 'is-active' : '' }}"
                     data-index="{{ $idx }}"
                     data-src="{{ $g->image_path }}"
                     data-caption="{{ $g->caption }}"
                     data-desc="{{ $g->description ?? '' }}"
                     role="group"
                     aria-roledescription="slide"
                     aria-label="{{ $idx + 1 }} dari {{ $galeriItems->count() }}: {{ $g->caption }}">
                  <div class="gs-card-inner">
                    <img src="{{ $g->image_path }}"
                         alt="{{ $g->caption }}"
                         loading="{{ $idx < 2 ? 'eager' : 'lazy' }}"
                         draggable="false">
                    <div class="gs-card-overlay">
                      <span class="gs-card-zoom-badge"><i class="bi bi-zoom-in me-1"></i>Perbesar</span>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      @endif
    </div>

    <!-- TAB: Tentang -->
    <div class="profil-tab-pane" id="tab-tentang">
      <div class="row g-4">
        <div class="col-lg-7">
          <div class="desa-info-card">
            <h4>Tentang Desa Pelemwatu</h4>
            <p>Desa Pelemwatu adalah salah satu desa yang berada di wilayah Kecamatan Menganti, Kabupaten Gresik,
            Provinsi Jawa Timur. Sebagai bagian dari sistem pemerintahan desa, Desa Pelemwatu memiliki struktur
            pemerintahan yang dipimpin oleh Kepala Desa beserta perangkat desa yang bertugas melayani masyarakat.</p>
            <p>Masyarakat Desa Pelemwatu dikenal aktif dalam berbagai kegiatan ekonomi dan sosial. Terdapat beragam
            usaha, produk, jasa, dan hunian yang dikelola oleh warga sebagai wujud kemandirian dan kreativitas lokal.</p>
            <p>Melalui EtalasePelemwatu, potensi-potensi lokal ini didokumentasikan dan dipromosikan agar dapat dikenal
            lebih luas oleh masyarakat sekitar maupun luar daerah.</p>
          </div>
        </div>
        <div class="col-lg-5 d-flex flex-column gap-3">
          <div class="desa-info-card">
            <h4>Identitas Desa</h4>
            <table class="table table-sm mb-0" style="font-size:.9rem;">
              <tbody>
                <tr>
                  <td style="color:var(--text-light);width:45%;">Nama Desa</td>
                  <td style="color:var(--clr-blue);font-weight:600;">Pelemwatu</td>
                </tr>
                <tr>
                  <td style="color:var(--text-light);">Kecamatan</td>
                  <td style="color:var(--clr-blue);font-weight:600;">Menganti</td>
                </tr>
                <tr>
                  <td style="color:var(--text-light);">Kabupaten</td>
                  <td style="color:var(--clr-blue);font-weight:600;">Gresik</td>
                </tr>
                <tr>
                  <td style="color:var(--text-light);">Provinsi</td>
                  <td style="color:var(--clr-blue);font-weight:600;">Jawa Timur</td>
                </tr>
                @if(!empty($desa['kepala']))
                <tr>
                  <td style="color:var(--text-light);">Kepala Desa</td>
                  <td style="color:var(--clr-blue);font-weight:600;">{{ $desa['kepala'] }}</td>
                </tr>
                @endif
                <tr>
                  <td style="color:var(--text-light);">Status</td>
                  <td><span class="status-badge status-aktif">Desa Aktif</span></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Statistik desa -->
          <div class="desa-stat-row">
            <div class="desa-stat-card">
              <!-- GANTI angka di bawah dengan jumlah KK resmi dari data desa -->
              <div class="desa-stat-num" id="statKK">{{ ($desa['jumlah_kk'] !== null && $desa['jumlah_kk'] !== '') ? number_format((int) $desa['jumlah_kk'], 0, ',', '.') : '—' }}</div>
              <div class="desa-stat-label">Jumlah KK</div>
            </div>
            <div class="desa-stat-card">
              <div class="desa-stat-num" id="statUMKM">&mdash;</div>
              <div class="desa-stat-label">UMKM Terdaftar</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB: Lokasi -->
    <div class="profil-tab-pane" id="tab-lokasi">
      <div class="row g-3 align-items-stretch">
        <div class="col-lg-4">
          <div class="desa-info-card lokasi-card h-100">
            <h4>Lokasi Desa Pelemwatu</h4>
            <h6 class="lokasi-label">Alamat Resmi</h6>
            <p class="lokasi-alamat">{{ $desa['alamat'] }}</p>
            @if(!empty($desa['kode_pos']))
            <h6 class="lokasi-label">Kode Pos</h6>
            <p class="lokasi-alamat">{{ $desa['kode_pos'] }}</p>
            @endif
            <a class="btn-outline-custom lokasi-btn" href="https://www.google.com/maps/search/?api=1&query=Desa+Pelemwatu+Menganti+Gresik" target="_blank" rel="noopener">Buka di Google Maps</a>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="desa-info-card lokasi-card h-100">
            <h4>Peta Lokasi</h4>
            <div class="lokasi-map">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.0!2d112.5847!3d-7.2456!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7f8e3e8e3e8e3%3A0x0!2sDesa+Pelemwatu%2C+Menganti%2C+Gresik!5e0!3m2!1sid!2sid!4v1699999999999!5m2!1sid!2sid" width="600" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB: Informasi -->
  </div><!-- /container -->
</section>

<!-- ============================================================
     TENTANG ETALASE — layout dua kolom: teks kiri, foto kanan
     ============================================================ -->
<section id="pengenalan-etalase" class="tentang-etalase-v2">
  <div class="container">
    <div class="row align-items-center g-0">

      {{-- Kolom kiri: konten teks --}}
      <div class="col-lg-6 tentang-v2-left">
        <div class="tentang-v2-eyebrow">
          <span class="tentang-v2-line"></span>
          <span class="tentang-v2-label">Tentang Etalase</span>
        </div>
        <h2 class="tentang-v2-title">Siapa <strong>Kami?</strong></h2>
        <p class="tentang-v2-body">
          <strong>EtalasePelemwatu</strong> adalah katalog digital resmi Desa Pelemwatu,
          Kecamatan Menganti, Kabupaten Gresik. Kami hadir untuk menjadi jembatan antara
          potensi lokal warga dengan pasar yang lebih luas — dari kuliner rumahan, kerajinan
          tangan, hingga jasa dan hunian sewa.
        </p>
        <p class="tentang-v2-body">
          Setiap usaha yang terdaftar ditampilkan lengkap dengan foto, deskripsi, dan
          informasi kontak pemilik. Pengunjung dapat mencari dan menyaring berdasarkan
          kategori, sehingga potensi warga lebih mudah ditemukan oleh siapa saja.
        </p>
        <a href="#katalog" class="tentang-v2-btn">Lihat Katalog Potensi</a>
      </div>

      {{-- Kolom kanan: foto + blok warna backdrop --}}
      <div class="col-lg-6 tentang-v2-right">
        <div class="tentang-v2-img-wrap">
          <div class="tentang-v2-backdrop"></div>
          <img
            src="{{ asset('assets/img/foto-warung-desa.jpg') }}"
            alt="Suasana usaha warga Desa Pelemwatu"
            class="tentang-v2-photo"
          >
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     KATALOG POTENSI LOKAL — Horizontal Slider + Search
     ============================================================ -->
<section id="katalog" class="katalog-slider-section">
  <div class="container">

    {{-- Header: judul kiri, deskripsi kanan --}}
    <div class="ks-header">
      <div class="ks-header-left">
        <h2 class="ks-title">Temukan Potensi Lokal</h2>
      </div>
      <div class="ks-header-right">
        <p class="ks-desc">Berbagai usaha, jasa, hunian, dan produk warga Desa Pelemwatu. Geser atau cari untuk menjelajahi.</p>
      </div>
    </div>

    {{-- Search bar --}}
    <div class="ks-search-wrap">
      <div class="ks-search-box">
        <i class="bi bi-search ks-search-icon"></i>
        <input type="text" id="ksSearchInput" class="ks-search-input" placeholder="Cari nama usaha, kategori, atau deskripsi..." autocomplete="off">
        <button type="button" id="ksSearchClear" class="ks-search-clear" style="display:none;" title="Hapus">
          <i class="bi bi-x-circle-fill"></i>
        </button>
      </div>
      <div class="ks-filter-tags" id="ksFilterTags">
        <button class="ks-tag active" data-cat="">Semua</button>
        <button class="ks-tag" data-cat="usaha">Usaha</button>
        <button class="ks-tag" data-cat="jasa">Jasa</button>
        <button class="ks-tag" data-cat="hunian">Hunian</button>
        <button class="ks-tag" data-cat="barang">Barang</button>
      </div>
    </div>

    {{-- Loading skeleton / hasil --}}
    <div class="ks-viewport" id="ksViewport">
      <div class="ks-track" id="ksTrack">
        <div class="ks-skeleton"></div>
        <div class="ks-skeleton"></div>
        <div class="ks-skeleton"></div>
        <div class="ks-skeleton"></div>
      </div>
    </div>
    <div id="ksEmpty" class="ks-empty" style="display:none;">
      <i class="bi bi-search" style="font-size:2rem;color:#ccc;"></i>
      <p>Tidak ada potensi yang cocok.</p>
    </div>

    {{-- Footer: tombol Daftarkan kiri, Prev/Next kanan --}}
    <div class="ks-footer">
      <a href="/daftar-potensi" class="ks-btn-all">Daftarkan Potensimu</a>
      <div class="ks-nav">
        <button class="ks-nav-btn" id="ksPrev" title="Sebelumnya">
          <i class="bi bi-chevron-left"></i>
        </button>
        <button class="ks-nav-btn" id="ksNext" title="Berikutnya">
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </div>

  </div>
</section>



<!-- ============================================================
     LAYANAN DIGITAL
     ============================================================ -->
<section id="layanan" class="py-5">
  <div class="container">
    <div class="section-header center reveal">
      <h2>Layanan yang Tersedia</h2>
      <p>EtalasePelemwatu menyediakan layanan digital untuk memudahkan akses informasi dan promosi potensi lokal Desa Pelemwatu.</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="layanan-card reveal">
          <h4>Pendaftaran Potensi Lokal</h4>
          <p>Daftarkan usaha, produk, jasa, hunian, atau barang milik Anda ke dalam katalog potensi Desa Pelemwatu secara mudah dan gratis.</p>
          <a href="/daftar-potensi" class="btn-primary-custom">
            Daftarkan Sekarang
          </a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="layanan-card reveal">
          <h4>Informasi Potensi Warga</h4>
          <p>Temukan informasi lengkap mengenai berbagai potensi lokal yang dimiliki masyarakat Desa Pelemwatu dalam satu katalog digital.</p>
          <a href="#katalog" class="btn-riviera-custom">
            Lihat Katalog
          </a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="layanan-card reveal">
          <h4>Kontak dan Informasi Desa</h4>
          <p>Hubungi kami untuk informasi lebih lanjut atau pertanyaan seputar Desa Pelemwatu dan platform EtalasePelemwatu.</p>
          <a href="#kontak-section" class="btn-outline-custom">
            Lihat Kontak
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     KONTAK
     ============================================================ -->
<section id="kontak-section" class="py-5">
  <div class="container">
    <div class="section-header center reveal">
      <h2>Kontak &amp; Informasi</h2>
      <p>Hubungi kami untuk informasi lebih lanjut seputar EtalasePelemwatu dan Desa Pelemwatu.</p>
    </div>
    <div class="kontak-list reveal">
      <div class="kontak-item">
        <i class="bi bi-geo-alt kontak-ikon" aria-hidden="true"></i>
        <h6>Alamat</h6>
        <p>{{ $desa['alamat'] }}</p>
      </div>
      <div class="kontak-item">
        <i class="bi bi-clock kontak-ikon" aria-hidden="true"></i>
        <h6>Jam Operasional</h6>
        <p>{{ $desa['jam_operasional'] }}</p>
      </div>
      <div class="kontak-item">
        <i class="bi bi-whatsapp kontak-ikon" aria-hidden="true"></i>
        <h6>WhatsApp</h6>
        <p><a href="https://wa.me/{{ $desa['whatsapp'] }}?text=Halo%2C%20saya%20ingin%20bertanya%20tentang%20EtalasePelemwatu." target="_blank" rel="noopener noreferrer">{{ $desa['whatsapp_display'] }}</a></p>
        @if(!empty($desa['telepon']))
        <p class="mt-1 mb-0" style="font-size:.82rem;color:var(--text-light, #6b7280);">
          Telp. Kantor: <a href="tel:{{ $desa['telepon'] }}" style="color:var(--clr-blue);font-weight:600;">{{ $desa['telepon'] }}</a>
        </p>
        @endif
      </div>
      <div class="kontak-item">
        <i class="bi bi-envelope kontak-ikon" aria-hidden="true"></i>
        <h6>Email</h6>
        <p><a href="mailto:{{ $desa['email'] }}">{{ $desa['email'] }}</a></p>
      </div>
      <div class="kontak-item">
        <i class="bi bi-instagram kontak-ikon" aria-hidden="true"></i>
        <h6>Instagram</h6>
        <p><a href="https://www.instagram.com/{{ $desa['instagram'] }}" target="_blank" rel="noopener noreferrer">{{ '@' . $desa['instagram'] }}</a></p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     FOOTER
     ============================================================ -->
<footer id="main-footer" class="py-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="footer-brand">
          EtalasePelemwatu
        </div>
        <p class="footer-desc mt-2">
          Platform digital untuk mengenal dan mempromosikan potensi lokal masyarakat
          Desa Pelemwatu, Kecamatan Menganti, Kabupaten Gresik, Jawa Timur.
        </p>
        <p class="footer-desc mt-3" style="font-size:.78rem;">&copy; 2026 EtalasePelemwatu &mdash; Platform Digital Desa Pelemwatu</p>
      </div>
      <div class="col-sm-4 col-lg-2">
        <div class="footer-heading">Navigasi</div>
        <ul class="footer-links">
          <li><a href="#hero-desa">Beranda</a></li>
          <li><a href="#profil-desa">Profil Desa</a></li>
          <li><a href="#pengenalan-etalase">Tentang Etalase</a></li>
          <li><a href="#katalog">Katalog Potensi</a></li>
          <li><a href="#layanan">Layanan</a></li>
          <li><a href="#kontak-section">Kontak</a></li>
        </ul>
      </div>
      <div class="col-sm-4 col-lg-3">
        <div class="footer-heading">Layanan</div>
        <ul class="footer-links">
          <li><a href="/daftar-potensi">Pendaftaran Potensi</a></li>
          <li><a href="#katalog">Katalog Potensi</a></li>
          <li><a href="#profil-desa">Informasi Desa</a></li>
          <li><a href="#kontak-section">Kontak Kami</a></li>
        </ul>
      </div>
      <div class="col-sm-4 col-lg-3">
        <div class="footer-heading">Informasi</div>
        <ul class="footer-links">
          <li><a href="#pengenalan-etalase">Tentang EtalasePelemwatu</a></li>
          <li><a href="/daftar-potensi">Daftarkan Potensimu</a></li>
          <li><a href="/admin">Panel Admin</a></li>
        </ul>
        <div class="footer-heading mt-4">Alamat</div>
        <p style="font-size:.82rem;color:rgba(255,255,255,.6);line-height:1.6;">
          {{ $desa['alamat'] }}
        </p>
      </div>
    </div>
    <hr class="footer-divider">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
      <span class="footer-bottom">&copy; 2026 EtalasePelemwatu. Platform Digital Desa Pelemwatu.</span>
      <span class="footer-bottom">Dikelola oleh Pengelola Desa Pelemwatu</span>
    </div>
  </div>
</footer>

<!-- ============================================================
     MODAL DETAIL POTENSI
     ============================================================ -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <div>
          <span class="dm-cat-badge" id="dm-cat">—</span>
          <h5 class="modal-title dm-title" id="dm-nama">Detail Potensi</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body pt-2">

        {{-- Foto --}}
        <div class="dm-foto-wrap" id="dm-foto-wrap">
          <img id="dm-foto" src="" alt="" style="display:none;">
          <div id="dm-foto-placeholder" class="dm-foto-placeholder">
            <i class="bi bi-image" style="font-size:3rem;color:#ccc;"></i>
          </div>
        </div>

        {{-- Info baris --}}
        <div class="dm-grid">
          <div class="dm-field">
            <span class="dm-label">Nama Potensi / Usaha</span>
            <span class="dm-value" id="dm-nama-potensi">—</span>
          </div>
          <div class="dm-field">
            <span class="dm-label">Jenis Usaha</span>
            <span class="dm-value" id="dm-jenis">—</span>
          </div>
          <div class="dm-field">
            <span class="dm-label">Jam Operasional</span>
            <span class="dm-value" id="dm-jam">—</span>
          </div>
          <div class="dm-field">
            <span class="dm-label">Lokasi / Alamat Usaha</span>
            <span class="dm-value" id="dm-lokasi">—</span>
          </div>
          <div class="dm-field">
            <span class="dm-label">WhatsApp</span>
            <span class="dm-value" id="dm-wa">—</span>
          </div>
          <div class="dm-field" id="dm-email-field" style="display:none;">
            <span class="dm-label">Email</span>
            <span class="dm-value" id="dm-email">—</span>
          </div>
        </div>

        <div class="dm-field dm-field-full">
          <span class="dm-label">Deskripsi</span>
          <p class="dm-desc" id="dm-deskripsi">—</p>
        </div>

      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn-outline-custom" data-bs-dismiss="modal">Tutup</button>
        <a href="/daftar-potensi" class="btn-primary-custom">Daftarkan Potensimu</a>
      </div>
    </div>
  </div>
</div>
<!-- Fixed Elements -->
<div id="toastContainer"></div>
<button id="backToTop" title="Kembali ke atas"><i class="bi bi-arrow-up"></i></button>

<!-- ============================================================
     MODAL LOGIN ADMIN
     ============================================================ -->
<div class="modal fade modal-login-admin" id="modalLoginAdmin" tabindex="-1" aria-labelledby="modalLoginAdminLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
    <div class="modal-content">
      <div class="modal-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="login-icon-badge">
            <i class="bi bi-shield-lock-fill"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold mb-0" id="modalLoginAdminLabel" style="color:var(--clr-blue);font-size:1.1rem;">Login Administrator</h5>
            <small class="text-muted" style="font-size:.78rem;">Portal EtalasePelemwatu</small>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="formLoginAdmin" novalidate>
        <div class="modal-body p-4">
          <div id="loginAlertBox" class="alert alert-danger py-2 px-3 mb-3 d-none" style="font-size:.84rem;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <span id="loginAlertMsg">Username atau kata sandi tidak sesuai.</span>
          </div>


          <div class="mb-3">
            <label for="adminUsername" class="form-label fw-semibold" style="color:var(--clr-blue);font-size:.88rem;">Username / Email</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0" style="color:var(--clr-text-light);">
                <i class="bi bi-person"></i>
              </span>
              <input type="text" class="form-control border-start-0" id="adminUsername" placeholder="Masukkan username atau email" required autocomplete="username">
            </div>
          </div>

          <div class="mb-3">
            <label for="adminPassword" class="form-label fw-semibold" style="color:var(--clr-blue);font-size:.88rem;">Kata Sandi</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0" style="color:var(--clr-text-light);">
                <i class="bi bi-key"></i>
              </span>
              <input type="password" class="form-control border-start-0 border-end-0" id="adminPassword" placeholder="Masukkan kata sandi" required autocomplete="current-password">
              <button class="btn btn-outline-secondary border-start-0" type="button" id="btnTogglePassword" style="background:#fff;border-color:var(--clr-border);">
                <i class="bi bi-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
          <button type="submit" class="btn-primary-custom w-100 justify-content-center" id="btnSubmitLogin" style="height:44px;">
            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Panel Admin
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Lightbox Modal -->
<div id="lightbox" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.92);
     align-items:center;justify-content:center;padding:1.5rem;" onclick="closeLightbox()">
  <button onclick="closeLightbox();event.stopPropagation()"
          style="position:absolute;top:1rem;right:1.25rem;background:none;border:none;color:#fff;font-size:2rem;cursor:pointer;line-height:1;">
    <i class="bi bi-x-lg"></i>
  </button>
  <button id="lbPrev" onclick="shiftLightbox(-1);event.stopPropagation()"
          style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.12);border:none;
                 color:#fff;font-size:1.8rem;cursor:pointer;border-radius:50%;width:48px;height:48px;">
    <i class="bi bi-chevron-left"></i>
  </button>
  <div style="max-width:900px;width:100%;text-align:center;" onclick="event.stopPropagation()">
    <img id="lbImg" src="" alt="" style="max-width:100%;max-height:80vh;border-radius:10px;object-fit:contain;">
    <p id="lbCaption" style="color:rgba(255,255,255,.8);margin-top:.85rem;font-size:.95rem;"></p>
  </div>
  <button id="lbNext" onclick="shiftLightbox(1);event.stopPropagation()"
          style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.12);border:none;
                 color:#fff;font-size:1.8rem;cursor:pointer;border-radius:50%;width:48px;height:48px;">
    <i class="bi bi-chevron-right"></i>
  </button>
</div>

<script src="assets/js/main.js"></script>
<script src="assets/js/auth.js"></script>
<script>window.APP_URL = "{{ rtrim(config('app.url'), '/') }}";</script>
<script src="assets/js/katalog-slider.js"></script>
<script>
  // Animasi scroll: elemen .reveal muncul saat masuk layar
  (function () {
    var els = document.querySelectorAll('.reveal');
    if (!('IntersectionObserver' in window) || !els.length) {
      document.documentElement.classList.remove('js-anim');
      return;
    }
    els.forEach(function (el) {
      var col = el.closest('[class*="col-"]');
      if (col && col.parentElement) {
        var i = Array.prototype.indexOf.call(col.parentElement.children, col);
        el.style.setProperty('--d', Math.min(i, 4) * 0.1 + 's');
      }
    });
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    els.forEach(function (el) { io.observe(el); });
  })();

  // Navbar: lebih ramping dan berbayang saat halaman di-scroll
  (function () {
    var nav = document.getElementById('mainNavbar');
    var hdr = document.getElementById('siteHeader');
    function onScroll() {
      var s = window.scrollY > 40;
      if (nav) nav.classList.toggle('scrolled', s);
      if (hdr) hdr.classList.toggle('scrolled', s);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  })();

  // Statistik UMKM terdaftar dari endpoint publik /api/stats/public
  (function () {
    var el = document.getElementById('statUMKM');
    if (!el) return;
    fetch('/api/stats/public')
      .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(function (res) {
        var count = (res && res.data && typeof res.data.umkm !== 'undefined')
          ? res.data.umkm
          : (res && typeof res.umkm !== 'undefined' ? res.umkm : null);
        el.textContent = (count !== null) ? count : '—';
      })
      .catch(function () {
        el.textContent = '—';
      });
  })();

  /* ── Lightbox Galeri ── */
  var lbItems = [];
  var lbIndex = 0;

  function openLightbox(items, idx) {
    lbItems = items;
    lbIndex = idx;
    renderLightbox();
    var lb = document.getElementById('lightbox');
    lb.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
  function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
    document.body.style.overflow = '';
  }
  function shiftLightbox(dir) {
    lbIndex = (lbIndex + dir + lbItems.length) % lbItems.length;
    renderLightbox();
  }
  function escapeHtml(s) {
    if (s == null) return '';
    return String(s)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }

  function renderLightbox() {
    var cur = lbItems[lbIndex] || {};
    var cap = escapeHtml(cur.caption || '');
    var desc = cur.desc ? escapeHtml(cur.desc) : '';
    document.getElementById('lbImg').src       = (window.sanitizeSrc ? window.sanitizeSrc(cur.src) : (cur.src || ''));
    document.getElementById('lbImg').alt       = cur.caption || '';
    document.getElementById('lbCaption').innerHTML =
      '<strong style="font-size:1rem;">' + cap + '</strong>' +
      (desc ? '<br><span style="font-size:.85rem;opacity:.75;">' + desc + '</span>' : '');
    document.getElementById('lbPrev').style.display = lbItems.length > 1 ? '' : 'none';
    document.getElementById('lbNext').style.display = lbItems.length > 1 ? '' : 'none';
  }

  window.openLightbox = openLightbox;
  window.closeLightbox = closeLightbox;
  window.shiftLightbox = shiftLightbox;

  /* Bind galeri items */
  function bindGaleri() {
    var items = Array.from(document.querySelectorAll('.gs-card, #galeriGrid .galeri-card'));
    var data  = items.map(function(el) {
      return {
        src:     el.dataset.src,
        caption: el.dataset.caption,
        desc:    el.dataset.desc || ''
      };
    });
    return data;
  }
  bindGaleri();

  /* Render dinamis dari GET /api/galeri */
  function fetchAndRenderGaleri() {
    fetch('/api/galeri')
      .then(function(r){ return r.json(); })
      .then(function(res){
        if (!res || !Array.isArray(res.data)) return;
        if (typeof window.updateGaleriSlider === 'function') {
          window.updateGaleriSlider(res.data);
        }
      })
      .catch(function(){});
  }

  var tabGaleriBtn = document.querySelector('[data-tab="tab-galeri"]');
  if (tabGaleriBtn) tabGaleriBtn.addEventListener('click', fetchAndRenderGaleri);

  /* Keyboard nav */
  document.addEventListener('keydown', function(e) {
    var lb = document.getElementById('lightbox');
    if (!lb || lb.style.display === 'none') return;
    if (e.key === 'Escape')     closeLightbox();
    if (e.key === 'ArrowLeft')  shiftLightbox(-1);
    if (e.key === 'ArrowRight') shiftLightbox(1);
  });
</script>
<script src="assets/js/utils.js"></script>
<script src="assets/js/warga-auth.js"></script>
<script src="assets/js/site-logo.js"></script>
<script src="assets/js/galeri-slider.js"></script>
</body>
</html>