<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Admin — EtalasePelemwatu</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/admin.css">
  <link rel="stylesheet" href="assets/css/galeri.css">
  <script>
    (function () {
      var savedTheme = localStorage.getItem('ep_admin_theme') || 'light';
      document.documentElement.setAttribute('data-theme', savedTheme);

      function toLogin() {
        ['adminLoggedIn', 'adminUser', 'ep_admin_session'].forEach(function (k) { localStorage.removeItem(k); });
        sessionStorage.removeItem('ep_admin_session');
        window.location.replace('/login?unauthorized=true');
      }
      var token = null;
      try {
        var raw = localStorage.getItem('ep_admin_session') || sessionStorage.getItem('ep_admin_session');
        if (raw) token = JSON.parse(raw).token || null;
      } catch (e) {}
      if (localStorage.getItem('adminLoggedIn') !== 'true' || !token) { toLogin(); return; }
      // Flag di localStorage bisa dipalsukan; sesi sebenarnya harus disahkan server.
      fetch('/api/auth/verify', { headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token } })
        .then(function (r) { if (r.status === 401) toLogin(); })
        .catch(function () { /* server tidak terjangkau: biarkan, tiap aksi tetap dicek server */ });
    })();
  </script>
  <style>
    /* ── Pengaturan Menu Rows ── */
    .peng-menu-row {
      width:100%;display:flex;align-items:center;gap:.85rem;padding:.85rem 1rem;
      background:none;border:none;cursor:pointer;text-align:left;transition:background .15s;
      border-radius:0;
    }
    .peng-menu-row:hover { background:var(--adm-pill-bg, rgba(9,74,116,.05)); }
    .peng-menu-icon {
      width:36px;height:36px;border-radius:9px;display:flex;align-items:center;
      justify-content:center;font-size:1rem;flex-shrink:0;
    }
    .peng-menu-label { flex:1;font-size:.9rem;font-weight:600;color:var(--adm-text-title, #094a74); }
    .peng-menu-arrow { font-size:.75rem;color:var(--adm-text-light, #aaa); }
    .peng-menu-divider { height:1px;background:var(--adm-card-border, #e5e7eb);margin:0 1rem; }
    /* Password eye toggle */
    .peng-eye-btn {
      position:absolute;right:.75rem;top:50%;transform:translateY(-50%);
      background:none;border:none;color:var(--adm-text-light, #aaa);cursor:pointer;padding:.25rem .4rem;font-size:1rem;line-height:1;
    }
    .peng-eye-btn:hover { color:var(--clr-orange, #f2630f); }
    /* Password strength bar */
    .peng-strength-bar { display:flex;gap:4px;margin-top:.5rem; }
    .peng-strength-seg { flex:1;height:5px;border-radius:3px;background:var(--adm-card-border, #e5e7eb);transition:background .3s; }
    /* Modal Profil Admin layout */
    .mp-left { display:flex;flex-direction:column;align-items:center;gap:.5rem;min-width:120px; }
    .mp-avatar-wrap { position:relative;width:90px;height:90px; }
    .mp-avatar-circle {
      width:90px;height:90px;border-radius:50%;background:var(--adm-pill-bg, #f4f7fb);
      border:2px solid var(--adm-card-border, #e5e7eb);display:flex;align-items:center;
      justify-content:center;overflow:hidden;
    }
    .mp-avatar-plus {
      position:absolute;bottom:0;right:0;width:26px;height:26px;border-radius:50%;
      background:var(--clr-orange, #f2630f);border:2px solid var(--adm-card-bg, #fff);color:#fff;
      font-size:.75rem;display:flex;align-items:center;justify-content:center;cursor:pointer;
      padding:0;
    }
    .mp-divider { width:1px;background:var(--adm-card-border, #e5e7eb);align-self:stretch;min-height:140px;flex-shrink:0; }
  </style>
</head>
<body class="admin-page">

<div id="sidebarOverlay" class="sidebar-overlay"></div>

<!-- ============================================================  SIDEBAR  -->
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo">
      <div class="sidebar-logo-text">
        <span class="site-name">EtalasePelemwatu</span>
        <span class="site-sub">Panel Admin</span>
      </div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="sidebar-nav-section">MENU UTAMA</div>
    <button class="sidebar-nav-item active" data-section="dashboard">
      <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </button>
    <button class="sidebar-nav-item" data-section="kelola-potensi">
      <i class="bi bi-grid"></i><span>Kelola Potensi</span><span class="nav-badge" id="potensiBadge"></span>
    </button>
    <button class="sidebar-nav-item" data-section="verifikasi">
      <i class="bi bi-clipboard-check"></i><span>Verifikasi</span>
      <span class="nav-badge" id="verifikasiBadge"></span>
    </button>
    <button class="sidebar-nav-item" data-section="permintaan-hapus">
      <i class="bi bi-trash"></i><span>Permintaan Hapus</span>
      <span class="nav-badge" id="deletionBadge" style="display:none;"></span>
    </button>
    <div class="sidebar-nav-section">DATA DESA</div>
    <button class="sidebar-nav-item" data-section="galeri">
      <i class="bi bi-images"></i><span>Galeri Kegiatan</span>
    </button>
    <button class="sidebar-nav-item" data-section="perangkat">
      <i class="bi bi-person-badge"></i><span>Perangkat Desa</span>
    </button>
    <button class="sidebar-nav-item" data-section="profil-desa-admin">
      <i class="bi bi-info-circle"></i><span>Profil Desa</span>
    </button>
    <div class="sidebar-nav-section">PENGATURAN</div>
    <button class="sidebar-nav-item" data-section="pengaturan">
      <i class="bi bi-gear"></i><span>Pengaturan</span>
    </button>
    <a href="/" class="sidebar-nav-item" style="text-decoration:none;">
      <i class="bi bi-house-door"></i><span>Kembali ke Website</span>
    </a>
    <button type="button" class="sidebar-nav-item text-danger border-0 bg-transparent w-100 text-start" id="sidebarNavLogout" style="cursor:pointer;">
      <i class="bi bi-box-arrow-right text-danger"></i><span class="text-danger fw-semibold">Keluar (Logout)</span>
    </button>
  </nav>
  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="sidebar-user-avatar">A</div>
      <div class="sidebar-user-info">
        <span class="uname">Administrator</span>
        <span class="urole">Admin Desa</span>
      </div>
      <button type="button" class="btn-logout-sidebar" id="btnLogoutSidebar" title="Keluar / Logout">
        <i class="bi bi-box-arrow-right"></i>
      </button>
    </div>
  </div>
</aside>

<!-- ============================================================  MAIN  -->
<div class="admin-main" id="adminMain">
  <div class="admin-topbar">
    <div class="admin-topbar-left">
      <button class="btn p-1" id="sidebarToggle"
              style="font-size:1.3rem;border:none;background:none;color:var(--adm-text-title);"
              title="Buka / Tutup Menu">
        <i class="bi bi-list"></i>
      </button>
      <div class="admin-header-greeting">
        <h1 class="admin-header-title">
          Welcome, <span id="adminGreetingName">Admin</span>
        </h1>
        <p class="admin-header-subtitle">
          Panel Kendali &bull; EtalasePelemwatu
        </p>
        <span id="adminPageTitle" class="d-none">Dashboard</span>
      </div>
    </div>
    <div class="admin-topbar-right">
      <div class="admin-topbar-capsule">
        <!-- Quick Search Trigger -->
        <button type="button" class="admin-tool-btn" id="btnSearchTopbar" title="Cari data" onclick="var s = document.getElementById('searchPotensi'); if(s && s.offsetParent !== null){ s.focus(); } else { switchSection('kelola-potensi'); setTimeout(function(){ var inp = document.getElementById('searchPotensi'); if(inp) inp.focus(); }, 200); }">
          <i class="bi bi-search"></i>
        </button>

        <!-- Dark / Light Mode Toggle -->
        <button type="button" class="admin-tool-btn" id="themeToggleBtn" title="Ganti Mode Tampilan (Terang / Gelap)" aria-label="Toggle Dark / Light Mode">
          <i class="bi bi-moon-stars" id="themeToggleIcon"></i>
        </button>

        <!-- Notification Bell -->
        <div class="position-relative" id="notifBellWrap">
          <button id="btnNotifBell" class="admin-tool-btn position-relative" title="Notifikasi" aria-expanded="false">
            <i class="bi bi-bell"></i>
            <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill"
                  style="background:var(--clr-orange);font-size:.6rem;">0</span>
          </button>

          <!-- Panel Notifikasi -->
          <div id="notifPanel"
               style="display:none;position:absolute;top:calc(100% + 10px);right:0;width:320px;
                      background:var(--adm-card-bg,#fff);border:1px solid var(--adm-card-border,#e0e7ef);border-radius:16px;
                      box-shadow:var(--adm-card-shadow-hover,0 12px 32px rgba(9,74,116,.15));z-index:9999;">
            <div style="display:flex;align-items:center;justify-content:space-between;
                        padding:.85rem 1.1rem .7rem;border-bottom:1px solid var(--adm-card-border,#f0f4f8);">
              <span style="font-weight:700;font-size:.92rem;color:var(--adm-text-title,var(--clr-blue));">
                <i class="bi bi-bell-fill me-1" style="color:var(--clr-orange);"></i>Notifikasi
              </span>
              <button id="btnNotifTandaiSemua"
                      style="font-size:.75rem;color:var(--clr-orange);background:none;border:none;
                             padding:0;cursor:pointer;font-weight:600;">
                Tandai semua dibaca
              </button>
            </div>
            <ul id="notifList" style="list-style:none;margin:0;padding:.4rem 0;max-height:320px;overflow-y:auto;">
              <li id="notifLoadingRow" style="padding:.85rem 1rem;text-align:center;font-size:.82rem;color:var(--adm-text-muted,#6b7a8d);">Memuat notifikasi…</li>
              <li style="padding:.65rem 1rem;text-align:center;">
                <button id="btnNotifLihatSemua"
                        style="font-size:.8rem;color:var(--adm-text-title,var(--clr-blue));background:none;border:none;
                               cursor:pointer;font-weight:600;padding:0;">
                  Lihat semua di Verifikasi &rarr;
                </button>
              </li>
            </ul>
          </div>
        </div>

        <!-- Date Capsule Pill -->
        <div class="admin-date-pill d-none d-lg-flex" id="topbarDate">
          <i class="bi bi-calendar3"></i> <span></span>
        </div>

        <!-- Admin Profile Pill Dropdown -->
        <div class="dropdown">
          <button class="admin-profile-pill dropdown-toggle" type="button" id="adminProfileMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="admin-profile-avatar">
              <span id="adminAvatarInitial">A</span>
            </div>
            <span class="d-none d-md-inline" id="adminTopName">Admin</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end admin-dropdown-menu" aria-labelledby="adminProfileMenuBtn">
            <li class="dropdown-header">Akun Administrator</li>
            <li><button type="button" class="dropdown-item" onclick="openProfilModal()"><i class="bi bi-person-gear me-2"></i>Profil Admin</button></li>
            <li><button type="button" class="dropdown-item" onclick="switchSection('pengaturan')"><i class="bi bi-shield-lock me-2"></i>Keamanan &amp; Sandi</button></li>
            <li><a class="dropdown-item" href="/"><i class="bi bi-house me-2"></i>Lihat Website</a></li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
              <button type="button" class="dropdown-item text-danger" id="btnLogoutTopbar">
                <i class="bi bi-box-arrow-right me-2"></i>Keluar (Logout)
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Sub-navigation Horizontal Pill Tabs (Matching Reference) -->
  <div class="admin-quick-tabs">
    <button type="button" class="admin-tab-pill active" data-tab-section="dashboard">
      <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
    </button>
    <button type="button" class="admin-tab-pill" data-tab-section="kelola-potensi">
      <i class="bi bi-grid"></i> <span>Kelola Potensi</span>
    </button>
    <button type="button" class="admin-tab-pill" data-tab-section="verifikasi">
      <i class="bi bi-clipboard-check"></i> <span>Verifikasi</span>
    </button>
    <button type="button" class="admin-tab-pill" data-tab-section="permintaan-hapus">
      <i class="bi bi-trash"></i> <span>Permintaan Hapus</span>
    </button>
    <button type="button" class="admin-tab-pill" data-tab-section="galeri">
      <i class="bi bi-images"></i> <span>Galeri</span>
    </button>
    <button type="button" class="admin-tab-pill" data-tab-section="perangkat">
      <i class="bi bi-person-badge"></i> <span>Perangkat Desa</span>
    </button>
    <button type="button" class="admin-tab-pill" data-tab-section="pengaturan">
      <i class="bi bi-gear"></i> <span>Pengaturan</span>
    </button>
  </div>

  <div class="admin-content">

    <!-- ─── DASHBOARD ─── -->
    <div class="admin-section active" id="section-dashboard">
      <div class="admin-page-header">
        <h2>Dashboard</h2>
        <p>Selamat datang di Panel Admin EtalasePelemwatu. Pantau dan kelola data potensi lokal Desa Pelemwatu.</p>
      </div>
      <!-- KPI Row (Harmonious equal-height cards matching homepage colors) -->
      <div class="row g-3 mb-4 kpi-row">
        <div class="col-sm-6 col-xl-3">
          <div class="kpi-card">
            <div class="kpi-icon-wrap icon-orange">
              <i class="bi bi-grid"></i>
            </div>
            <div class="kpi-data">
              <div class="kpi-label">Total Potensi</div>
              <div class="kpi-num" id="kpiTotal">–</div>
              <div class="kpi-trend">Semua usaha terdaftar</div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="kpi-card">
            <div class="kpi-icon-wrap icon-yellow">
              <i class="bi bi-clock-history"></i>
            </div>
            <div class="kpi-data">
              <div class="kpi-label">Menunggu Verifikasi</div>
              <div class="kpi-num" id="kpiPending">–</div>
              <div class="kpi-trend" style="color:var(--clr-yellow,#FFC20B);">Perlu ditinjau</div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="kpi-card">
            <div class="kpi-icon-wrap icon-green">
              <i class="bi bi-check-circle"></i>
            </div>
            <div class="kpi-data">
              <div class="kpi-label">Terverifikasi</div>
              <div class="kpi-num" id="kpiVerified">–</div>
              <div class="kpi-trend up"><i class="bi bi-arrow-up-short"></i> Aktif ditampilkan</div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="kpi-card">
            <div class="kpi-icon-wrap icon-blue">
              <i class="bi bi-tags"></i>
            </div>
            <div class="kpi-data">
              <div class="kpi-label">Kategori Usaha</div>
              <div class="kpi-num" id="kpiCategories">4</div>
              <div class="kpi-trend">Kuliner, Jasa, dsb.</div>
            </div>
          </div>
        </div>
      </div><!-- /kpi row -->

      <div class="row g-4 align-items-stretch">
        <div class="col-lg-8">
          <div class="admin-card h-100">
            <div class="admin-card-header">
              <h5><i class="bi bi-clock-history me-2" style="color:var(--clr-orange);"></i>Pendaftaran Terbaru</h5>
              <button class="btn btn-sm btn-outline-secondary" id="btnLihatSemua">
                Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
              </button>
            </div>
            <div class="table-responsive">
              <table class="table admin-table mb-0">
                <thead>
                  <tr><th>No</th><th>Nama Potensi</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody id="dashboardRecentTbody">
                  <tr data-name="Omah Dimsum" data-cat="Usaha &amp; Produk" data-date="28 Okt 2024" data-status="Aktif" data-owner="Ibu Siti" data-wa="081234567890" data-address="Dsn. Pelemwatu RT 02 RW 01" data-desc="Menyediakan berbagai varian olahan dimsum lezat, halal, higienis, dan terjangkau untuk seluruh keluarga.">
                    <td>1</td><td><strong>Omah Dimsum</strong></td>
                    <td><span class="potensi-badge badge-usaha">Usaha &amp; Produk</span></td>
                    <td>28 Okt 2024</td>
                    <td><span class="status-badge status-aktif">Aktif</span></td>
                    <td class="text-end"><button type="button" class="btn btn-sm btn-action btn-lihat-db" title="Lihat Detail Potensi"><i class="bi bi-eye"></i></button></td>
                  </tr>
                  <tr data-name="Moni Laundry 24 Jam" data-cat="Jasa" data-date="25 Okt 2024" data-status="Aktif" data-owner="Bpk. Budi" data-wa="082345678901" data-address="Jl. Raya Pelemwatu No. 15" data-desc="Layanan pencucian pakaian cepat, bersih, wangi, dengan peralatan modern buka 24 jam nonstop.">
                    <td>2</td><td><strong>Moni Laundry 24 Jam</strong></td>
                    <td><span class="potensi-badge badge-jasa">Jasa</span></td>
                    <td>25 Okt 2024</td>
                    <td><span class="status-badge status-aktif">Aktif</span></td>
                    <td class="text-end"><button type="button" class="btn btn-sm btn-action btn-lihat-db" title="Lihat Detail Potensi"><i class="bi bi-eye"></i></button></td>
                  </tr>
                  <tr data-name="My Kost" data-cat="Hunian" data-date="20 Okt 2024" data-status="Aktif" data-owner="Ibu Rahayu" data-wa="083456789012" data-address="Dsn. Pelemwatu RT 05 RW 02" data-desc="Hunian kost eksklusif, aman, bersih, kamar mandi dalam, kasur empuk, wifi super cepat, parkir motor luas.">
                    <td>3</td><td><strong>My Kost</strong></td>
                    <td><span class="potensi-badge badge-hunian">Hunian</span></td>
                    <td>20 Okt 2024</td>
                    <td><span class="status-badge status-aktif">Aktif</span></td>
                    <td class="text-end"><button type="button" class="btn btn-sm btn-action btn-lihat-db" title="Lihat Detail Potensi"><i class="bi bi-eye"></i></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="admin-card h-100">
            <div class="admin-card-header">
              <h5><i class="bi bi-lightning me-2" style="color:var(--clr-orange);"></i>Aksi Cepat</h5>
            </div>
            <div class="admin-card-body d-flex flex-column gap-2">
              <a href="/daftar-potensi" class="quick-action">
                <i class="bi bi-plus-circle"></i><span>Tambah Potensi</span>
              </a>
              <button class="quick-action" id="btnExport">
                <i class="bi bi-download"></i><span>Export Data</span>
              </button>
              <button class="quick-action" id="btnGoSettings">
                <i class="bi bi-gear"></i><span>Pengaturan</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /dashboard -->

    <!-- â”€â”€â”€ KELOLA POTENSI â”€â”€â”€ -->
    <div class="admin-section" id="section-kelola-potensi">
      <div class="admin-page-header">
        <h2>Kelola Potensi</h2>
        <p>Daftar seluruh potensi lokal yang terdaftar di EtalasePelemwatu.</p>
      </div>
      <div class="admin-card">
        <div class="admin-card-header">
          <h5><i class="bi bi-grid me-2" style="color:var(--clr-orange);"></i>Daftar Potensi</h5>
          <div class="potensi-toolbar">
            <div class="admin-search-bar">
              <input type="text" placeholder="Cari nama potensi..." id="searchPotensi">
              <select class="form-select form-select-sm" style="width:auto;" id="filterKategori" hidden tabindex="-1" aria-hidden="true">
                <option value="all">Semua Kategori</option>
                <option value="usaha">Usaha &amp; Produk</option>
                <option value="jasa">Jasa</option>
                <option value="hunian">Hunian</option>
                <option value="barang">Barang Warga</option>
              </select>
              <div class="kat-dropdown" id="katDropdown">
                <button type="button" class="kat-dropdown-toggle"
                        id="katDropdownToggle" aria-haspopup="listbox"
                        aria-expanded="false" aria-label="Filter kategori">
                  <span class="kat-dropdown-label">Semua Kategori</span>
                  <i class="bi bi-chevron-down kat-dropdown-caret"></i>
                </button>
                <ul class="kat-dropdown-menu" id="katDropdownMenu" role="listbox"
                    hidden></ul>
              </div>
            </div>
            <a href="/daftar-potensi" class="btn-tambah-potensi">
              <i class="bi bi-plus-lg"></i> Tambah Potensi
            </a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table admin-table mb-0" id="tabelPotensi">
            <thead>
              <tr><th>No</th><th>Nama Potensi</th><th>Kategori</th><th>Status</th><th>Tgl Daftar</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
              <tr class="empty-row text-center"><td colspan="6" class="py-4 text-muted">Memuat data...</td></tr>
            </tbody>
          </table>
        </div>
        <div class="admin-card-body pt-2 pb-3">
          <div class="admin-pagination">
            <!-- pagination buttons rendered by JS -->
          </div>
        </div>
      </div>
    </div><!-- /kelola-potensi -->

    <!-- â”€â”€â”€ VERIFIKASI â”€â”€â”€ -->
    <div class="admin-section" id="section-verifikasi">
      <div class="admin-page-header">
        <h2>Verifikasi Pendaftaran</h2>
        <p>Tinjau dan verifikasi pendaftaran potensi lokal yang masuk dari masyarakat.</p>
      </div>
      <div class="admin-alert warning mb-4" id="warningVerifikasi">
        <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
        <span>Terdapat <strong id="pendingCount">0</strong> pendaftaran yang menunggu verifikasi.</span>
      </div>
      <div id="verifikasiList"></div>
      <div class="text-center py-4" id="verifikasiEmpty" style="display:none;color:var(--text-light);">
        <i class="bi bi-check2-circle fs-1 d-block mb-2"></i>Tidak ada pendaftaran yang menunggu verifikasi.
      </div>
    </div><!-- /verifikasi -->

    <!-- ─── PERMINTAAN HAPUS ─── -->
    <div class="admin-section" id="section-permintaan-hapus">
      <div class="admin-page-header">
        <h2>Permintaan Penghapusan</h2>
        <p>Warga mengajukan permintaan penghapusan untuk potensi yang sudah disetujui. Tinjau dan putuskan.</p>
      </div>
      <div id="deletionRequestList"></div>
      <div class="text-center py-5" id="deletionEmpty" style="display:none;color:var(--text-light);">
        <i class="bi bi-check2-circle fs-1 d-block mb-2"></i>Tidak ada permintaan penghapusan.
      </div>
    </div><!-- /permintaan-hapus -->

    <!-- â”€â”€â”€ GALERI KEGIATAN â”€â”€â”€ -->
    <div class="admin-section" id="section-galeri">
      <div class="admin-page-header">
        <h2>Galeri Kegiatan</h2>
        <p>Kelola foto kegiatan desa yang ditampilkan di halaman utama website.</p>
      </div>
      <div class="admin-card">
        <div class="admin-card-header">
          <h5><i class="bi bi-images me-2" style="color:var(--clr-orange);"></i>Foto Kegiatan</h5>
          <label class="btn btn-sm btn-primary" id="btnTambahFoto"
                 style="background:var(--clr-orange);border-color:var(--clr-orange);cursor:pointer;">
            <i class="bi bi-plus-lg me-1"></i> Tambah Foto
            <input type="file" id="galeriFileInput" accept="image/*" multiple style="display:none;">
          </label>
        </div>
        <div class="admin-card-body">
          <div class="galeri-admin-grid" id="galeriAdminGrid">
            <!-- Tombol tambah foto -->
            <label class="galeri-add-btn" for="galeriFileInput">
              <i class="bi bi-plus-circle"></i>
              <span>Tambah Foto</span>
            </label>
          </div>
        </div>
        <div class="admin-card-body pt-0 pb-3">
          <small style="color:var(--text-light);">
            <i class="bi bi-info-circle me-1"></i>
            Hover pada foto untuk opsi edit kegiatan atau hapus. Klik "Tambah Foto" untuk mengunggah kegiatan baru.
          </small>
        </div>
      </div>

      <!-- Modal Edit Judul Galeri -->
      <div class="modal fade" id="modalEditGaleri" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header" style="border-bottom:1px solid var(--border);">
              <h5 class="modal-title" style="color:var(--clr-blue);">
                <i class="bi bi-pencil me-2" style="color:var(--clr-orange);"></i>Edit Kegiatan Galeri
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" id="editGaleriId">
              <label class="form-label fw-semibold" style="color:var(--clr-blue);">Judul Kegiatan</label><input type="text" class="form-control" id="editGaleriCaption" placeholder="Masukkan judul kegiatan">
              <label class="form-label fw-semibold mt-3" style="color:var(--clr-blue);">Deskripsi Kegiatan</label>
              <textarea class="form-control" id="editGaleriDesc" rows="3" placeholder="Deskripsi singkat kegiatan..."></textarea>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border);">
              <button type="button" class="btn-outline-custom" data-bs-dismiss="modal">Batal</button>
              <button type="button" class="btn-primary-custom" id="btnSimpanJudulGaleri">
                <i class="bi bi-save me-1"></i>Simpan
              </button>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /galeri -->

    <!-- Modal Edit Potensi -->
    <div class="modal fade" id="modalEditPotensi" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header" style="border-bottom:1px solid var(--border);">
            <h5 class="modal-title" style="color:var(--clr-blue);">
              <i class="bi bi-pencil me-2" style="color:var(--clr-orange);"></i>Edit Potensi
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="editPotensiId">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Nama Potensi <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="editPotensiName" placeholder="Nama potensi/usaha" maxlength="100">
                <div class="invalid-feedback" id="editPotensiNameErr"></div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Nama Pemilik <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="editPotensiOwner" placeholder="Nama lengkap pemilik" maxlength="100">
                <div class="invalid-feedback" id="editPotensiOwnerErr"></div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Kategori</label>
                <select class="form-control" id="editPotensiCategory">
                  <option value="">-- Pilih Kategori --</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Nomor WhatsApp <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="editPotensiWa" placeholder="08xxxxxxxxxx" maxlength="20">
                <div class="form-text text-muted" style="font-size: .8rem;">Nomor ini akan ditampilkan di halaman publik agar pembeli dapat menghubungi Anda.</div>
                <div class="invalid-feedback" id="editPotensiWaErr"></div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Email</label>
                <input type="text" class="form-control" id="editPotensiEmail" placeholder="opsional" maxlength="100">
                <div class="invalid-feedback" id="editPotensiEmailErr"></div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Status</label>
                <select class="form-control" id="editPotensiStatus">
                  <option value="approved">Aktif / Disetujui</option>
                  <option value="pending">Menunggu</option>
                  <option value="rejected">Ditolak</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Alamat <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="editPotensiAddress" placeholder="Alamat lengkap" maxlength="255">
                <div class="invalid-feedback" id="editPotensiAddressErr"></div>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Deskripsi <span class="text-danger">*</span></label>
                <textarea class="form-control" id="editPotensiDesc" rows="3" placeholder="Deskripsi potensi..." maxlength="1000"></textarea>
                <div class="invalid-feedback" id="editPotensiDescErr"></div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Lokasi</label>
                <input type="text" class="form-control" id="editPotensiLocation" placeholder="opsional" maxlength="255">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Jenis</label>
                <input type="text" class="form-control" id="editPotensiJenis" placeholder="opsional" maxlength="100">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Jam Operasional</label>
                <input type="text" class="form-control" id="editPotensiJam" placeholder="opsional" maxlength="100">
              </div>
            </div>
          </div>
          <div class="modal-footer" style="border-top:1px solid var(--border);">
            <button type="button" class="btn-outline-custom" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn-primary-custom" id="btnSimpanPotensi">
              <i class="bi bi-save me-1"></i>Simpan
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- /modal edit potensi -->

    <!-- Modal Detail Potensi Admin -->
    <div class="modal fade" id="modalDetailPotensiAdmin" tabindex="-1" aria-labelledby="modalDetailPotensiAdminLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <div style="width:36px;height:36px;border-radius:10px;background:rgba(9,74,116,0.1);color:var(--clr-blue);display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                <i class="bi bi-eye-fill"></i>
              </div>
              <div>
                <h5 class="modal-title mb-0 fw-bold" id="modalDetailPotensiAdminLabel" style="font-size:1.05rem;">Detail Potensi Desa</h5>
                <small class="text-muted" style="font-size:.8rem;">Informasi lengkap potensi dan data pemilik</small>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body p-4">
            <!-- Banner / Status Bar -->
            <div class="adm-detail-banner mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
              <div class="d-flex align-items-center gap-2">
                <span class="potensi-badge badge-usaha" id="admDetailCat">-</span>
                <span class="status-badge status-aktif" id="admDetailStatus">Aktif</span>
              </div>
              <small class="text-muted fw-semibold" id="admDetailDate"><i class="bi bi-calendar3 me-1"></i> -</small>
            </div>

            <!-- Foto Potensi Preview -->
            <div class="mb-4" id="admDetailPhotoWrap">
              <label class="adm-detail-label mb-2"><i class="bi bi-images me-1"></i> Foto Potensi</label>
              <div id="admDetailPhotoList" class="d-flex gap-2 flex-wrap align-items-center">
                <!-- dynamic photos -->
              </div>
            </div>

            <!-- Grid Info -->
            <div class="row g-3">
              <div class="col-md-6">
                <div class="adm-detail-box h-100">
                  <span class="adm-detail-label">Nama Potensi / Usaha</span>
                  <div class="adm-detail-value" id="admDetailName">-</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="adm-detail-box h-100">
                  <span class="adm-detail-label">Nama Pemilik / Warga</span>
                  <div class="adm-detail-value" id="admDetailOwner">-</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="adm-detail-box h-100">
                  <span class="adm-detail-label">Kontak WhatsApp</span>
                  <div class="adm-detail-value" id="admDetailWa">-</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="adm-detail-box h-100">
                  <span class="adm-detail-label">Email</span>
                  <div class="adm-detail-value text-break" id="admDetailEmail">-</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="adm-detail-box h-100">
                  <span class="adm-detail-label">Jenis Usaha</span>
                  <div class="adm-detail-value" id="admDetailJenis">-</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="adm-detail-box h-100">
                  <span class="adm-detail-label">Jam Operasional</span>
                  <div class="adm-detail-value" id="admDetailJam">-</div>
                </div>
              </div>
              <div class="col-12">
                <div class="adm-detail-box">
                  <span class="adm-detail-label">Alamat &amp; Lokasi</span>
                  <div class="adm-detail-value" id="admDetailAddress">-</div>
                  <div id="admDetailLocation" class="text-muted mt-1 small" style="display:none;"></div>
                </div>
              </div>
              <div class="col-12">
                <div class="adm-detail-box">
                  <span class="adm-detail-label">Deskripsi Potensi</span>
                  <p class="adm-detail-value fw-normal mb-0 text-break" style="white-space:pre-line;line-height:1.6;" id="admDetailDesc">-</p>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:50px;padding:.45rem 1.25rem;">Tutup</button>
            <button type="button" class="btn btn-warning text-dark fw-bold" id="btnEditFromDetail" style="border-radius:50px;padding:.45rem 1.35rem;background:#FFC20B;border:none;">
              <i class="bi bi-pencil me-1"></i> Edit Potensi
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- /modal detail potensi admin -->

    <!-- â”€â”€â”€ PERANGKAT â”€â”€â”€ -->
    <div class="admin-section" id="section-perangkat">
      <div class="admin-page-header"><h2>Perangkat Desa</h2><p>Data perangkat Desa Pelemwatu.</p></div>
      <div class="admin-card">
        <div class="admin-card-body text-center py-5">
          <div style="width:80px;height:80px;background:rgba(9,74,116,.07);border-radius:50%;
                      display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
            <i class="bi bi-person-badge" style="font-size:2rem;color:var(--clr-blue);"></i>
          </div>
          <h5 style="color:var(--clr-blue);">Data Belum Tersedia</h5>
          <p style="color:var(--text-mid);max-width:420px;margin:0 auto 1.5rem;">
            Informasi perangkat desa akan ditambahkan setelah data resmi tersedia dari pemerintah Desa Pelemwatu.
            Data tidak akan dibuat tanpa sumber resmi yang dapat diverifikasi.
          </p>
          <button class="btn-primary-custom" id="btnHubungiPengelola">
            <i class="bi bi-envelope"></i> Hubungi Pengelola
          </button>
        </div>
      </div>
    </div>

    <!-- â”€â”€â”€ PROFIL DESA â”€â”€â”€ -->
    <div class="admin-section" id="section-profil-desa-admin">
      <div class="admin-page-header"><h2>Profil Desa</h2><p>Kelola informasi profil Desa Pelemwatu yang ditampilkan di website.</p></div>
      <div class="admin-card">
        <div class="admin-card-header d-flex justify-content-between align-items-center">
          <h5><i class="bi bi-building me-2" style="color:var(--clr-orange);"></i>Data Profil Desa</h5>
          <div class="d-flex align-items-center gap-2">
            <a href="{{ url('/#kontak-section') }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" style="font-size:.8rem;">
              <i class="bi bi-box-arrow-up-right"></i> Lihat di website
            </a>
            <span class="status-badge status-aktif" id="badgeStatusProfil">Data Tersimpan</span>
          </div>
        </div>
        <div class="admin-card-body">
          <form id="formProfilDesa" onsubmit="return false;">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Nama Desa</label>
                <input type="text" class="form-control" value="Pelemwatu" readonly style="background:var(--clr-bg);">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Kecamatan</label>
                <input type="text" class="form-control" value="Menganti" readonly style="background:var(--clr-bg);">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Kabupaten</label>
                <input type="text" class="form-control" value="Gresik" readonly style="background:var(--clr-bg);">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Provinsi</label>
                <input type="text" class="form-control" value="Jawa Timur" readonly style="background:var(--clr-bg);">
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Alamat Lengkap</label>
                <input type="text" class="form-control" id="admDesaAlamat" name="desa_alamat"
                       placeholder="Jl. Raya Pelemwatu No. 128, Kecamatan Menganti, Kabupaten Gresik, Jawa Timur">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Kode Pos</label>
                <input type="text" class="form-control" id="admDesaKodePos" name="desa_kode_pos" placeholder="Contoh: 61174" maxlength="5">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Telepon Kantor</label>
                <input type="tel" class="form-control" id="admDesaTelepon" name="desa_telepon" placeholder="Nomor telepon kantor">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Email Resmi</label>
                <input type="email" class="form-control" id="admDesaEmail" name="desa_email" placeholder="info@etalasepelemwatu.id">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Jam Operasional</label>
                <input type="text" class="form-control" id="admDesaJam" name="desa_jam_operasional" placeholder="Contoh: Senin – Jumat, 08.00 – 16.00 WIB">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">WhatsApp Resmi</label>
                <input type="tel" class="form-control" id="admDesaWa" name="desa_whatsapp" placeholder="Contoh: 0822-4573-0256">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Instagram Resmi</label>
                <input type="text" class="form-control" id="admDesaIg" name="desa_instagram" placeholder="Contoh: official_pelemwatu atau @official_pelemwatu">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Nama Kepala Desa</label>
                <input type="text" class="form-control" id="admDesaKepala" name="desa_kepala" placeholder="Nama kepala desa">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold" style="color:var(--clr-blue);">Jumlah KK</label>
                <input type="number" class="form-control" id="admDesaJmlKK" name="desa_jumlah_kk" min="0" placeholder="Contoh: 850">
              </div>
              <div class="col-12 mt-2">
                <button type="button" class="btn-primary-custom" id="saveProfileBtn">
                  <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
                <button type="button" class="btn-outline-custom ms-2" id="btnBatalProfil">Batal</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div><!-- /profil-desa-admin -->

    <!-- â”€â”€â”€ PENGATURAN â”€â”€â”€ -->
    <div class="admin-section" id="section-pengaturan">
      <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
           HALAMAN UTAMA PENGATURAN
           â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
      <div id="peng-home">
        <div class="admin-page-header"><h2>Pengaturan</h2><p>Konfigurasi platform EtalasePelemwatu.</p></div>

        <!-- Kelompok: Akun -->
        <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-light);padding:.5rem .25rem .4rem;">Akun</div>
        <div class="peng-menu-card">
          <button class="peng-menu-row" id="peng-open-profil">
            <span class="peng-menu-icon" style="background:rgba(242,99,15,.1);color:var(--clr-orange);"><i class="bi bi-person-fill"></i></span>
            <span class="peng-menu-label">Profil Admin</span>
            <i class="bi bi-chevron-right peng-menu-arrow"></i>
          </button>
          <div class="peng-menu-divider"></div>
          <button class="peng-menu-row" id="peng-open-keamanan">
            <span class="peng-menu-icon" style="background:rgba(9,74,116,.1);color:var(--clr-blue);"><i class="bi bi-shield-lock-fill"></i></span>
            <span class="peng-menu-label">Keamanan</span>
            <i class="bi bi-chevron-right peng-menu-arrow"></i>
          </button>
        </div>

        <!-- Kelompok: Tampilan Website -->
        <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-light);padding:.5rem .25rem .4rem;">Tampilan Website</div>
        <div class="peng-menu-card">
          <button class="peng-menu-row" id="peng-open-tampilan">
            <span class="peng-menu-icon" style="background:rgba(104,147,75,.1);color:var(--clr-green);"><i class="bi bi-display-fill"></i></span>
            <span class="peng-menu-label">Tampilan &amp; Maintenance</span>
            <i class="bi bi-chevron-right peng-menu-arrow"></i>
          </button>
          <div class="peng-menu-divider"></div>
          <button class="peng-menu-row" id="peng-open-logo">
            <span class="peng-menu-icon" style="background:rgba(242,99,15,.1);color:var(--clr-orange);"><i class="bi bi-image-fill"></i></span>
            <span class="peng-menu-label">Logo &amp; Identitas</span>
            <i class="bi bi-chevron-right peng-menu-arrow"></i>
          </button>
        </div>

        <!-- Kelompok: Notifikasi -->
        <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-light);padding:.5rem .25rem .4rem;">Notifikasi</div>
        <div class="peng-menu-card">
          <button class="peng-menu-row" id="peng-open-notif">
            <span class="peng-menu-icon" style="background:rgba(255,194,11,.18);color:#b8860b;"><i class="bi bi-bell-fill"></i></span>
            <span class="peng-menu-label">Notifikasi</span>
            <i class="bi bi-chevron-right peng-menu-arrow"></i>
          </button>
        </div>

        <!-- Kelompok: Lainnya -->
        <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-light);padding:.5rem .25rem .4rem;">Lainnya</div>
        <div class="peng-menu-card">
          <button class="peng-menu-row" id="peng-open-tentang">
            <span class="peng-menu-icon" style="background:rgba(9,74,116,.08);color:var(--clr-blue);"><i class="bi bi-info-circle-fill"></i></span>
            <span class="peng-menu-label">Tentang Aplikasi</span>
            <i class="bi bi-chevron-right peng-menu-arrow"></i>
          </button>
          <div class="peng-menu-divider"></div>
          <button class="peng-menu-row" id="peng-btn-keluar">
            <span class="peng-menu-icon" style="background:rgba(176,42,55,.1);color:#b02a37;"><i class="bi bi-box-arrow-right"></i></span>
            <span class="peng-menu-label" style="color:#b02a37;">Keluar</span>
            <i class="bi bi-chevron-right peng-menu-arrow" style="color:#b02a37;"></i>
          </button>
        </div>
      </div><!-- /peng-home -->

      <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
           PANEL KEDUA â€” template dibuat oleh JS
           â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
      <div id="peng-panel" style="display:none;">
        <!-- Header panel -->
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;">
          <button id="peng-back-btn" style="background:none;border:1px solid var(--border);border-radius:8px;padding:.35rem .6rem;color:var(--clr-blue);cursor:pointer;font-size:1rem;display:flex;align-items:center;">
            <i class="bi bi-arrow-left"></i>
          </button>
          <h4 id="peng-panel-title" style="margin:0;font-size:1.05rem;font-weight:700;color:var(--clr-blue);"></h4>
        </div>
        <!-- Konten panel diisi oleh JS -->
        <div id="peng-panel-body"></div>
      </div><!-- /peng-panel -->

      <!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
           KONTEN TERSEMBUNYI UNTUK TIAP PANEL
           â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->

      <!-- TEMPLATE: Tampilan & Maintenance -->
      <div id="tmpl-tampilan" style="display:none;">
        <div class="admin-card" style="padding:1.25rem;">
          <div class="settings-row">
            <div class="settings-row-info"><h6>Mode Maintenance</h6><p>Tampilkan halaman maintenance saat website dalam pemeliharaan.</p></div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="toggleMaintenance"></div>
          </div>
          <div class="settings-row" style="border-bottom:none;">
            <div class="settings-row-info"><h6>Tampilkan Banner Statistik</h6><p>Tampilkan keterangan tambahan pada bagian ringkasan statistik platform.</p></div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="toggleDemo" checked></div>
          </div>
        </div>
      </div>

      <!-- TEMPLATE: Notifikasi -->
      <div id="tmpl-notif" style="display:none;">
        <div class="admin-card" style="padding:1.25rem;">
          <div class="settings-row">
            <div class="settings-row-info"><h6>Notifikasi Pendaftaran Baru</h6><p>Terima notifikasi saat ada pendaftaran potensi baru masuk.</p></div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="toggleNotifDaftar" checked></div>
          </div>
          <div class="settings-row" style="border-bottom:none;">
            <div class="settings-row-info"><h6>Notifikasi Email</h6><p>Kirim notifikasi melalui email saat ada pendaftaran atau perubahan data.</p></div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="toggleNotifEmail"></div>
          </div>
        </div>
      </div>

      <!-- TEMPLATE: Logo & Identitas -->
      <div id="tmpl-logo" style="display:none;">
        <div class="admin-card" style="padding:1.4rem;">

          <!-- Label + keterangan -->
          <div style="margin-bottom:1.1rem;">
            <div style="font-size:.95rem;font-weight:700;color:var(--clr-blue);margin-bottom:.2rem;">Logo Beranda</div>
            <div style="font-size:.8rem;color:var(--text-mid);">Logo ini tampil di navbar dan footer beranda.</div>
          </div>

          <!-- Preview logo saat ini -->
          <div style="margin-bottom:1.25rem;">
            <div style="font-size:.8rem;font-weight:600;color:var(--text-mid);margin-bottom:.5rem;text-transform:uppercase;letter-spacing:.04em;">Logo Saat Ini</div>
            <div style="display:inline-block;padding:8px 14px;border-radius:10px;border:1px solid var(--border,#e5e7ef);background:#f8fafc;">
              <img id="logoCurrentPreview" src="/assets/img/logo-desa.png" alt="Logo saat ini"
                   style="height:56px;object-fit:contain;display:block;">
            </div>
          </div>

          <!-- Dropzone -->
          <div id="logoDropzone"
               style="border:2px dashed var(--border,#c9d6e3);border-radius:12px;padding:2.2rem 1.5rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;background:#fafcff;margin-bottom:1rem;">
            <input type="file" id="logoFileInput" accept="image/jpeg,image/jpg,image/png,image/webp" style="display:none;">
            <div id="logoDropContent">
              <i class="bi bi-image" style="font-size:2rem;color:var(--clr-blue);opacity:.5;display:block;margin-bottom:.6rem;"></i>
              <div style="font-size:.95rem;font-weight:700;color:var(--clr-blue);margin-bottom:.35rem;">Pilih file atau seret ke sini</div>
              <div style="font-size:.8rem;color:var(--text-mid);margin-bottom:1rem;">JPG, PNG, WebP, hingga 10 MB</div>
              <button type="button" id="logoBtnTelusuri"
                      style="display:inline-block;padding:.42rem 1.3rem;border-radius:999px;border:1.5px solid var(--clr-blue,#094a74);background:#fff;color:var(--clr-blue,#094a74);font-size:.85rem;font-weight:600;cursor:pointer;transition:background .15s,color .15s;">
                Telusuri file
              </button>
            </div>
            <!-- Preview setelah file dipilih -->
            <div id="logoPickedWrap" style="display:none;">
              <img id="logoPickedPreview" src="" alt="Preview"
                   style="max-height:120px;border-radius:8px;border:2px solid var(--clr-orange,#f2630f);object-fit:contain;margin-bottom:.75rem;">
              <div style="font-size:.82rem;color:var(--clr-blue);font-weight:600;" id="logoPickedName"></div>
              <div style="font-size:.78rem;color:var(--text-mid);margin-bottom:.6rem;" id="logoPickedSize"></div>
              <button type="button" id="logoBtnHapusPilihan"
                      style="font-size:.8rem;padding:.28rem .9rem;border-radius:999px;border:1.5px solid #ccc;background:#fff;color:#666;cursor:pointer;">
                <i class="bi bi-x-circle me-1"></i>Hapus pilihan
              </button>
            </div>
          </div>

          <!-- Tombol aksi -->
          <div style="display:flex;gap:.7rem;flex-wrap:wrap;">
            <button type="button" id="logoBtnBatalkan" class="btn btn-outline-secondary" style="font-size:.88rem;">
              Batalkan
            </button>
            <button type="button" id="btnSimpanLogo" class="btn-primary-custom" style="font-size:.88rem;" disabled>
              <i class="bi bi-cloud-upload me-1"></i> Simpan Logo
            </button>
          </div>

        </div>
      </div>

      <!-- TEMPLATE: Tentang Aplikasi -->
      <div id="tmpl-tentang" style="display:none;">
        <div class="admin-card" style="padding:1.5rem;text-align:center;">
          <div style="width:72px;height:72px;border-radius:18px;background:linear-gradient(135deg,var(--clr-orange),#ff7b2b);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;box-shadow:0 6px 18px rgba(242,99,15,.3);">
            <i class="bi bi-shop-window" style="font-size:2rem;color:#fff;"></i>
          </div>
          <div style="font-size:1.2rem;font-weight:800;color:var(--clr-blue);margin-bottom:.35rem;">Etalase Pelemwatu</div>
          <div style="font-size:.85rem;color:var(--text-mid);max-width:380px;margin:0 auto 1.25rem;line-height:1.6;">
            Platform digital potensi lokal Desa Pelemwatu, Kecamatan Menganti, Kabupaten Gresik, Jawa Timur. Memperkenalkan dan memasarkan produk, jasa, serta potensi warga desa secara digital.
          </div>
          <div style="display:inline-flex;align-items:center;gap:.5rem;background:var(--clr-bg);border-radius:8px;padding:.5rem 1rem;font-size:.82rem;color:var(--text-mid);">
            <i class="bi bi-patch-check-fill" style="color:var(--clr-green);"></i> Versi 1.0.0 &nbsp;·&nbsp; <i class="bi bi-circle-fill" style="color:var(--clr-green);font-size:.5rem;"></i>&nbsp; Aktif &amp; Online
          </div>
        </div>
      </div>

      <!-- TEMPLATE: Keamanan (Ganti Password) -->
      <div id="tmpl-keamanan" style="display:none;">
        <div class="admin-card" style="padding:1.5rem;">
          <!-- Header kartu -->
          <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;padding-bottom:1.1rem;border-bottom:1px solid var(--border);">
            <div style="width:48px;height:48px;border-radius:12px;background:var(--clr-bg);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="bi bi-shield-lock-fill" style="font-size:1.4rem;color:var(--clr-blue);"></i>
            </div>
            <div>
              <div style="font-weight:700;font-size:.98rem;color:var(--clr-blue);">Ganti Password</div>
              <div style="font-size:.82rem;color:var(--text-mid);">Perbarui kata sandi untuk keamanan akun yang lebih baik.</div>
            </div>
          </div>

          <!-- Field: Password Lama -->
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.87rem;color:var(--clr-blue);">Password Lama <span style="color:#b02a37;">*</span></label>
            <div style="position:relative;">
              <input type="password" id="secCurrentPass" class="form-control" placeholder="Masukkan password saat ini" style="padding-right:2.75rem;">
              <button type="button" class="peng-eye-btn" data-target="secCurrentPass"><i class="bi bi-eye" id="eyeSecCurrent"></i></button>
            </div>
          </div>

          <!-- Field: Password Baru -->
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.87rem;color:var(--clr-blue);">Password Baru <span style="color:#b02a37;">*</span></label>
            <div style="position:relative;">
              <input type="password" id="secNewPass" class="form-control" placeholder="Minimal 8 karakter" style="padding-right:2.75rem;">
              <button type="button" class="peng-eye-btn" data-target="secNewPass"><i class="bi bi-eye" id="eyeSecNew"></i></button>
            </div>
          </div>

          <!-- Field: Konfirmasi + tombol Bersihkan -->
          <div class="mb-2">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.35rem;">
              <label class="form-label fw-semibold mb-0" style="font-size:.87rem;color:var(--clr-blue);">Konfirmasi Password Baru <span style="color:#b02a37;">*</span></label>
              <button type="button" id="secBtnBersihkan" style="background:none;border:none;font-size:.8rem;color:var(--clr-orange);cursor:pointer;font-weight:600;padding:0;">Bersihkan</button>
            </div>
            <div style="position:relative;">
              <input type="password" id="secConfirmPass" class="form-control" placeholder="Ulangi password baru" style="padding-right:2.75rem;">
              <button type="button" class="peng-eye-btn" data-target="secConfirmPass"><i class="bi bi-eye" id="eyeSecConfirm"></i></button>
            </div>
          </div>

          <!-- Bar kekuatan password -->
          <div style="display:flex;gap:5px;margin:1rem 0 .5rem;">
            <div class="pass-strength-bar" id="strengthBar1" style="flex:1;height:4px;border-radius:2px;background:#e0e7ef;transition:background .3s;"></div>
            <div class="pass-strength-bar" id="strengthBar2" style="flex:1;height:4px;border-radius:2px;background:#e0e7ef;transition:background .3s;"></div>
            <div class="pass-strength-bar" id="strengthBar3" style="flex:1;height:4px;border-radius:2px;background:#e0e7ef;transition:background .3s;"></div>
          </div>
          <div id="strengthLabel" style="font-size:.78rem;color:var(--text-light);margin-bottom:.85rem;"></div>

          <!-- Checklist syarat -->
          <div style="display:flex;flex-direction:column;gap:.35rem;margin-bottom:1.25rem;font-size:.83rem;">
            <div class="pass-req" id="req-len"><i class="bi bi-x-circle-fill" style="color:#aaa;margin-right:.4rem;"></i>Minimal 8 karakter</div>
            <div class="pass-req" id="req-upper"><i class="bi bi-x-circle-fill" style="color:#aaa;margin-right:.4rem;"></i>Minimal 1 huruf besar</div>
            <div class="pass-req" id="req-num"><i class="bi bi-x-circle-fill" style="color:#aaa;margin-right:.4rem;"></i>Minimal 1 angka</div>
          </div>

          <!-- Tombol aksi -->
          <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <button type="button" id="secBtnBatal" class="btn-outline-custom" style="flex:1;min-width:120px;justify-content:center;">
              Batalkan
            </button>
            <button type="button" id="secBtnTerapkan" class="btn-primary-custom" style="flex:2;min-width:160px;justify-content:center;" disabled>
              Terapkan Perubahan
            </button>
          </div>
        </div>
      </div><!-- /tmpl-keamanan -->

      <!-- HIDDEN: id yg dibutuhkan JS lama (agar tidak error) -->
      <div style="display:none;">
        <button id="btnSavePengaturan"></button>
        <button id="btnSimpanProfil"></button>
        <button id="btnGantiPassword"></button>
        <input id="profileName"><input id="profileEmail">
        <input id="profileCurrentPass"><input id="profileNewPass"><input id="profileConfirmPass">
        <div id="logoBerandaCard"></div>
        <div id="profileAdminCard"></div>
      </div>

    </div><!-- /pengaturan -->

  </div><!-- /admin-content -->
</div><!-- /admin-main -->

<!-- â”€â”€ Modal Profil Admin â”€â”€ -->
<div class="modal fade" id="modalProfilAdmin" tabindex="-1" aria-labelledby="modalProfilAdminLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
    <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 50px rgba(0,0,0,.18);">
      <div class="modal-header" style="border-bottom:1px solid var(--border,#e5e7eb);padding:1.1rem 1.4rem;">
        <h5 class="modal-title fw-bold" id="modalProfilAdminLabel" style="color:var(--clr-blue,#094a74);font-size:1.05rem;">Profil Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body" style="padding:1.5rem;">
        <div style="display:flex;gap:1.5rem;align-items:flex-start;flex-wrap:wrap;">
          <!-- Kiri: avatar -->
          <div class="mp-left">
            <div class="mp-avatar-wrap">
              <div class="mp-avatar-circle">
                <i class="bi bi-person-fill" id="mpAvatarIcon" style="font-size:2.8rem;color:#aaa;"></i>
                <img id="mpAvatarImg" src="" alt="Avatar" style="display:none;width:100%;height:100%;object-fit:cover;">
              </div>
              <button type="button" class="mp-avatar-plus" id="mpBtnPilihFotoPlus" title="Pilih foto">
                <i class="bi bi-plus"></i>
              </button>
            </div>
            <div style="font-size:.82rem;font-weight:600;color:var(--clr-blue,#094a74);">Unggah Foto</div>
            <div style="font-size:.75rem;color:#aaa;">Ukuran maks. 10 MB</div>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="mpBtnPilihFoto" style="font-size:.8rem;padding:.3rem .9rem;">Pilih Foto</button>
            <input type="file" id="mpAvatarInput" accept="image/jpeg,image/jpg,image/png,image/webp" style="display:none;">
          </div>
          <!-- Pemisah vertikal -->
          <div class="mp-divider"></div>
          <!-- Kanan: form -->
          <div style="flex:1;min-width:200px;">
            <div class="mb-3">
              <label class="form-label fw-semibold" style="font-size:.87rem;color:var(--clr-blue,#094a74);">
                Nama <span style="color:#b02a37;">*</span>
              </label>
              <input type="text" id="mpName" class="form-control" placeholder="Nama admin">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold" style="font-size:.87rem;color:var(--clr-blue,#094a74);">
                Email <span style="color:#b02a37;">*</span>
              </label>
              <input type="email" id="mpEmail" class="form-control" placeholder="Email admin">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--border,#e5e7eb);padding:.9rem 1.4rem;gap:.6rem;">
        <button type="button" class="btn btn-outline-secondary" id="mpBtnBatal" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="mpBtnSimpan" style="background:var(--clr-blue,#094a74);border-color:var(--clr-blue,#094a74);">Simpan Perubahan</button>
      </div>
    </div>
  </div>
</div>

<div id="toastContainer"></div>

<script>
'use strict';

/* â”€â”€ Toast â”€â”€ */
function showAdminToast(msg, type) {
  type = type || 'info';
  var c = document.getElementById('toastContainer');
  var colors = {success:'#537839',error:'#B02A37',info:'var(--clr-blue)',warning:'#E0AA08'};
  var icons  = {success:'bi-check-circle-fill',error:'bi-x-circle-fill',info:'bi-info-circle-fill',warning:'bi-exclamation-triangle-fill'};
  var t = document.createElement('div');
  t.className = 'custom-toast ' + type;
  t.innerHTML = '<i class="bi '+(icons[type]||icons.info)+'" style="color:'+(colors[type]||colors.info)+';font-size:1.2rem;flex-shrink:0"></i><span>'+msg+'</span>';
  c.appendChild(t);
  setTimeout(function(){t.style.opacity='0';t.style.transform='translateX(40px)';t.style.transition='all .35s ease';setTimeout(function(){t.remove();},380);},3500);
}

/* ── Section Switch ── */
function switchSection(name) {
  document.querySelectorAll('.sidebar-nav-item[data-section]').forEach(function(i){i.classList.remove('active');});
  document.querySelectorAll('.admin-tab-pill[data-tab-section]').forEach(function(t){t.classList.remove('active');});
  document.querySelectorAll('.admin-section').forEach(function(s){s.classList.remove('active');});
  var item = document.querySelector('.sidebar-nav-item[data-section="'+name+'"]');
  if (item) item.classList.add('active');
  var tab = document.querySelector('.admin-tab-pill[data-tab-section="'+name+'"]');
  if (tab) tab.classList.add('active');
  var sec = document.getElementById('section-'+name);
  if (sec) sec.classList.add('active');
  var lbl = item && item.querySelector('span') ? item.querySelector('span').textContent : name;
  var titleEl = document.getElementById('adminPageTitle');
  if (titleEl) titleEl.textContent = lbl;
  if (window.innerWidth < 992) {
    document.getElementById('adminSidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('show');
  }
  var contentEl = document.querySelector('.admin-content');
  if (contentEl) {
    contentEl.scrollTop = 0;
  }
  window.scrollTo({top:0,behavior:'smooth'});
  try {
    if (history.replaceState) {
      history.replaceState(null, '', '#' + name);
    }
  } catch (e) {}
}

/* ── Nav items ── */
document.querySelectorAll('.sidebar-nav-item[data-section]').forEach(function(item){
  item.addEventListener('click', function(){switchSection(this.dataset.section);});
});

/* ── Sub-navigation quick tab items ── */
document.querySelectorAll('.admin-tab-pill[data-tab-section]').forEach(function(tab){
  tab.addEventListener('click', function(){switchSection(this.dataset.tabSection);});
});

/* ── Dark / Light Mode Toggle ── */
(function initAdminTheme() {
  var saved = localStorage.getItem('ep_admin_theme') || 'light';
  function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('ep_admin_theme', theme);
    var icon = document.getElementById('themeToggleIcon');
    if (icon) {
      if (theme === 'dark') {
        icon.className = 'bi bi-sun-fill text-warning';
      } else {
        icon.className = 'bi bi-moon-stars';
      }
    }
  }
  applyTheme(saved);
  var btn = document.getElementById('themeToggleBtn');
  if (btn) {
    btn.addEventListener('click', function() {
      var current = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
      var next = current === 'dark' ? 'light' : 'dark';
      applyTheme(next);
      if (typeof showAdminToast === 'function') {
        showAdminToast('Mode ' + (next === 'dark' ? 'Gelap (Dark)' : 'Terang (Light)') + ' diaktifkan.', 'info');
      }
    });
  }
})();

/* ── Topbar Date Pill ── */
(function initTopbarDate() {
  var el = document.getElementById('topbarDate');
  if (!el) return;
  var now = new Date();
  var str = now.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
  var sp = el.querySelector('span');
  if (sp) sp.textContent = str;
})();

/* ── Admin User Info Sync ── */
(function syncAdminUserInfo() {
  try {
    var raw = localStorage.getItem('adminUser');
    if (raw) {
      var u = JSON.parse(raw);
      if (u.name) {
        var g = document.getElementById('adminGreetingName');
        if (g) g.textContent = u.name;
        var topName = document.getElementById('adminTopName');
        if (topName) topName.textContent = u.name;
        var initial = document.getElementById('adminAvatarInitial');
        if (initial) initial.textContent = u.name.charAt(0).toUpperCase();
        var sidebarUname = document.querySelector('.sidebar-user-info .uname');
        if (sidebarUname) sidebarUname.textContent = u.name;
        var sidebarAvatar = document.querySelector('.sidebar-user-avatar');
        if (sidebarAvatar) sidebarAvatar.textContent = u.name.charAt(0).toUpperCase();
      }
    }
  } catch (e) {}
})();

/* â”€â”€ Mobile toggle â”€â”€ */
document.getElementById('sidebarToggle').addEventListener('click', function(){
  document.getElementById('adminSidebar').classList.toggle('open');
  document.getElementById('sidebarOverlay').classList.toggle('show');
});
document.getElementById('sidebarOverlay').addEventListener('click', function(){
  document.getElementById('adminSidebar').classList.remove('open');
  this.classList.remove('show');
});


/* ── Hash-based section routing ── */
(function () {
  var VALID_SECTIONS = ['dashboard','kelola-potensi','verifikasi','permintaan-hapus','galeri','perangkat','profil-desa-admin','pengaturan'];
  function routeFromHash() {
    var hash = window.location.hash.replace('#', '');
    if (hash && VALID_SECTIONS.indexOf(hash) !== -1) {
      switchSection(hash);
    }
  }
  routeFromHash();
  window.addEventListener('hashchange', routeFromHash);
})();

/* ── Dashboard shortcuts ── */
document.getElementById('btnLihatSemua').addEventListener('click',function(){switchSection('verifikasi');});
document.getElementById('btnGoSettings').addEventListener('click',function(){switchSection('pengaturan');});
document.getElementById('btnExport').addEventListener('click',function(){showAdminToast('Fitur export data belum tersedia.','info');});
var btnHubungiPengelola = document.getElementById('btnHubungiPengelola');
if (btnHubungiPengelola) {
  btnHubungiPengelola.addEventListener('click', function () {
    switchSection('profil-desa-admin');
  });
}

/* \u2500\u2500 Notifikasi Bell \u2500\u2500 */
(function () {
  'use strict';

  var API_BASE = (window.location.protocol === 'file:' || window.location.port === '5500')
    ? 'http://localhost:8000/api'
    : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');

  var bell      = document.getElementById('btnNotifBell');
  var panel     = document.getElementById('notifPanel');
  var badge     = document.getElementById('notifBadge');
  var btnAll    = document.getElementById('btnNotifTandaiSemua');
  var btnVerif  = document.getElementById('btnNotifLihatSemua');
  var list      = document.getElementById('notifList');

  var STORAGE_KEY = 'ep_notif_read';
  var MAX_IDS     = 200;
  var _items      = [];           // cache data terakhir
  var _loadTimer  = null;
  var _autoTimer  = null;

  /* ── localStorage helpers ── */
  function getReadIds() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); } catch (e) { return []; }
  }
  function saveReadIds(ids) {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(ids.slice(-MAX_IDS))); } catch (e) {}
  }
  function markRead(id) {
    var ids = getReadIds();
    if (ids.indexOf(id) === -1) { ids.push(id); saveReadIds(ids); }
  }
  function isRead(id) { return getReadIds().indexOf(id) !== -1; }

  /* ── Waktu relatif ── */
  var MONTHS = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
  function relTime(str) {
    if (!str) return '';
    try {
      var d   = new Date(str);
      if (isNaN(d.getTime())) return '';
      var now = new Date();
      var diff = Math.floor((now - d) / 1000);
      if (diff < 0)   diff = 0;
      if (diff < 60)  return 'baru saja';
      if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
      if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
      var days = Math.floor(diff / 86400);
      if (days < 30)  return days + ' hari lalu';
      return d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear();
    } catch (e) { return ''; }
  }

  /* ── Badge ── */
  function updateBadge() {
    var ids = getReadIds();
    var unread = _items.filter(function (p) { return ids.indexOf(p.id) === -1; }).length;
    if (!badge) return;
    if (unread === 0) {
      badge.style.display = 'none';
      badge.textContent   = '';
    } else {
      badge.textContent   = unread > 9 ? '9+' : String(unread);
      badge.style.display = '';
    }
  }

  /* ── Render list ── */
  function render() {
    // Hapus semua kecuali <li> tombol (last child)
    var btnLi = list.lastElementChild;
    while (list.firstElementChild !== btnLi) {
      list.removeChild(list.firstElementChild);
    }

    var ids = getReadIds();

    if (_items.length === 0) {
      var emptyLi = document.createElement('li');
      emptyLi.style.cssText = 'padding:.85rem 1rem;text-align:center;font-size:.82rem;color:#6b7a8d;';
      emptyLi.textContent = 'Tidak ada notifikasi baru';
      list.insertBefore(emptyLi, btnLi);
      updateBadge();
      return;
    }

    _items.forEach(function (p) {
      var read     = ids.indexOf(p.id) !== -1;
      var timeStr  = relTime(p.created_at);
      var subLabel = p.status === 'pending'
        ? 'Menunggu verifikasi' + (timeStr ? ' \u00b7 ' + timeStr : '')
        : 'Baru terdaftar'     + (timeStr ? ' \u00b7 ' + timeStr : '');

      var li = document.createElement('li');
      li.className = read ? 'notif-item' : 'notif-item notif-unread';
      li.style.cssText = 'display:flex;gap:.75rem;padding:.7rem 1rem;cursor:pointer;border-bottom:1px solid #f5f7fa;'
                       + (read ? '' : 'background:rgba(242,99,15,.04);');
      li.dataset.notifId     = p.id;
      li.dataset.notifStatus = p.status || '';

      // Ikon
      var iconWrap = document.createElement('div');
      iconWrap.style.cssText = 'flex-shrink:0;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;'
                             + (read
                               ? 'background:rgba(9,74,116,.10);color:#6b7a8d;'
                               : 'background:rgba(242,99,15,.12);color:var(--clr-orange);');
      var icon = document.createElement('i');
      icon.className = 'bi bi-person-plus-fill';
      iconWrap.appendChild(icon);
      li.appendChild(iconWrap);

      // Teks
      var textWrap = document.createElement('div');
      textWrap.style.cssText = 'flex:1;min-width:0;';

      var title = document.createElement('div');
      title.style.cssText = 'font-size:.82rem;font-weight:600;color:var(--clr-blue);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
      title.textContent = 'Pendaftaran baru: ' + (p.name || '');
      textWrap.appendChild(title);

      var sub = document.createElement('div');
      sub.style.cssText = 'font-size:.77rem;color:#6b7a8d;margin-top:.15rem;';
      sub.textContent = subLabel;
      textWrap.appendChild(sub);
      li.appendChild(textWrap);

      // Titik oranye (hanya jika belum dibaca)
      if (!read) {
        var dot = document.createElement('span');
        dot.style.cssText = 'width:8px;height:8px;border-radius:50%;background:var(--clr-orange);flex-shrink:0;margin-top:5px;';
        li.appendChild(dot);
      }

      list.insertBefore(li, btnLi);
    });

    updateBadge();
  }

  /* ── Load dari API ── */
  function load(showLoading) {
    if (document.hidden) return;
    if (showLoading && _items.length === 0) {
      // Tampilkan loading row
      var btnLi = list.lastElementChild;
      while (list.firstElementChild !== btnLi) list.removeChild(list.firstElementChild);
      var loadLi = document.createElement('li');
      loadLi.id = 'notifLoadingRow';
      loadLi.style.cssText = 'padding:.85rem 1rem;text-align:center;font-size:.82rem;color:#6b7a8d;';
      loadLi.textContent = 'Memuat notifikasi\u2026';
      list.insertBefore(loadLi, btnLi);
    }

    clearTimeout(_loadTimer);
    _loadTimer = setTimeout(function () {
      var _tok = null;
      try {
        var _s = localStorage.getItem('ep_admin_session') || sessionStorage.getItem('ep_admin_session');
        if (_s) _tok = JSON.parse(_s).token || null;
      } catch (e) {}
      fetch(API_BASE + '/potensi?per_page=50', { headers: _tok ? { 'Accept': 'application/json', 'Authorization': 'Bearer ' + _tok } : { 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res && Array.isArray(res.data)) {
            var all = res.data;
            // Hitung pending untuk verifikasiBadge
            var pendingTotal = all.filter(function (p) { return p.status === 'pending'; }).length;
            var vb = document.getElementById('verifikasiBadge');
            if (vb) vb.textContent = pendingTotal > 0 ? pendingTotal : '';
            var pb = document.getElementById('potensiBadge');
            if (pb) pb.textContent = all.length > 0 ? all.length : '';

            // Ambil 5 teratas bukan rejected
            _items = all.filter(function (p) { return p.status !== 'rejected'; }).slice(0, 5);
          }
          render();
        })
        .catch(function () {
          // Gagal: jika belum ada data, tampilkan pesan error
          if (_items.length === 0) {
            var btnLi = list.lastElementChild;
            while (list.firstElementChild !== btnLi) list.removeChild(list.firstElementChild);
            var errLi = document.createElement('li');
            errLi.style.cssText = 'padding:.85rem 1rem;text-align:center;font-size:.82rem;color:#6b7a8d;';
            errLi.textContent = 'Gagal memuat notifikasi.';
            list.insertBefore(errLi, btnLi);
          }
          // Jika sudah ada data, pertahankan render lama
        });
    }, 120);
  }

  /* ── Buka / tutup panel ── */
  function openPanel() {
    panel.style.display = 'block';
    bell.setAttribute('aria-expanded', 'true');
    load(true);   // muat ulang setiap kali panel dibuka
  }
  function closePanel() {
    panel.style.display = 'none';
    bell.setAttribute('aria-expanded', 'false');
  }

  bell.addEventListener('click', function (e) {
    e.stopPropagation();
    if (panel.style.display === 'block') { closePanel(); } else { openPanel(); }
  });

  // Klik di luar panel \u2192 tutup
  document.addEventListener('click', function (e) {
    if (!document.getElementById('notifBellWrap').contains(e.target)) closePanel();
  });

  /* ── Tandai semua dibaca ── */
  btnAll.addEventListener('click', function () {
    var ids = getReadIds();
    _items.forEach(function (p) { if (ids.indexOf(p.id) === -1) ids.push(p.id); });
    saveReadIds(ids);
    render();
    showAdminToast('Semua notifikasi telah ditandai dibaca.', 'success');
  });

  /* ── Event delegation: klik item ── */
  list.addEventListener('click', function (e) {
    var li = e.target.closest('.notif-item');
    if (!li) return;
    var id     = Number(li.dataset.notifId);
    var status = li.dataset.notifStatus || '';
    if (id) markRead(id);
    render();
    closePanel();
    if (status === 'pending') { switchSection('verifikasi'); }
    else                      { switchSection('kelola-potensi'); }
  });

  /* ── Lihat semua di Verifikasi ── */
  btnVerif.addEventListener('click', function () {
    closePanel();
    switchSection('verifikasi');
  });

  /* ── Muat saat halaman dibuka ── */
  load(true);

  /* ── Auto-refresh 60 detik ── */
  _autoTimer = setInterval(function () {
    if (!document.hidden) load(false);
  }, 60000);

  document.addEventListener('visibilitychange', function () {
    if (!document.hidden && panel.style.display === 'block') load(false);
  });

})();


document.querySelectorAll('.btn-go-verifikasi').forEach(function(b){
  b.addEventListener('click',function(){switchSection('verifikasi');});
});

/* Detail Potensi Admin Handler */
(function () {
  function esc(s) {
    if (s == null) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }

  window.openDetailPotensiAdmin = function (item) {
    if (!item) return;
    var modalEl = document.getElementById('modalDetailPotensiAdmin');
    if (!modalEl) return;

    var nameEl = document.getElementById('admDetailName');
    if (nameEl) nameEl.textContent = item.name || '-';

    var ownerEl = document.getElementById('admDetailOwner');
    if (ownerEl) ownerEl.textContent = item.owner_name || '-';

    var catName = (item.category && item.category.name) ? item.category.name : (item.category || '-');
    var catEl = document.getElementById('admDetailCat');
    if (catEl) {
      catEl.textContent = catName;
      catEl.className = 'potensi-badge badge-usaha';
      var lowerCat = String(catName).toLowerCase();
      if (lowerCat.indexOf('jasa') !== -1) catEl.className = 'potensi-badge badge-jasa';
      else if (lowerCat.indexOf('hunian') !== -1) catEl.className = 'potensi-badge badge-hunian';
      else if (lowerCat.indexOf('barang') !== -1) catEl.className = 'potensi-badge badge-barang';
    }

    var st = (item.status || 'pending').toLowerCase();
    var stEl = document.getElementById('admDetailStatus');
    if (stEl) {
      if (st === 'approved' || st === 'aktif') {
        stEl.className = 'status-badge status-aktif';
        stEl.textContent = 'Aktif';
      } else if (st === 'rejected' || st === 'ditolak') {
        stEl.className = 'status-badge status-rejected';
        stEl.textContent = 'Ditolak';
      } else {
        stEl.className = 'status-badge status-pending';
        stEl.textContent = 'Menunggu';
      }
    }

    var dateEl = document.getElementById('admDetailDate');
    if (dateEl) {
      var dStr = item.created_at || '';
      var d = new Date(dStr);
      var BULAN = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
      var formatted = !isNaN(d.getTime()) ? (d.getDate() + ' ' + BULAN[d.getMonth()] + ' ' + d.getFullYear()) : (dStr || '-');
      dateEl.innerHTML = '<i class="bi bi-calendar3 me-1"></i> Terdaftar: ' + esc(formatted);
    }

    var waEl = document.getElementById('admDetailWa');
    if (waEl) {
      if (item.whatsapp) {
        var num = String(item.whatsapp).replace(/\D/g, '');
        if (num.startsWith('0')) num = '62' + num.substring(1);
        waEl.innerHTML = '<a href="https://wa.me/' + num + '" target="_blank" rel="noopener" class="d-inline-flex align-items-center gap-1 text-decoration-none" style="color:var(--clr-orange);font-weight:600;"><i class="bi bi-whatsapp"></i> ' + esc(item.whatsapp) + '</a>';
      } else {
        waEl.textContent = '-';
      }
    }

    var emailEl = document.getElementById('admDetailEmail');
    if (emailEl) {
      if (item.email) {
        emailEl.innerHTML = '<a href="mailto:' + esc(item.email) + '" class="text-decoration-none" style="color:var(--clr-orange);"><i class="bi bi-envelope me-1"></i>' + esc(item.email) + '</a>';
      } else {
        emailEl.textContent = '-';
      }
    }

    var jenisEl = document.getElementById('admDetailJenis');
    if (jenisEl) jenisEl.textContent = item.jenis || catName || '-';

    var jamEl = document.getElementById('admDetailJam');
    if (jamEl) jamEl.textContent = (item.jam_operasional && item.jam_operasional !== '-') ? item.jam_operasional : '—';

    var addrEl = document.getElementById('admDetailAddress');
    if (addrEl) addrEl.textContent = item.address || '-';

    var locEl = document.getElementById('admDetailLocation');
    if (locEl) {
      if (item.location) {
        locEl.style.display = 'block';
        locEl.innerHTML = '<i class="bi bi-geo-alt me-1 text-danger"></i> ' + esc(item.location);
      } else {
        locEl.style.display = 'none';
        locEl.textContent = '';
      }
    }

    var descEl = document.getElementById('admDetailDesc');
    if (descEl) descEl.textContent = item.description || '-';

    var photoWrap = document.getElementById('admDetailPhotoList');
    if (photoWrap) {
      photoWrap.innerHTML = '';
      var photos = item.images || [];
      if (photos.length > 0) {
        photos.forEach(function (p) {
          var src = (p.path || p.image_path || p);
          if (typeof src === 'string') {
            var img = document.createElement('img');
            var safeSrc = window.sanitizeSrc ? window.sanitizeSrc(src) : src;
            img.src = safeSrc;
            img.alt = item.name || 'Foto Potensi';
            img.style.cssText = 'width:90px;height:90px;object-fit:cover;border-radius:12px;border:1px solid var(--adm-card-border);cursor:pointer;transition:transform .2s;';
            img.addEventListener('mouseover', function () { this.style.transform = 'scale(1.05)'; });
            img.addEventListener('mouseout', function () { this.style.transform = 'scale(1)'; });
            img.addEventListener('click', function () { window.open(safeSrc, '_blank'); });
            photoWrap.appendChild(img);
          }
        });
      } else {
        photoWrap.innerHTML = '<div class="d-flex align-items-center gap-2 text-muted p-2 rounded" style="background:var(--adm-pill-bg);font-size:.84rem;"><i class="bi bi-image" style="font-size:1.3rem;"></i> Tidak ada foto dilampirkan</div>';
      }
    }

    var btnEdit = document.getElementById('btnEditFromDetail');
    if (btnEdit) {
      btnEdit.onclick = function () {
        var modalInst = bootstrap.Modal.getInstance(modalEl);
        if (modalInst) modalInst.hide();
        setTimeout(function () {
          if (typeof window.openEditModal === 'function') window.openEditModal(item);
        }, 350);
      };
    }

    bootstrap.Modal.getOrCreateInstance(modalEl).show();
  };

  document.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-lihat-db');
    if (!btn) return;
    var tr = btn.closest('tr');
    if (tr && tr.dataset && tr.dataset.name) {
      var staticItem = {
        name: tr.dataset.name,
        owner_name: tr.dataset.owner || '-',
        category: { name: tr.dataset.cat || '-' },
        whatsapp: tr.dataset.wa || '-',
        email: tr.dataset.email || '-',
        address: tr.dataset.address || '-',
        description: tr.dataset.desc || '-',
        jam_operasional: tr.dataset.jam || '—',
        jenis: tr.dataset.jenis || tr.dataset.cat || '-',
        status: tr.dataset.status || 'Aktif',
        created_at: tr.dataset.date || ''
      };
      window.openDetailPotensiAdmin(staticItem);
    }
  });
})();

/* -- Verifikasi: lihat blok 'Verifikasi live' di bawah -- */
/* -- Verifikasi live: daftar pending dari API, aksi lewat /potensi/{id}/review -- */
(function () {
  'use strict';
  var API = (window.location.protocol === 'file:' || window.location.port === '5500')
    ? 'http://localhost:8000/api'
    : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');
  var list = document.getElementById('verifikasiList');
  var emptyBox = document.getElementById('verifikasiEmpty');
  var warn = document.getElementById('warningVerifikasi');
  if (!list) return;

  function token() {
    try {
      var s = localStorage.getItem('ep_admin_session') || sessionStorage.getItem('ep_admin_session');
      if (s) return JSON.parse(s).token || null;
    } catch (e) {}
    return null;
  }
  function headers(json) {
    var h = { 'Accept': 'application/json' };
    if (json) h['Content-Type'] = 'application/json';
    var t = token(); if (t) h['Authorization'] = 'Bearer ' + t;
    return h;
  }
  function setText(id, v) { var el = document.getElementById(id); if (el) el.textContent = v; }
  function el(tag, cls, text) {
    var n = document.createElement(tag);
    if (cls) n.className = cls;
    if (text != null) n.textContent = text;   // textContent: aman dari XSS
    return n;
  }
  function fmtDate(iso) {
    if (!iso) return '-';
    var d = new Date(iso);
    return isNaN(d) ? '-' : d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
  }
  function field(label, value, cols) {
    var c = el('div', cols || 'col-md-4');
    c.appendChild(el('div', 'preview-label', label));
    c.appendChild(el('div', 'preview-value', value || '-'));
    return c;
  }

  function card(p) {
    var wrap = el('div', 'admin-card mb-4');
    var head = el('div', 'admin-card-header');
    var left = el('div');
    var h5 = el('h5', 'mb-0');
    h5.appendChild(el('i', 'bi bi-file-earmark-text me-2'));
    h5.appendChild(document.createTextNode(p.name));
    left.appendChild(h5);
    left.appendChild(el('small', '', 'Pendaftaran masuk: ' + fmtDate(p.created_at)));
    head.appendChild(left);
    var badge = el('span', 'status-badge status-pending', 'Menunggu Verifikasi');
    head.appendChild(badge);
    wrap.appendChild(head);

    var body = el('div', 'admin-card-body');
    var row = el('div', 'row g-3 mb-3');
    row.appendChild(field('Kategori', p.category && p.category.name));
    row.appendChild(field('Pendaftar', p.owner_name));
    row.appendChild(field('WhatsApp', p.whatsapp));
    row.appendChild(field('Email', p.email));
    row.appendChild(field('Jenis', p.jenis));
    row.appendChild(field('Jam Operasional', p.jam_operasional));
    row.appendChild(field('Alamat', p.address, 'col-12'));
    var d = el('div', 'col-12');
    d.appendChild(el('div', 'preview-label', 'Deskripsi Potensi'));
    var pc = el('div', 'preview-card mt-1');
    pc.appendChild(el('p', 'mb-0', p.description || '-'));
    d.appendChild(pc);
    row.appendChild(d);
    body.appendChild(row);

    var actions = el('div', 'd-flex gap-2 flex-wrap');
    var ok = el('button', 'btn btn-success');
    ok.type = 'button';
    ok.appendChild(el('i', 'bi bi-check-circle me-1'));
    ok.appendChild(document.createTextNode('Verifikasi & Aktifkan'));
    var no = el('button', 'btn btn-danger');
    no.type = 'button';
    no.appendChild(el('i', 'bi bi-x-circle me-1'));
    no.appendChild(document.createTextNode('Tolak'));
    actions.appendChild(ok); actions.appendChild(no);
    body.appendChild(actions);
    wrap.appendChild(body);

    function send(status, reason, doneMsg, toastType) {
      ok.disabled = no.disabled = true;
      var payload = { status: status };
      if (reason) payload.reason = reason;
      fetch(API + '/potensi/' + p.id + '/review', { method: 'POST', headers: headers(true), body: JSON.stringify(payload) })
        .then(function (r) { return r.json().then(function (j) { return { r: r, j: j }; }); })
        .then(function (x) {
          if (x.r.ok) {
            showAdminToast(doneMsg, toastType);
            load();
            if (typeof window.reloadPotensiTable === 'function') window.reloadPotensiTable();
          } else {
            ok.disabled = no.disabled = false;
            var msg = x.r.status === 401 ? 'Sesi berakhir, silakan login ulang.'
              : (x.j && x.j.errors ? Object.values(x.j.errors)[0][0] : 'Gagal memproses pendaftaran.');
            showAdminToast(msg, 'error');
          }
        })
        .catch(function () { ok.disabled = no.disabled = false; showAdminToast('Server tidak dapat dihubungi.', 'error'); });
    }
    ok.addEventListener('click', function () {
      send('approved', null, 'Potensi berhasil diverifikasi dan diaktifkan.', 'success');
    });
    no.addEventListener('click', function () {
      var reason = window.prompt('Alasan penolakan (wajib diisi):');
      if (reason === null) return;
      reason = reason.trim();
      if (!reason) { showAdminToast('Alasan penolakan wajib diisi.', 'error'); return; }
      send('rejected', reason, 'Pendaftaran telah ditolak.', 'error');
    });
    return wrap;
  }

  function load() {
    // Muat semua halaman agar hitungan KPI akurat
    var page = 1, lastPage = 1, collected = [];
    function fetchPage() {
      fetch(API + '/potensi?per_page=50&page=' + page, { headers: headers(false) })
        .then(function (r) { if (r.status === 401) throw new Error('401'); return r.json(); })
        .then(function (res) {
          var rows = (res && Array.isArray(res.data)) ? res.data : [];
          collected = collected.concat(rows);
          lastPage = (res && res.meta && res.meta.last_page) ? res.meta.last_page : 1;
          if (page < lastPage) { page++; fetchPage(); return; }

          var all     = collected;
          var pending  = all.filter(function (p) { return p.status === 'pending'; });
          var verified = all.filter(function (p) { return p.status === 'approved'; });

          setText('kpiTotal', all.length);
          setText('kpiPending', pending.length);
          setText('kpiVerified', verified.length);
          setText('pendingCount', pending.length);
          setText('verifikasiBadge', pending.length > 0 ? pending.length : '');
          setText('potensiBadge', all.length > 0 ? all.length : '');
          if (warn) warn.style.display = pending.length ? '' : 'none';

          list.textContent = '';
          pending.forEach(function (p) { list.appendChild(card(p)); });
          if (emptyBox) emptyBox.style.display = pending.length ? 'none' : '';

          // Update Dashboard "Pendaftaran Terbaru" table dynamically if data exists
          var recentTbody = document.getElementById('dashboardRecentTbody');
          if (recentTbody && all.length > 0) {
            recentTbody.innerHTML = '';
            var recentItems = all.slice(0, 5);
            recentItems.forEach(function (item, idx) {
              var kat = (item.category && item.category.name) ? item.category.name : (item.category || '-');
              var katBadge = '<span class="potensi-badge badge-usaha">' + el('span', '', kat).textContent + '</span>';
              var lowerKat = String(kat).toLowerCase();
              if (lowerKat.indexOf('jasa') !== -1) katBadge = '<span class="potensi-badge badge-jasa">' + el('span', '', kat).textContent + '</span>';
              else if (lowerKat.indexOf('hunian') !== -1) katBadge = '<span class="potensi-badge badge-hunian">' + el('span', '', kat).textContent + '</span>';
              else if (lowerKat.indexOf('barang') !== -1) katBadge = '<span class="potensi-badge badge-barang">' + el('span', '', kat).textContent + '</span>';

              var st = (item.status || 'pending').toLowerCase();
              var stClass = 'status-pending', stLabel = 'Menunggu';
              if (st === 'approved' || st === 'aktif') { stClass = 'status-aktif'; stLabel = 'Aktif'; }
              else if (st === 'rejected' || st === 'ditolak') { stClass = 'status-rejected'; stLabel = 'Ditolak'; }

              var tr = document.createElement('tr');
              var itemDate = item.created_at ? fmtDate(item.created_at) : '-';
              tr.innerHTML =
                '<td>' + (idx + 1) + '</td>'
                + '<td><strong>' + (el('span', '', item.name || '-').textContent) + '</strong></td>'
                + '<td>' + katBadge + '</td>'
                + '<td>' + itemDate + '</td>'
                + '<td><span class="status-badge ' + stClass + '">' + stLabel + '</span></td>'
                + '<td class="text-end">'
                  + '<button type="button" class="btn btn-sm btn-action btn-lihat-db" title="Lihat Detail Potensi"><i class="bi bi-eye"></i></button>'
                + '</td>';

              var btn = tr.querySelector('.btn-lihat-db');
              if (btn) {
                btn.addEventListener('click', function () {
                  if (window.openDetailPotensiAdmin) window.openDetailPotensiAdmin(item);
                });
              }
              recentTbody.appendChild(tr);
            });
          }
        })
        .catch(function () {
          list.textContent = '';
          var e = el('div', 'admin-alert warning', 'Gagal memuat daftar verifikasi. Muat ulang halaman atau login ulang.');
          list.appendChild(e);
        });
    }
    fetchPage();
  }

  window.refreshVerifikasi = load;
  load();
  var nav = document.querySelector('[data-section="verifikasi"]');
  if (nav) nav.addEventListener('click', load);
})();

/* -- Permintaan Penghapusan: Load dan Handle -- */
(function () {
  'use strict';
  var API = (window.location.protocol === 'file:' || window.location.port === '5500')
    ? 'http://localhost:8000/api'
    : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');

  var listEl   = document.getElementById('deletionRequestList');
  var emptyEl  = document.getElementById('deletionEmpty');
  var badge    = document.getElementById('deletionBadge');
  if (!listEl) return;

  function token() {
    try {
      var s = localStorage.getItem('ep_admin_session') || sessionStorage.getItem('ep_admin_session');
      if (s) return JSON.parse(s).token || null;
    } catch(e){}
    return null;
  }
  function hdrs(json) {
    var h = { 'Accept': 'application/json' };
    if (json) h['Content-Type'] = 'application/json';
    var t = token(); if (t) h['Authorization'] = 'Bearer ' + t;
    return h;
  }
  function fmtDate(iso) {
    if (!iso) return '-';
    var d = new Date(iso);
    return isNaN(d) ? '-' : d.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });
  }

  function loadDeletionRequests() {
    listEl.innerHTML = '<div class="text-center py-4" style="color:#9ca3af;">Memuat data...</div>';
    if (emptyEl) emptyEl.style.display = 'none';

    fetch(API + '/admin/deletion-requests', { headers: hdrs(false) })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        listEl.innerHTML = '';
        var items = (res && Array.isArray(res.data)) ? res.data : [];

        // Update badge di sidebar
        if (badge) {
          badge.textContent = items.length;
          badge.style.display = items.length ? '' : 'none';
        }

        if (!items.length) {
          if (emptyEl) emptyEl.style.display = '';
          return;
        }

        items.forEach(function(p) {
          var card = document.createElement('div');
          card.className = 'admin-card mb-3';
          card.style.cssText = 'border-left:4px solid #dc3545;';

          var imgHtml = '';
          if (p.images && p.images.length) {
            imgHtml = '<div class="d-flex gap-2 flex-wrap mt-2">';
            p.images.forEach(function(img) {
              var safeImg = window.sanitizeSrc ? window.sanitizeSrc(img.path||'') : (img.path||'');
              imgHtml += '<img src="' + safeImg + '" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #e5e9f0;" onerror="this.style.display=\'none\'">';
            });
            imgHtml += '</div>';
          }

          var safeName = escapeHtml(p.name || '');
          card.innerHTML =
            '<div class="admin-card-body">' +
              '<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">' +
                '<div class="flex-grow-1">' +
                  '<div style="font-weight:700;color:var(--clr-blue);font-size:.95rem;">' + safeName + '</div>' +
                  '<div style="font-size:.82rem;color:#9ca3af;margin-top:.2rem;">' +
                    'Pemilik: <strong>' + escapeHtml(p.owner_name || '-') + '</strong>' +
                    ' &middot; WA: ' + escapeHtml(p.owner_phone || '-') +
                    ' &middot; Status: <span class="fw-semibold text-success">' + escapeHtml(p.status || '-') + '</span>' +
                  '</div>' +
                  '<div style="font-size:.8rem;color:#9ca3af;margin-top:.15rem;">' +
                    'Diajukan: ' + fmtDate(p.deletion_requested_at) +
                  '</div>' +
                  imgHtml +
                '</div>' +
                '<div class="d-flex gap-2 flex-shrink-0 align-items-center">' +
                  '<button class="btn btn-sm btn-danger btn-approve-del" data-id="' + p.id + '" data-name="' + safeName + '" style="font-size:.82rem;">' +
                    '<i class="bi bi-trash-fill me-1"></i>Setujui Hapus' +
                  '</button>' +
                  '<button class="btn btn-sm btn-outline-secondary btn-reject-del" data-id="' + p.id + '" style="font-size:.82rem;">' +
                    '<i class="bi bi-x-lg me-1"></i>Tolak' +
                  '</button>' +
                '</div>' +
              '</div>' +
            '</div>';

          card.querySelector('.btn-approve-del').addEventListener('click', function() {
            var id   = this.dataset.id;
            var name = this.dataset.name;
            if (!confirm('Setujui penghapusan "' + name + '"? Data akan benar-benar terhapus.')) return;
            fetch(API + '/admin/deletion-requests/' + id + '/approve', { method: 'POST', headers: hdrs(true) })
              .then(function(r) { return r.json(); })
              .then(function(res) {
                if (res && res.status === 'success') { showAdminToast('Potensi berhasil dihapus.', 'success'); loadDeletionRequests(); }
                else showAdminToast(res.message || 'Gagal.', 'error');
              }).catch(function() { showAdminToast('Terjadi kesalahan.', 'error'); });
          });

          card.querySelector('.btn-reject-del').addEventListener('click', function() {
            var id = this.dataset.id;
            if (!confirm('Tolak permintaan penghapusan ini? Potensi akan tetap ada.')) return;
            fetch(API + '/admin/deletion-requests/' + id + '/reject', { method: 'POST', headers: hdrs(true) })
              .then(function(r) { return r.json(); })
              .then(function(res) {
                if (res && res.status === 'success') { showAdminToast('Permintaan penghapusan ditolak.', 'info'); loadDeletionRequests(); }
                else showAdminToast(res.message || 'Gagal.', 'error');
              }).catch(function() { showAdminToast('Terjadi kesalahan.', 'error'); });
          });

          listEl.appendChild(card);
        });
      })
      .catch(function() { listEl.innerHTML = '<div class="text-center py-4 text-danger">Gagal memuat data.</div>'; });
  }

  // Load saat section diklik
  document.querySelectorAll('[data-section="permintaan-hapus"]').forEach(function(btn) {
    btn.addEventListener('click', loadDeletionRequests);
  });

  // Load juga saat init untuk update badge
  loadDeletionRequests();
})();

/* -- Kelola Potensi: Load, Filter, Paginate, Edit, Delete -- */
(function () {
  'use strict';

  var API_BASE = (window.location.protocol === 'file:' || window.location.port === '5500')
    ? 'http://localhost:8000/api'
    : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');

  var PAGE_SIZE = 8;
  var _allData  = [];
  var _filtered = [];
  var _page     = 1;

  var tbody        = document.querySelector('#tabelPotensi tbody');
  var pagDiv       = document.querySelector('.admin-pagination');
  var searchInput  = document.getElementById('searchPotensi');
  var filterSelect = document.getElementById('filterKategori');

  var BULAN = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

  function escapeHtml(s) {
    if (s == null) return '';
    return String(s)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }
  function getToken() {
    try {
      var s = localStorage.getItem('ep_admin_session') || sessionStorage.getItem('ep_admin_session');
      if (s) return JSON.parse(s).token || null;
    } catch(e){}
    return null;
  }
  function hdrs(json) {
    var h = { 'Accept': 'application/json' };
    if (json) h['Content-Type'] = 'application/json';
    var t = getToken(); if (t) h['Authorization'] = 'Bearer ' + t;
    return h;
  }
  function katFrom(name) {
    if (!name) return '';
    var n = name.toLowerCase();
    if (n.indexOf('usaha')  !== -1) return 'usaha';
    if (n.indexOf('jasa')   !== -1) return 'jasa';
    if (n.indexOf('hunian') !== -1) return 'hunian';
    if (n.indexOf('barang') !== -1) return 'barang';
    return '';
  }
  function fmtDate(str) {
    if (!str) return '-';
    var d = new Date(str);
    if (isNaN(d.getTime())) return str;
    return ('0'+d.getDate()).slice(-2)+' '+BULAN[d.getMonth()]+' '+d.getFullYear();
  }

  function makeRow(item, no) {
    var catName = (item.category && item.category.name) ? item.category.name : '';
    var kat     = katFrom(catName);
    var katBadge = catName
      ? '<span class="potensi-badge badge-'+(kat||'usaha')+'">'+escapeHtml(catName)+'</span>'
      : '-';
    var st = item.status || '';
    var stClass, stLabel;
    if (st==='approved')                  { stClass='status-aktif';   stLabel='Aktif'; }
    else if (st==='pending')              { stClass='status-pending'; stLabel='Menunggu'; }
    else if (st==='rejected')             { stClass='status-ditolak'; stLabel='Ditolak'; }
    else                                  { stClass='status-pending'; stLabel=escapeHtml(st); }
    var tr = document.createElement('tr');
    tr.dataset.kat = kat; tr.dataset.id = item.id;
    tr.innerHTML =
      '<td>'+no+'</td>'
      +'<td><strong>'+escapeHtml(item.name)+'</strong></td>'
      +'<td>'+katBadge+'</td>'
      +'<td><span class="status-badge '+stClass+'">'+stLabel+'</span></td>'
      +'<td>'+fmtDate(item.created_at)+'</td>'
      +'<td class="text-end">'
        +'<div class="d-inline-flex align-items-center gap-1">'
          +'<button type="button" class="btn btn-sm btn-action btn-lihat-row" title="Lihat Detail Potensi"><i class="bi bi-eye"></i></button>'
          +'<button type="button" class="btn btn-sm btn-action btn-edit-row" title="Edit Potensi"><i class="bi bi-pencil"></i></button>'
          +'<button type="button" class="btn btn-sm btn-action btn-hapus" title="Hapus Potensi"><i class="bi bi-trash"></i></button>'
        +'</div>'
      +'</td>';
    return tr;
  }

  function renderTable() {
    tbody.innerHTML = '';
    if (_filtered.length === 0) {
      var q   = searchInput ? searchInput.value.trim() : '';
      var etr = document.createElement('tr'); etr.className = 'empty-row text-center';
      var etd = document.createElement('td'); etd.colSpan=6; etd.className='py-4 text-muted';
      etd.innerHTML = q
        ? '<i class="bi bi-search me-2"></i>Tidak ada potensi yang cocok dengan pencarian'
        : 'Belum ada data potensi.';
      etr.appendChild(etd); tbody.appendChild(etr);
      renderPagination(); return;
    }
    var start = (_page-1)*PAGE_SIZE, end = Math.min(start+PAGE_SIZE,_filtered.length);
    for (var i=start;i<end;i++) tbody.appendChild(makeRow(_filtered[i],i+1));
    renderPagination();
  }

  function renderPagination() {
    pagDiv.innerHTML = '';
    var total = Math.max(1, Math.ceil(_filtered.length / PAGE_SIZE));
    if (total >= 2) {
      var inp = document.createElement('input');
      inp.type='number'; inp.min='1'; inp.inputMode='numeric';
      inp.placeholder='Hal.'; inp.id='inputLompatHalaman'; inp.className='page-jump-input';
      var btnJ = document.createElement('button');
      btnJ.className='page-btn'; btnJ.setAttribute('aria-label','Ke halaman');
      btnJ.innerHTML='<i class="bi bi-search"></i>';
      function doJump() {
        var v=inp.value.trim(), n=parseInt(v,10);
        if (!v||isNaN(n)||n<1||n>total||n!==Math.floor(n)) {
          showAdminToast('Halaman tidak valid. Masukkan angka 1 sampai '+total+'.','warning'); return;
        }
        inp.value=''; goPage(n);
      }
      inp.addEventListener('keydown',function(e){if(e.key==='Enter')doJump();});
      btnJ.addEventListener('click',doJump);
      pagDiv.appendChild(inp); pagDiv.appendChild(btnJ);
    }
    var pages=[];
    if (total<=7) { for(var i=1;i<=total;i++) pages.push(i); }
    else {
      pages.push(1);
      var lo=Math.max(2,_page-1), hi=Math.min(total-1,_page+1);
      if (lo>2) pages.push('...');
      for(var i=lo;i<=hi;i++) pages.push(i);
      if (hi<total-1) pages.push('...');
      pages.push(total);
    }
    pages.forEach(function(p) {
      var btn=document.createElement('button');
      btn.className='page-btn'+(p===_page?' active':'');
      if (p==='...') { btn.textContent='...'; btn.disabled=true; btn.style.cursor='default'; }
      else { btn.textContent=p; (function(pg){btn.addEventListener('click',function(){goPage(pg);});})(p); }
      pagDiv.appendChild(btn);
    });
    if (total >= 2) {
      var arr=document.createElement('button');
      arr.className='page-btn'; arr.innerHTML='<i class="bi bi-chevron-right"></i>';
      if (_page>=total) { arr.disabled=true; arr.style.opacity='0.4'; arr.style.cursor='default'; }
      else { (function(pg){arr.addEventListener('click',function(){goPage(pg);});})((_page+1)); }
      pagDiv.appendChild(arr);
    }
  }

  function goPage(n) {
    _page=n; renderTable();
    var tbl=tbody.closest('.table-responsive');
    if (tbl) tbl.scrollIntoView({behavior:'smooth',block:'start'});
  }

  function applyFilter() {
    var q   = searchInput  ? searchInput.value.toLowerCase().trim() : '';
    var sel = filterSelect ? filterSelect.value : 'all';
    _filtered = _allData.filter(function(item) {
      var name=(item.name||'').toLowerCase();
      var kat=katFrom((item.category&&item.category.name)?item.category.name:'');
      return (!q||name.indexOf(q)!==-1||kat.indexOf(q)!==-1)&&(sel==='all'||kat===sel);
    });
    _page=1; renderTable();
  }
  if (searchInput)  searchInput.addEventListener('input',  applyFilter);
  if (filterSelect) filterSelect.addEventListener('change', applyFilter);

  window.reloadPotensiTable = function () { loadPotensi(true); };
  function loadPotensi(keepPage) {
    if (!keepPage) _page=1;
    // Muat semua halaman dari API (per_page maks 50) agar tabel admin selalu lengkap
    var page = 1, lastPage = 1, collected = [];
    function fetchPage() {
      fetch(API_BASE+'/potensi?per_page=50&page='+page,{headers:hdrs(false)})
        .then(function(r){return r.json();})
        .then(function(json){
          var rows = (json&&Array.isArray(json.data))?json.data:[];
          collected = collected.concat(rows);
          lastPage = (json&&json.meta&&json.meta.last_page)?json.meta.last_page:1;
          if (page < lastPage) { page++; fetchPage(); return; }
          _allData = collected;
          applyFilter();
        })
        .catch(function(){
          tbody.innerHTML='<tr class="empty-row text-center"><td colspan="6" class="py-4 text-muted">Gagal memuat data potensi.</td></tr>';
          pagDiv.innerHTML='';
          showAdminToast('Gagal memuat data potensi.','error');
        });
    }
    fetchPage();
  }

  tbody.addEventListener('click',function(e){
    var btnH=e.target.closest('.btn-hapus');
    var btnE=e.target.closest('.btn-edit-row');
    if (btnH) {
      var tr=btnH.closest('tr'), id=tr?tr.dataset.id:null;
      var name=tr&&tr.cells[1]?tr.cells[1].textContent:'';
      if (!id) return;
      if (!confirm('Hapus potensi "'+name.trim()+'"? Data tidak bisa dikembalikan.')) return;
      btnH.disabled=true;
      fetch(API_BASE+'/potensi/'+id,{method:'DELETE',headers:hdrs(false)})
        .then(function(r){
          if (r.status===404){loadPotensi(true);showAdminToast('Data telah dihapus.','error');return;}
          return r.json().then(function(json){
            if (json&&(json.status==='success'||r.ok)){
              _allData=_allData.filter(function(x){return String(x.id)!==String(id);});
              applyFilter();
              var tot=Math.max(1,Math.ceil(_filtered.length/PAGE_SIZE));
              if (_page>tot) _page=tot;
              renderTable();
              showAdminToast('Data telah dihapus.','error');
            } else {btnH.disabled=false;showAdminToast('Gagal menghapus data.','error');}
          });
        })
        .catch(function(){btnH.disabled=false;showAdminToast('Gagal menghapus data.','error');});
    }
    var btnV = e.target.closest('.btn-lihat-row');
    if (btnV) {
      var trV = btnV.closest('tr'), idV = trV ? trV.dataset.id : null;
      if (!idV) return;
      var itemV = _allData.find(function(x){ return String(x.id) === String(idV); });
      if (itemV && window.openDetailPotensiAdmin) window.openDetailPotensiAdmin(itemV);
      return;
    }
    if (btnE) {
      var tr=btnE.closest('tr'), id=tr?tr.dataset.id:null;
      if (!id) return;
      var item=_allData.find(function(x){return String(x.id)===String(id);});
      if (item) openEditModal(item);
    }
  });

  var _categories=[];
  function loadCategories(cb) {
    if (_categories.length){cb();return;}
    fetch(API_BASE+'/categories',{headers:hdrs(false)})
      .then(function(r){return r.json();})
      .then(function(json){_categories=(json&&Array.isArray(json.data))?json.data:[];cb();})
      .catch(function(){cb();});
  }

  function openEditModal(item) {
    loadCategories(function(){
      var sel=document.getElementById('editPotensiCategory');
      sel.innerHTML='<option value="">-- Pilih Kategori --</option>';
      _categories.forEach(function(c){
        var o=document.createElement('option');o.value=c.id;o.textContent=c.name;sel.appendChild(o);
      });
      document.getElementById('editPotensiId').value      = item.id;
      document.getElementById('editPotensiName').value    = item.name||'';
      document.getElementById('editPotensiOwner').value   = item.owner_name||'';
      document.getElementById('editPotensiWa').value      = item.whatsapp||'';
      document.getElementById('editPotensiEmail').value   = item.email||'';
      document.getElementById('editPotensiAddress').value = item.address||'';
      document.getElementById('editPotensiDesc').value    = item.description||'';
      document.getElementById('editPotensiLocation').value= item.location||'';
      document.getElementById('editPotensiJenis').value   = item.jenis||'';
      document.getElementById('editPotensiJam').value     = item.jam_operasional||'';
      var catId=(item.category_id)||(item.category?item.category.id:'');
      sel.value=catId||'';
      document.getElementById('editPotensiStatus').value  = item.status||'approved';
      ['editPotensiName','editPotensiOwner','editPotensiWa','editPotensiEmail',
       'editPotensiAddress','editPotensiDesc'].forEach(function(fid){
        document.getElementById(fid).classList.remove('is-invalid');
        var e=document.getElementById(fid+'Err');if(e)e.textContent='';
      });
      bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditPotensi')).show();
    });
  }
  window.openEditModal = openEditModal;

  function setInv(fid,msg){
    var el=document.getElementById(fid),err=document.getElementById(fid+'Err');
    el.classList.add('is-invalid');if(err)err.textContent=msg;return el;
  }
  function clrInv(fid){
    var el=document.getElementById(fid),err=document.getElementById(fid+'Err');
    el.classList.remove('is-invalid');if(err)err.textContent='';
  }
  ['editPotensiName','editPotensiOwner','editPotensiWa','editPotensiEmail',
   'editPotensiAddress','editPotensiDesc'].forEach(function(fid){
    var el=document.getElementById(fid);if(el)el.addEventListener('input',function(){clrInv(fid);});
  });

  function validateEdit() {
    var ok=true,first=null;
    var nm=document.getElementById('editPotensiName').value.trim();
    if(!nm||nm.length<3||nm.length>100){first=first||setInv('editPotensiName','Nama potensi minimal 3 karakter (maks 100).');ok=false;}else clrInv('editPotensiName');
    var ow=document.getElementById('editPotensiOwner').value.trim();
    var owRe=/^[A-Za-z\u00C0-\u024F\s.'\u2019\-]+$/;
    if(!ow||ow.length<3||ow.length>100||!owRe.test(ow)){first=first||setInv('editPotensiOwner','Nama pemilik minimal 3 huruf dan hanya boleh berisi huruf.');ok=false;}else clrInv('editPotensiOwner');
    var wa=document.getElementById('editPotensiWa').value.replace(/[\s\-()]/g,'');
    if(/^\+?62/.test(wa))wa='0'+wa.replace(/^\+?62/,'');
    if(!/^08[1-9][0-9]{7,10}$/.test(wa)){first=first||setInv('editPotensiWa','Nomor telepon tidak valid. Contoh: 08123456789.');ok=false;}
    else{clrInv('editPotensiWa');document.getElementById('editPotensiWa').value=wa;}
    var em=document.getElementById('editPotensiEmail').value.trim().toLowerCase();
    if(em){var eRe=/^[a-z0-9._%+\-]+@[a-z0-9\-]+(\.[a-z0-9\-]+)*\.[a-z]{2,}$/;
      if(em.length>100||!eRe.test(em)){first=first||setInv('editPotensiEmail','Format email tidak valid. Contoh: nama@gmail.com.');ok=false;}else clrInv('editPotensiEmail');
    }else clrInv('editPotensiEmail');
    var ad=document.getElementById('editPotensiAddress').value.trim();
    if(!ad||ad.length<10||ad.length>255){first=first||setInv('editPotensiAddress','Alamat minimal 10 karakter (maks 255).');ok=false;}else clrInv('editPotensiAddress');
    var ds=document.getElementById('editPotensiDesc').value.trim();
    if(!ds||ds.length<20||ds.length>1000){first=first||setInv('editPotensiDesc','Deskripsi minimal 20 karakter (maks 1000).');ok=false;}else clrInv('editPotensiDesc');
    if(first)first.focus();
    return ok;
  }

  var btnSimpan=document.getElementById('btnSimpanPotensi');
  if(btnSimpan){
    btnSimpan.addEventListener('click',function(){
      if(!validateEdit())return;
      var id=document.getElementById('editPotensiId').value;
      var catVal=document.getElementById('editPotensiCategory').value;
      var body={
        name:            document.getElementById('editPotensiName').value.trim(),
        owner_name:      document.getElementById('editPotensiOwner').value.trim(),
        category_id:     catVal?parseInt(catVal,10):null,
        whatsapp:        document.getElementById('editPotensiWa').value.trim(),
        email:           document.getElementById('editPotensiEmail').value.trim().toLowerCase()||null,
        address:         document.getElementById('editPotensiAddress').value.trim(),
        description:     document.getElementById('editPotensiDesc').value.trim(),
        location:        document.getElementById('editPotensiLocation').value.trim()||null,
        jenis:           document.getElementById('editPotensiJenis').value.trim()||null,
        jam_operasional: document.getElementById('editPotensiJam').value.trim()||null,
        status:          document.getElementById('editPotensiStatus').value
      };
      btnSimpan.disabled=true;
      fetch(API_BASE+'/potensi/'+id,{method:'PUT',headers:hdrs(true),body:JSON.stringify(body)})
        .then(function(r){return r.json().then(function(j){return{ok:r.ok,j:j};});})
        .then(function(res){
          if(res.ok){
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditPotensi')).hide();
            loadPotensi(true);
            showAdminToast('Data potensi berhasil diperbarui.','success');
          }else{btnSimpan.disabled=false;showAdminToast('Gagal menyimpan perubahan.','error');}
        })
        .catch(function(){btnSimpan.disabled=false;showAdminToast('Gagal menyimpan perubahan.','error');});
    });
    document.getElementById('modalEditPotensi').addEventListener('hidden.bs.modal',function(){
      btnSimpan.disabled=false;
    });
  }

  loadPotensi(false);

})(); /* end Kelola Potensi IIFE */

/* -- Dropdown Kategori Custom -- */
(function () {
  'use strict';

  var sel    = document.getElementById('filterKategori');
  var wrap   = document.getElementById('katDropdown');
  var toggle = document.getElementById('katDropdownToggle');
  var menu   = document.getElementById('katDropdownMenu');
  var label  = toggle ? toggle.querySelector('.kat-dropdown-label') : null;

  if (!sel || !wrap || !toggle || !menu || !label) return;

  /* Build <li> items from <option> */
  function buildItems() {
    menu.innerHTML = '';
    Array.from(sel.options).forEach(function (opt) {
      var li = document.createElement('li');
      li.setAttribute('role', 'option');
      li.dataset.value = opt.value;
      li.setAttribute('tabindex', '-1');

      var txt = document.createElement("span");
      txt.textContent = opt.text;

      var chk = document.createElement('i');
      chk.className = 'bi bi-check2';
      chk.setAttribute('aria-hidden', 'true');

      li.appendChild(txt);
      li.appendChild(chk);

      li.addEventListener('click', function () { choose(opt.value); });
      li.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); choose(opt.value); }
        else if (e.key === 'ArrowDown') { e.preventDefault(); moveFocus(1); }
        else if (e.key === 'ArrowUp')   { e.preventDefault(); moveFocus(-1); }
        else if (e.key === 'Escape')    { closeMenu(); toggle.focus(); }
        else if (e.key === 'Tab')       { closeMenu(); }
      });

      menu.appendChild(li);
    });
    markActive(sel.value);
  }

  function markActive(val) {
    Array.from(menu.children).forEach(function (li) {
      var active = li.dataset.value === val;
      li.classList.toggle('active', active);
      li.setAttribute('aria-selected', active ? 'true' : 'false');
    });
    toggle.classList.toggle('is-filtered', val !== 'all');
  }

  function updateLabel(val) {
    var opt = Array.from(sel.options).find(function (o) { return o.value === val; });
    label.textContent = opt ? opt.text : 'Semua Kategori';
  }

  function openMenu() {
    menu.removeAttribute('hidden');
    wrap.classList.add('open');
    toggle.setAttribute('aria-expanded', 'true');
    /* focus active item or first item */
    var active = menu.querySelector('li.active') || menu.querySelector('li');
    if (active) active.focus();
  }

  function closeMenu() {
    menu.setAttribute('hidden', '');
    wrap.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
  }

  function moveFocus(dir) {
    var items = Array.from(menu.querySelectorAll('li'));
    var cur   = document.activeElement;
    var idx   = items.indexOf(cur);
    var next  = items[Math.max(0, Math.min(items.length - 1, idx + dir))];
    if (next) next.focus();
  }

  function choose(val) {
    var changed = sel.value !== val;
    if (changed) {
      sel.value = val;
      sel.dispatchEvent(new Event('change', { bubbles: true }));
    }
    updateLabel(val);
    markActive(val);
    closeMenu();
    toggle.focus();
  }

  toggle.addEventListener('click', function (e) {
    e.stopPropagation();
    if (menu.hasAttribute('hidden')) { buildItems(); openMenu(); }
    else { closeMenu(); }
  });

  toggle.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
      e.preventDefault();
      buildItems(); openMenu();
    }
  });

  /* Close on outside click */
  document.addEventListener('click', function (e) {
    if (!wrap.contains(e.target)) closeMenu();
  });

  /* Close on Esc from anywhere inside wrap */
  wrap.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeMenu(); toggle.focus(); }
  });

  /* Init label */
  updateLabel(sel.value);
  buildItems();

})(); /* end Dropdown Kategori IIFE */


(function () {
  'use strict';

  /* â”€â”€â”€ Konstanta â”€â”€â”€ */
  const API_BASE = (window.location.protocol === 'file:' || window.location.port === '5500')
    ? 'http://localhost:8000/api'
    : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');

  /* â”€â”€â”€ Token helper â”€â”€â”€ */
  function getAuthToken() {
    try {
      var s = localStorage.getItem('ep_admin_session') || sessionStorage.getItem('ep_admin_session');
      if (s) return JSON.parse(s).token || null;
    } catch (e) {}
    return null;
  }
  function authHeaders(json) {
    var h = { 'Accept': 'application/json' };
    if (json) h['Content-Type'] = 'application/json';
    var t = getAuthToken(); if (t) h['Authorization'] = 'Bearer ' + t;
    return h;
  }
  function handle401(res) {
    if (res.status === 401) {
      // Gunakan fungsi bersama dari auth.js jika sudah dimuat; fallback manual jika belum.
      if (typeof window.epHandleUnauthorized === 'function') {
        window.epHandleUnauthorized();
      } else {
        ['adminLoggedIn', 'adminUser', 'ep_admin_session'].forEach(function (k) { localStorage.removeItem(k); });
        sessionStorage.removeItem('ep_admin_session');
        window.location.replace('/login?unauthorized=true');
      }
      return true;
    }
    return false;
  }
  function extractApiError(data) {
    if (data && data.errors) {
      var first = Object.values(data.errors)[0];
      if (Array.isArray(first) && first.length) return first[0];
    }
    return null;
  }

  /* â”€â”€â”€ Panel kedua: navigasi â”€â”€â”€ */
  var pengHome  = document.getElementById('peng-home');
  var pengPanel = document.getElementById('peng-panel');
  var pengTitle = document.getElementById('peng-panel-title');
  var pengBody  = document.getElementById('peng-panel-body');
  var pengBack  = document.getElementById('peng-back-btn');

  function openPanel(title, templateId, onOpen) {
    var tmpl = document.getElementById(templateId);
    if (!tmpl) return;
    pengTitle.textContent = title;
    pengBody.innerHTML = '';
    pengBody.appendChild(tmpl.cloneNode(true));
    // Clone tidak perlu display:none — ubah tampilan
    pengBody.firstChild.style.display = '';
    pengHome.style.display  = 'none';
    pengPanel.style.display = '';
    var contentEl = document.querySelector('.admin-content');
    if (contentEl) contentEl.scrollTop = 0;
    if (typeof onOpen === 'function') onOpen();
  }

  function goBack() {
    pengPanel.style.display = 'none';
    pengHome.style.display  = '';
    var contentEl = document.querySelector('.admin-content');
    if (contentEl) contentEl.scrollTop = 0;
  }

  pengBack.addEventListener('click', goBack);

  /* â”€â”€â”€ Keluar â”€â”€â”€ */
  document.getElementById('peng-btn-keluar').addEventListener('click', function () {
    // Gunakan fungsi logout yang sudah ada di auth.js jika tersedia
    var logoutBtn = document.getElementById('btnLogoutSidebar');
    if (logoutBtn) { logoutBtn.click(); return; }
    if (confirm('Apakah Anda yakin ingin keluar?')) {
      localStorage.removeItem('adminLoggedIn');
      localStorage.removeItem('adminUser');
      localStorage.removeItem('ep_admin_session');
      sessionStorage.removeItem('ep_admin_session');
      window.location.replace('/login');
    }
  });

  /* â”€â”€â”€ Tampilan & Maintenance â”€â”€â”€ */
  document.getElementById('peng-open-tampilan').addEventListener('click', function () {
    openPanel('Tampilan & Maintenance', 'tmpl-tampilan', function () {
      bindToggles();
    });
  });

  /* â”€â”€â”€ Notifikasi â”€â”€â”€ */
  document.getElementById('peng-open-notif').addEventListener('click', function () {
    openPanel('Notifikasi', 'tmpl-notif', function () {
      bindToggles();
    });
  });

  /* â”€â”€â”€ Tentang Aplikasi â”€â”€â”€ */
  document.getElementById('peng-open-tentang').addEventListener('click', function () {
    openPanel('Tentang Aplikasi', 'tmpl-tentang');
  });

  /* â”€â”€â”€ Logo & Identitas â”€â”€â”€ */
  document.getElementById('peng-open-logo').addEventListener('click', function () {
    openPanel('Logo & Identitas', 'tmpl-logo', function () {
      initLogoPanel();
    });
  });

  /* â”€â”€â”€ Toggle change handler â”€â”€â”€ */
  function bindToggles() {
    pengBody.querySelectorAll('.form-check-input[type=checkbox]').forEach(function (cb) {
      cb.addEventListener('change', function () {
        showAdminToast('Pengaturan diperbarui.', 'info');
      });
    });
  }

  /* ═══════════════════════════════════════════════════════════
     LOGO & IDENTITAS PANEL
     ═══════════════════════════════════════════════════════════ */
  function initLogoPanel() {
    var LOGO_ALLOWED = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    var LOGO_MAX     = 2 * 1024 * 1024; // 2 MB (sesuai upload_max_filesize PHP)

    // Muat logo saat ini dari API
    fetch(API_BASE + '/settings/logo')
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.status === 'success' && res.data && res.data.path) {
          var prev = pengBody.querySelector('#logoCurrentPreview');
          if (prev) prev.src = res.data.path;
        }
      }).catch(function () {});

    var dropzone    = pengBody.querySelector('#logoDropzone');
    var fileInput   = pengBody.querySelector('#logoFileInput');
    var btnTelusuri = pengBody.querySelector('#logoBtnTelusuri');
    var dropContent = pengBody.querySelector('#logoDropContent');
    var pickedWrap  = pengBody.querySelector('#logoPickedWrap');
    var pickedImg   = pengBody.querySelector('#logoPickedPreview');
    var pickedName  = pengBody.querySelector('#logoPickedName');
    var pickedSize  = pengBody.querySelector('#logoPickedSize');
    var btnHapus    = pengBody.querySelector('#logoBtnHapusPilihan');
    var btnBatalkan = pengBody.querySelector('#logoBtnBatalkan');
    var btnSimpan   = pengBody.querySelector('#btnSimpanLogo');

    if (!dropzone || !fileInput) return;

    // Helper: format ukuran file
    function formatSize(bytes) {
      if (bytes < 1024)    return bytes + ' B';
      if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
      return (bytes / 1048576).toFixed(2) + ' MB';
    }

    // Helper: tampilkan file terpilih
    function showPicked(file) {
      var reader = new FileReader();
      reader.onload = function (e) {
        pickedImg.src = e.target.result;
        pickedName.textContent = file.name;
        pickedSize.textContent = formatSize(file.size);
        dropContent.style.display = 'none';
        pickedWrap.style.display  = 'block';
        btnSimpan.disabled = false;
      };
      reader.readAsDataURL(file);
    }

    // Helper: reset pilihan
    function resetPicked() {
      fileInput.value = '';
      pickedImg.src   = '';
      pickedName.textContent = '';
      pickedSize.textContent = '';
      pickedWrap.style.display  = 'none';
      dropContent.style.display = 'block';
      btnSimpan.disabled = true;
      dropzone.style.borderColor = '';
      dropzone.style.background  = '';
    }

    // Validasi file
    function validateFile(file) {
      if (!LOGO_ALLOWED.includes(file.type)) {
        showAdminToast('Format tidak didukung. Gunakan JPG, PNG, atau WebP.', 'error');
        return false;
      }
      if (file.size > LOGO_MAX) {
        showAdminToast('Ukuran file melebihi 2 MB.', 'error');
        return false;
      }
      return true;
    }

    // Klik tombol Telusuri
    btnTelusuri.addEventListener('click', function (e) { e.stopPropagation(); fileInput.click(); });

    // Klik area dropzone
    dropzone.addEventListener('click', function () { fileInput.click(); });

    // Input file change
    fileInput.addEventListener('change', function () {
      var file = this.files[0]; if (!file) return;
      if (!validateFile(file)) { this.value = ''; return; }
      showPicked(file);
    });

    // Drag-and-drop
    dropzone.addEventListener('dragover', function (e) {
      e.preventDefault();
      dropzone.style.borderColor = 'var(--clr-blue,#094a74)';
      dropzone.style.background  = 'rgba(9,74,116,.05)';
    });
    dropzone.addEventListener('dragleave', function () {
      dropzone.style.borderColor = '';
      dropzone.style.background  = '';
    });
    dropzone.addEventListener('drop', function (e) {
      e.preventDefault();
      dropzone.style.borderColor = '';
      dropzone.style.background  = '';
      var file = e.dataTransfer.files[0]; if (!file) return;
      if (!validateFile(file)) return;
      var dt = new DataTransfer(); dt.items.add(file); fileInput.files = dt.files;
      showPicked(file);
    });

    // Tombol Hapus pilihan
    btnHapus.addEventListener('click', function (e) { e.stopPropagation(); resetPicked(); });

    // Tombol Batalkan
    btnBatalkan.addEventListener('click', function () { resetPicked(); });

    // Tombol Simpan Logo
    btnSimpan.addEventListener('click', async function () {
      var file = fileInput.files[0];
      if (!file) { showAdminToast('Pilih file logo terlebih dahulu.', 'error'); return; }
      var btn = this; btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengunggah...';
      var fd = new FormData(); fd.append('logo', file);
      var h = { 'Accept': 'application/json' };
      var t = getAuthToken(); if (t) h['Authorization'] = 'Bearer ' + t;
      try {
        var res = await fetch(API_BASE + '/settings/logo', { method: 'POST', headers: h, body: fd });
        if (handle401(res)) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-cloud-upload me-1"></i> Simpan Logo'; return; }
        var data = await res.json();
        if (res.ok && data.status === 'success') {
          var curPrev = pengBody.querySelector('#logoCurrentPreview');
          if (curPrev) curPrev.src = data.data.path;
          document.querySelectorAll('[data-site-logo]').forEach(function (img) { img.src = data.data.path; });
          resetPicked();
          showAdminToast('Logo berhasil diperbarui.', 'success');
        } else {
          showAdminToast(extractApiError(data) || data.message || 'Gagal mengunggah logo.', 'error');
        }
      } catch (e) { showAdminToast('Tidak dapat terhubung ke server.', 'error'); }
      finally { btn.disabled = false; btn.innerHTML = '<i class="bi bi-cloud-upload me-1"></i> Simpan Logo'; }
    });
  }

  /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     KEAMANAN (Ganti Password) PANEL
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
  document.getElementById('peng-open-keamanan').addEventListener('click', function () {
    openPanel('Keamanan', 'tmpl-keamanan', function () {
      initKeamananPanel();
    });
  });

  function initKeamananPanel() {
    var body        = pengBody;
    var curPass     = body.querySelector('#secCurrentPass');
    var newPass     = body.querySelector('#secNewPass');
    var confirmPass = body.querySelector('#secConfirmPass');
    var btnTerapkan = body.querySelector('#secBtnTerapkan');
    var btnBatal    = body.querySelector('#secBtnBatal');
    var btnBersih   = body.querySelector('#secBtnBersihkan');
    var bar1 = body.querySelector('#strengthBar1');
    var bar2 = body.querySelector('#strengthBar2');
    var bar3 = body.querySelector('#strengthBar3');
    var lbl  = body.querySelector('#strengthLabel');
    var reqLen   = body.querySelector('#req-len');
    var reqUpper = body.querySelector('#req-upper');
    var reqNum   = body.querySelector('#req-num');

    // Eye-toggle
    body.querySelectorAll('.peng-eye-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var inp = body.querySelector('#' + this.dataset.target);
        var ico = this.querySelector('i');
        if (!inp) return;
        if (inp.type === 'password') { inp.type = 'text'; ico.className = 'bi bi-eye-slash'; }
        else { inp.type = 'password'; ico.className = 'bi bi-eye'; }
      });
    });

    // Password strength checker
    function checkStrength() {
      var v = newPass.value;
      var okLen   = v.length >= 8;
      var okUpper = /[A-Z]/.test(v);
      var okNum   = /[0-9]/.test(v);
      var score   = (okLen ? 1 : 0) + (okUpper ? 1 : 0) + (okNum ? 1 : 0);

      function setReq(el, ok) {
        el.querySelector('i').className = ok ? 'bi bi-check-circle-fill' : 'bi bi-x-circle-fill';
        el.querySelector('i').style.color = ok ? 'var(--clr-green)' : '#aaa';
        el.style.color = ok ? 'var(--clr-green)' : 'var(--text-mid)';
      }
      setReq(reqLen,   okLen);
      setReq(reqUpper, okUpper);
      setReq(reqNum,   okNum);

      var colors = ['#e0e7ef', '#e0e7ef', '#e0e7ef'];
      if (score === 1) { colors[0] = '#dc3545'; lbl.textContent = 'Lemah'; lbl.style.color = '#dc3545'; }
      else if (score === 2) { colors[0] = '#ffc107'; colors[1] = '#ffc107'; lbl.textContent = 'Sedang'; lbl.style.color = '#b8860b'; }
      else if (score === 3) { colors[0] = 'var(--clr-green)'; colors[1] = 'var(--clr-green)'; colors[2] = 'var(--clr-green)'; lbl.textContent = 'Kuat'; lbl.style.color = 'var(--clr-green)'; }
      else { lbl.textContent = ''; }
      bar1.style.background = colors[0]; bar2.style.background = colors[1]; bar3.style.background = colors[2];

      var allOk = okLen && okUpper && okNum && confirmPass.value && confirmPass.value === newPass.value;
      btnTerapkan.disabled = !allOk;
    }

    newPass.addEventListener('input', checkStrength);
    confirmPass.addEventListener('input', checkStrength);

    // Bersihkan
    btnBersih.addEventListener('click', function () {
      curPass.value = ''; newPass.value = ''; confirmPass.value = '';
      bar1.style.background = bar2.style.background = bar3.style.background = '#e0e7ef';
      lbl.textContent = '';
      ['req-len','req-upper','req-num'].forEach(function (id) {
        var el = body.querySelector('#' + id);
        el.querySelector('i').className = 'bi bi-x-circle-fill';
        el.querySelector('i').style.color = '#aaa';
        el.style.color = 'var(--text-mid)';
      });
      btnTerapkan.disabled = true;
    });

    // Batalkan
    btnBatal.addEventListener('click', function () {
      btnBersih.click(); goBack();
    });

    // Terapkan Perubahan
    btnTerapkan.addEventListener('click', async function () {
      var current  = curPass.value;
      var password = newPass.value;
      var confirm  = confirmPass.value;
      if (!current) { showAdminToast('Password lama tidak boleh kosong.', 'error'); return; }
      var btn = this; btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';
      try {
        var res = await fetch(API_BASE + '/admin/password', {
          method: 'PUT',
          headers: authHeaders(true),
          body: JSON.stringify({ current_password: current, password: password, password_confirmation: confirm })
        });
        if (handle401(res)) { btn.disabled = false; btn.innerHTML = 'Terapkan Perubahan'; return; }
        var data = await res.json();
        if (res.ok && data.status === 'success') {
          curPass.value = ''; newPass.value = ''; confirmPass.value = '';
          showAdminToast('Password berhasil diperbarui.', 'success');
          setTimeout(goBack, 900);
        } else {
          showAdminToast(extractApiError(data) || data.message || 'Gagal mengganti password.', 'error');
        }
      } catch (e) { showAdminToast('Tidak dapat terhubung ke server.', 'error'); }
      finally { btn.disabled = false; btn.innerHTML = 'Terapkan Perubahan'; checkStrength(); }
    });
  }

  /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     PROFIL ADMIN â€” Modal
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
  document.getElementById('peng-open-profil').addEventListener('click', function () {
    openProfilModal();
  });

  function openProfilModal() {
    var modal = document.getElementById('modalProfilAdmin');
    if (modal && typeof bootstrap !== 'undefined') {
      loadProfilData();
      new bootstrap.Modal(modal).show();
    }
  }

  function loadProfilData() {
    var t = getAuthToken();
    if (!t || t.startsWith('ep-local-session') || t.startsWith('ep-session-') || t.startsWith('session-')) {
      fillProfilFromSession(); return;
    }
    fetch(API_BASE + '/admin/profile', { headers: authHeaders() })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.status === 'success' && res.data) {
          document.getElementById('mpName').value  = res.data.name  || '';
          document.getElementById('mpEmail').value = res.data.email || '';
          if (res.data.avatar) {
            document.getElementById('mpAvatarImg').src = res.data.avatar;
            document.getElementById('mpAvatarImg').style.display = '';
            document.getElementById('mpAvatarIcon').style.display = 'none';
          }
        } else { fillProfilFromSession(); }
      }).catch(fillProfilFromSession);
  }

  function fillProfilFromSession() {
    try {
      var u = JSON.parse(localStorage.getItem('adminUser') || '{}');
      if (u.name)  document.getElementById('mpName').value  = u.name;
      if (u.email) document.getElementById('mpEmail').value = u.email;
    } catch (e) {}
  }

  // Bind profil modal events (dipanggil sekali saat DOMContentLoaded)
  (function bindProfilModal() {
    var AVATAR_ALLOWED = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    var AVATAR_MAX     = 10 * 1024 * 1024; // 10 MB (UI), backend max 2MB per php.ini

    var avatarInput = document.getElementById('mpAvatarInput');
    var avatarImg   = document.getElementById('mpAvatarImg');
    var avatarIcon  = document.getElementById('mpAvatarIcon');
    var btnPilih    = document.getElementById('mpBtnPilihFoto');
    var btnBatal    = document.getElementById('mpBtnBatal');
    var btnSimpan   = document.getElementById('mpBtnSimpan');

    if (!avatarInput) return; // modal belum ada di DOM â€” tidak mungkin tapi aman

    btnPilih.addEventListener('click', function () { avatarInput.click(); });

    avatarInput.addEventListener('change', function () {
      var file = this.files[0]; if (!file) return;
      if (!AVATAR_ALLOWED.includes(file.type)) {
        showAdminToast('Format tidak didukung. Gunakan JPG, PNG, atau WebP.', 'error');
        this.value = ''; return;
      }
      if (file.size > AVATAR_MAX) {
        showAdminToast('Ukuran file melebihi 2 MB.', 'error');
        this.value = ''; return;
      }
      var reader = new FileReader();
      reader.onload = function (e) {
        avatarImg.src = e.target.result;
        avatarImg.style.display = '';
        avatarIcon.style.display = 'none';
      };
      reader.readAsDataURL(file);
    });

    btnBatal.addEventListener('click', function () {
      var modal = bootstrap.Modal.getInstance(document.getElementById('modalProfilAdmin'));
      if (modal) modal.hide();
      avatarInput.value = '';
    });

    btnSimpan.addEventListener('click', async function () {
      var name  = document.getElementById('mpName').value.trim();
      var email = document.getElementById('mpEmail').value.trim();
      if (!name)  { showAdminToast('Nama tidak boleh kosong.', 'error'); return; }
      if (!email) { showAdminToast('Email tidak boleh kosong.', 'error'); return; }

      var btn = this; btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';

      var fd = new FormData();
      fd.append('name', name);
      fd.append('email', email);
      var file = avatarInput.files[0];
      if (file) fd.append('avatar', file);

      var h = { 'Accept': 'application/json' };
      var t = getAuthToken(); if (t) h['Authorization'] = 'Bearer ' + t;

      try {
        var res = await fetch(API_BASE + '/admin/profile', { method: 'POST', headers: h, body: fd });
        if (handle401(res)) { btn.disabled = false; btn.innerHTML = 'Simpan Perubahan'; return; }
        var data = await res.json();
        if (res.ok && data.status === 'success') {
          // Update localStorage
          try { var u = JSON.parse(localStorage.getItem('adminUser') || '{}'); u.name = name; u.email = email; localStorage.setItem('adminUser', JSON.stringify(u)); } catch (e) {}
          // Jika ada avatar di topbar/sidebar, perbarui
          if (data.data && data.data.avatar) {
            document.querySelectorAll('#adminAvatarImg').forEach(function (el) { el.src = data.data.avatar; });
          }
          showAdminToast('Profil berhasil disimpan.', 'success');
          var modal = bootstrap.Modal.getInstance(document.getElementById('modalProfilAdmin'));
          if (modal) modal.hide();
        } else {
          showAdminToast(extractApiError(data) || data.message || 'Gagal menyimpan profil.', 'error');
        }
      } catch (e) { showAdminToast('Tidak dapat terhubung ke server.', 'error'); }
      finally { btn.disabled = false; btn.innerHTML = 'Simpan Perubahan'; }
    });
  })();

  /* â”€â”€ Settings Toggles (di halaman utama â€” tidak ada lagi, tapi jaga-jaga toggle di DOM asli) â”€â”€ */
  document.querySelectorAll('#section-pengaturan > .settings-section .form-check-input[type=checkbox]').forEach(function(cb){
    cb.addEventListener('change', function(){ showAdminToast('Pengaturan diperbarui.','info'); });
  });

})(); /* end Pengaturan IIFE */

/* ── Galeri Admin Dinamis ── */
(function () {
  'use strict';
  var API = (window.location.protocol === 'file:' || window.location.port === '5500')
    ? 'http://localhost:8000/api'
    : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');

  var grid = document.getElementById('galeriAdminGrid');
  var fileInput = document.getElementById('galeriFileInput');
  var editModalEl = document.getElementById('modalEditGaleri');
  var btnSimpan = document.getElementById('btnSimpanJudulGaleri');
  if (!grid) return;

  function getToken() {
    try {
      var s = localStorage.getItem('ep_admin_session') || sessionStorage.getItem('ep_admin_session');
      if (s) return JSON.parse(s).token || null;
    } catch (e) {}
    return null;
  }

  function getHeaders(isJson) {
    var h = { 'Accept': 'application/json' };
    if (isJson) h['Content-Type'] = 'application/json';
    var t = getToken();
    if (t) h['Authorization'] = 'Bearer ' + t;
    return h;
  }

  function escapeHtml(s) {
    if (s == null) return '';
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function loadGaleriAdmin(onLoaded) {
    fetch(API + '/galeri', { headers: getHeaders(false) })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        var items = (res && Array.isArray(res.data)) ? res.data : [];
        renderGaleriGrid(items);
        if (typeof onLoaded === 'function') onLoaded(items);
      })
      .catch(function() {
        showAdminToast('Gagal memuat data galeri.', 'error');
      });
  }

  function renderGaleriGrid(items) {
    grid.innerHTML = '';

    if (items.length === 0) {
      var emptyDiv = document.createElement('div');
      emptyDiv.className = 'col-12 py-5 text-center text-muted';
      emptyDiv.style.gridColumn = '1 / -1';
      emptyDiv.id = 'galeriEmptyMsg';
      emptyDiv.innerHTML = '<i class="bi bi-images me-2"></i>Belum ada foto kegiatan.';
      grid.appendChild(emptyDiv);
    } else {
      items.forEach(function(g) {
        var div = document.createElement('div');
        div.className = 'galeri-admin-item';
        div.dataset.galeriId = g.id;
        div.dataset.galeriDesc = g.description || '';
        var safeSrc = window.sanitizeSrc ? window.sanitizeSrc(g.image_path) : escapeHtml(g.image_path);
        div.innerHTML =
          '<div class="galeri-admin-thumb">' +
            '<img src="' + safeSrc + '" alt="' + escapeHtml(g.caption) + '">' +
            '<div class="galeri-admin-actions">' +
              '<button type="button" class="btn btn-sm btn-warning btn-galeri-edit" data-id="' + g.id + '" style="font-size:.78rem;padding:.3rem .75rem;">' +
                '<i class="bi bi-pencil me-1"></i>Edit' +
              '</button>' +
              '<button type="button" class="btn btn-sm btn-danger btn-galeri-hapus" data-id="' + g.id + '" style="font-size:.78rem;padding:.3rem .75rem;">' +
                '<i class="bi bi-trash me-1"></i>Hapus' +
              '</button>' +
            '</div>' +
          '</div>' +
          '<div class="galeri-admin-info">' +
            '<div class="galeri-caption">' + escapeHtml(g.caption) + '</div>' +
            '<p class="galeri-admin-desc">' + escapeHtml(g.description || 'Belum ada deskripsi kegiatan.') + '</p>' +
          '</div>';

        // Bind Edit
        div.querySelector('.btn-galeri-edit').addEventListener('click', function() {
          document.getElementById('editGaleriId').value = g.id;
          document.getElementById('editGaleriCaption').value = g.caption || '';
          document.getElementById('editGaleriDesc').value = g.description || '';
          bootstrap.Modal.getOrCreateInstance(editModalEl).show();
        });

        // Bind Hapus
        div.querySelector('.btn-galeri-hapus').addEventListener('click', function() {
          if (!confirm('Hapus foto ini dari galeri?')) return;
          var btn = this;
          btn.disabled = true;
          fetch(API + '/galeri/' + g.id, {
            method: 'DELETE',
            headers: getHeaders(false)
          })
            .then(function(r) { return r.json().then(function(data){ return { ok: r.ok, data: data }; }); })
            .then(function(res) {
              if (res.ok && res.data.status === 'success') {
                showAdminToast(res.data.message || 'Foto galeri berhasil dihapus.', 'success');
                loadGaleriAdmin();
              } else {
                btn.disabled = false;
                showAdminToast((res.data && res.data.message) ? res.data.message : 'Gagal menghapus foto.', 'error');
              }
            })
            .catch(function() {
              btn.disabled = false;
              showAdminToast('Server tidak dapat dihubungi.', 'error');
            });
        });

        grid.appendChild(div);
      });
    }

    // Tombol tambah foto
    var addBtn = document.createElement('label');
    addBtn.className = 'galeri-add-btn';
    addBtn.htmlFor = 'galeriFileInput';
    addBtn.id = 'galeriAddBtn';
    addBtn.innerHTML = '<i class="bi bi-plus-circle"></i><span>Tambah Foto</span>';
    grid.appendChild(addBtn);
  }

  // Upload handler
  if (fileInput) {
    fileInput.addEventListener('change', function() {
      var files = Array.from(this.files);
      if (!files.length) return;

      var uploads = files.map(function(file) {
        if (!file.type.startsWith('image/')) {
          showAdminToast('File "' + file.name + '" bukan gambar yang valid.', 'error');
          return Promise.resolve();
        }

        var caption = file.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' ');
        caption = caption.charAt(0).toUpperCase() + caption.slice(1);

        var fd = new FormData();
        fd.append('image', file);
        fd.append('caption', caption);
        fd.append('description', '');

        return fetch(API + '/galeri', {
          method: 'POST',
          headers: getHeaders(false),
          body: fd
        })
          .then(function(r) { return r.json().then(function(data){ return { ok: r.ok, data: data }; }); })
          .then(function(res) {
            if (res.ok && res.data.status === 'success') {
              showAdminToast(res.data.message || 'Foto "' + caption + '" berhasil ditambahkan.', 'success');
              return res.data.data;
            } else {
              showAdminToast((res.data && res.data.message) ? res.data.message : 'Gagal menambahkan foto.', 'error');
              return null;
            }
          })
          .catch(function() {
            showAdminToast('Server tidak dapat dihubungi.', 'error');
            return null;
          });
      });

      Promise.all(uploads).then(function(results) {
        var added = results.filter(Boolean);
        loadGaleriAdmin(function() {
          if (added.length === 1 && added[0]) {
            var item = added[0];
            document.getElementById('editGaleriId').value = item.id;
            document.getElementById('editGaleriCaption').value = item.caption;
            document.getElementById('editGaleriDesc').value = item.description || '';
            bootstrap.Modal.getOrCreateInstance(editModalEl).show();
          }
        });
      });

      this.value = '';
    });
  }

  // Simpan edit galeri
  if (btnSimpan) {
    btnSimpan.addEventListener('click', function() {
      var id   = document.getElementById('editGaleriId').value;
      var cap  = document.getElementById('editGaleriCaption').value.trim();
      var desc = document.getElementById('editGaleriDesc').value.trim();

      if (!cap) {
        showAdminToast('Judul kegiatan wajib diisi.', 'error');
        return;
      }

      btnSimpan.disabled = true;
      fetch(API + '/galeri/' + id, {
        method: 'PUT',
        headers: getHeaders(true),
        body: JSON.stringify({ caption: cap, description: desc })
      })
        .then(function(r) { return r.json().then(function(data){ return { ok: r.ok, data: data }; }); })
        .then(function(res) {
          btnSimpan.disabled = false;
          if (res.ok && res.data.status === 'success') {
            var modalInst = bootstrap.Modal.getInstance(editModalEl);
            if (modalInst) modalInst.hide();
            showAdminToast(res.data.message || 'Judul dan deskripsi foto berhasil diperbarui.', 'success');
            loadGaleriAdmin();
          } else {
            showAdminToast((res.data && res.data.message) ? res.data.message : 'Gagal memperbarui kegiatan.', 'error');
          }
        })
        .catch(function() {
          btnSimpan.disabled = false;
          showAdminToast('Server tidak dapat dihubungi.', 'error');
        });
    });
  }

  window.loadGaleriAdmin = loadGaleriAdmin;
  var navGaleri = document.querySelector('[data-section="galeri"]');
  if (navGaleri) {
    navGaleri.addEventListener('click', function() {
      loadGaleriAdmin();
    });
  }

  loadGaleriAdmin();
})();

/* ─── PROFIL DESA ADMIN ─── */
(function() {
  var API = (window.location.protocol === 'file:' || window.location.port === '5500')
    ? 'http://localhost:8000/api'
    : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');

  function getHeaders(json) {
    var h = { 'Accept': 'application/json' };
    if (json) h['Content-Type'] = 'application/json';
    var tok = null;
    try {
      var raw = localStorage.getItem('ep_admin_session') || sessionStorage.getItem('ep_admin_session');
      if (raw) tok = JSON.parse(raw).token || null;
      if (!tok) tok = localStorage.getItem('token');
    } catch(e) {}
    if (tok) h['Authorization'] = 'Bearer ' + tok;
    return h;
  }

  var _serverData = {};

  function populateForm(data) {
    if (!data) return;
    var elAlamat = document.getElementById('admDesaAlamat');
    var elKodePos = document.getElementById('admDesaKodePos');
    var elTelepon = document.getElementById('admDesaTelepon');
    var elEmail = document.getElementById('admDesaEmail');
    var elKepala = document.getElementById('admDesaKepala');
    var elJam = document.getElementById('admDesaJam');
    var elWa = document.getElementById('admDesaWa');
    var elIg = document.getElementById('admDesaIg');
    var elJmlKK = document.getElementById('admDesaJmlKK');

    if (elAlamat) elAlamat.value = data.desa_alamat || '';
    if (elKodePos) elKodePos.value = data.desa_kode_pos || '';
    if (elTelepon) elTelepon.value = data.desa_telepon || '';
    if (elEmail) elEmail.value = data.desa_email || '';
    if (elKepala) elKepala.value = data.desa_kepala || '';
    if (elJam) elJam.value = data.desa_jam_operasional || '';
    if (elWa) elWa.value = data.desa_whatsapp || '';
    if (elIg) elIg.value = data.desa_instagram || '';
    if (elJmlKK) elJmlKK.value = (data.desa_jumlah_kk !== undefined && data.desa_jumlah_kk !== null) ? data.desa_jumlah_kk : '';
  }

  function loadProfilDesa() {
    fetch(API + '/admin/profil-desa', {
      headers: getHeaders(false)
    })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        if (res.status === 'success' && res.data) {
          _serverData = Object.assign({}, res.data);
          populateForm(_serverData);
          var badge = document.getElementById('badgeStatusProfil');
          if (badge) {
            badge.textContent = 'Data Tersimpan';
          }
        }
      })
      .catch(function() {
        // Abaikan kegagalan jaringan saat load awal
      });
  }

  var btnSimpan = document.getElementById('saveProfileBtn');
  var btnBatal = document.getElementById('btnBatalProfil');

  if (btnSimpan) {
    btnSimpan.addEventListener('click', function() {
      var payload = {
        desa_alamat: (document.getElementById('admDesaAlamat') ? document.getElementById('admDesaAlamat').value : '').trim(),
        desa_kode_pos: (document.getElementById('admDesaKodePos') ? document.getElementById('admDesaKodePos').value : '').trim(),
        desa_telepon: (document.getElementById('admDesaTelepon') ? document.getElementById('admDesaTelepon').value : '').trim(),
        desa_email: (document.getElementById('admDesaEmail') ? document.getElementById('admDesaEmail').value : '').trim(),
        desa_jam_operasional: (document.getElementById('admDesaJam') ? document.getElementById('admDesaJam').value : '').trim(),
        desa_whatsapp: (document.getElementById('admDesaWa') ? document.getElementById('admDesaWa').value : '').trim(),
        desa_instagram: (document.getElementById('admDesaIg') ? document.getElementById('admDesaIg').value : '').trim(),
        desa_kepala: (document.getElementById('admDesaKepala') ? document.getElementById('admDesaKepala').value : '').trim(),
        desa_jumlah_kk: (document.getElementById('admDesaJmlKK') ? document.getElementById('admDesaJmlKK').value : '').trim()
      };

      btnSimpan.disabled = true;
      var originalHtml = btnSimpan.innerHTML;
      btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';

      fetch(API + '/admin/profil-desa', {
        method: 'PUT',
        headers: getHeaders(true),
        body: JSON.stringify(payload)
      })
        .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, status: r.status, data: data }; }); })
        .then(function(res) {
          btnSimpan.disabled = false;
          btnSimpan.innerHTML = originalHtml;

          if (res.ok && res.data.status === 'success') {
            _serverData = Object.assign({}, res.data.data);
            populateForm(_serverData);
            showAdminToast(res.data.message || 'Profil desa berhasil diperbarui.', 'success');
            var badge = document.getElementById('badgeStatusProfil');
            if (badge) badge.textContent = 'Baru saja disimpan';
          } else {
            var errMsg = 'Gagal menyimpan profil desa.';
            if (res.data && res.data.errors) {
              var firstKey = Object.keys(res.data.errors)[0];
              if (firstKey && res.data.errors[firstKey].length) {
                errMsg = res.data.errors[firstKey][0];
              }
            } else if (res.data && res.data.message) {
              errMsg = res.data.message;
            }
            showAdminToast(errMsg, 'error');
          }
        })
        .catch(function() {
          btnSimpan.disabled = false;
          btnSimpan.innerHTML = originalHtml;
          showAdminToast('Server tidak dapat dihubungi.', 'error');
        });
    });
  }

  if (btnBatal) {
    btnBatal.addEventListener('click', function() {
      populateForm(_serverData);
      showAdminToast('Perubahan dibatalkan.', 'info');
    });
  }

  window.loadProfilDesa = loadProfilDesa;

  var navProfil = document.querySelector('[data-section="profil-desa-admin"]');
  if (navProfil) {
    navProfil.addEventListener('click', function() {
      loadProfilDesa();
    });
  }

  loadProfilDesa();
})();
</script>
<script src="assets/js/utils.js"></script>
<script src="assets/js/auth.js"></script>
</body>
</html>
