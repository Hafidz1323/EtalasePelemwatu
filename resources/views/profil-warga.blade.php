<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Warga — EtalasePelemwatu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/profil-warga.css">
</head>
<body class="pw-page-body">

<!-- NAVBAR (Publik — Tidak Diubah) -->
<header id="siteHeader">
  <nav class="navbar navbar-expand-lg" id="mainNavbar">
    <div class="container">
      <a class="navbar-brand-wrap" href="/">
        <img class="brand-logo" src="assets/img/logo-desa.png" alt="Logo" data-site-logo>
        <div class="brand-text-wrap">
          <span class="brand-name">EtalasePelemwatu</span>
          <span class="brand-sub">Desa Pelemwatu</span>
        </div>
      </a>
      <button class="navbar-toggler ms-auto me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navMenuProfil">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenuProfil">
        <ul class="navbar-nav mx-auto gap-1">
          <li class="nav-item"><a class="nav-link" href="/">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="/#katalog">Katalog</a></li>
        </ul>
        <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0 ms-lg-2">
          <div id="navWargaWrap"></div>
          <a href="/daftar-potensi" class="nav-link btn-nav-daftar" id="navBtnDaftar">
            Daftarkan Potensi
            <span class="nav-cta-arrow"><i class="bi bi-arrow-up-right"></i></span>
          </a>
        </div>
      </div>
    </div>
  </nav>
</header>

<!-- MAIN FLOATING CARD WRAPPER -->
<main class="pw-container">
  <div class="pw-card-main">
    
    <!-- BANNER COVER & OVERLAPPING AVATAR -->
    <div class="pw-cover-wrapper">
      <div class="pw-cover"></div>
      <div class="pw-avatar-wrap" id="avatarWrap">
        <span class="pw-avatar-initial">W</span>
      </div>
    </div>

    <!-- USER IDENTITY & TOP ACTIONS -->
    <div class="pw-identity-section">
      <div class="pw-identity-main">
        <div class="pw-name-badge-row">
          <h1 class="pw-profile-name" id="profileName">Memuat...</h1>
        </div>
        <div class="pw-profile-sub">
          <span class="pw-sub-item" id="profilePhone"><i class="bi bi-telephone"></i> -</span>
          <span class="text-muted d-none d-sm-inline">&bull;</span>
          <span class="pw-sub-item text-muted"><i class="bi bi-geo-alt"></i> Desa Pelemwatu, Menganti, Gresik</span>
        </div>
      </div>
      <div class="pw-identity-actions">
        <a href="/#katalog" class="pw-btn-secondary pw-btn-sm" title="Jelajahi Katalog Potensi">
          <i class="bi bi-grid-fill me-1"></i> Lihat Katalog
        </a>
      </div>
    </div>

    <!-- STATISTICS ROW (Separated by Thin Vertical Dividers) -->
    <div class="pw-stats-row">
      <div class="pw-stat-col">
        <div class="pw-stat-label">Potensi Terdaftar</div>
        <div class="pw-stat-val"><span id="statPotensiCount">0</span> Usaha</div>
      </div>
      <div class="pw-stat-divider"></div>
      <div class="pw-stat-col">
        <div class="pw-stat-label">Nomor WhatsApp</div>
        <div class="pw-stat-val" id="statPhone">-</div>
      </div>
      <div class="pw-stat-divider"></div>
      <div class="pw-stat-col">
        <div class="pw-stat-label">Email Terdaftar</div>
        <div class="pw-stat-val" id="statEmail">-</div>
      </div>
      <div class="pw-stat-divider"></div>
      <div class="pw-stat-col">
        <div class="pw-stat-label">Status Warga</div>
        <div class="pw-stat-val" style="color:var(--pw-green);">
          <i class="bi bi-check-circle-fill me-1"></i> Terverifikasi
        </div>
      </div>
    </div>

    <!-- SEGMENTED CONTROL / PILL TABS -->
    <div class="pw-tabs-wrapper">
      <ul class="nav nav-pills pw-segmented-tabs" id="profilTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="tab-profil-btn" data-bs-toggle="tab" data-bs-target="#tab-profil" type="button" role="tab">
            <i class="bi bi-person-gear"></i> Profil Saya
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="tab-potensi-btn" data-bs-toggle="tab" data-bs-target="#tab-potensi" type="button" role="tab">
            <i class="bi bi-shop"></i> Potensi Saya
            <span class="pw-tab-badge" id="potensiCount">0</span>
          </button>
        </li>
      </ul>
    </div>

    <!-- TAB CONTENTS -->
    <div class="tab-content pw-tab-content">

      <!-- TAB: PROFIL (Settings Rows Layout) -->
      <div class="tab-pane fade show active" id="tab-profil" role="tabpanel">

        <!-- Row 1: Data Profil -->
        <div class="pw-settings-row">
          <div class="pw-settings-info">
            <h3 class="pw-settings-title"><i class="bi bi-person-vcard me-1 text-primary"></i> Data Profil</h3>
            <p class="pw-settings-desc">
              Informasi identitas warga. Nama lengkap dan kontak aktif akan memudahkan pembeli dan warga desa menghubungi Anda.
            </p>
          </div>
          <div class="pw-settings-form">
            <div class="alert-inline error" id="editAlert">
              <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
              <span id="editAlertMsg"></span>
            </div>
            <div class="alert-inline success" id="editSuccess">
              <i class="bi bi-check-circle-fill flex-shrink-0"></i>
              <span>Profil berhasil diperbarui.</span>
            </div>
            <form id="formEditProfil">
              <div class="mb-3">
                <label class="pw-label" for="editNama">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control pw-input" id="editNama" maxlength="100" placeholder="Masukkan nama lengkap">
              </div>
              <div class="mb-3">
                <label class="pw-label" for="editPhone">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                <input type="tel" class="form-control pw-input" id="editPhone" maxlength="20" inputmode="tel" placeholder="Contoh: 081234567890">
              </div>
              <div class="mb-4">
                <label class="pw-label" for="editEmail">Alamat Email <span class="pw-hint">(opsional)</span></label>
                <input type="email" class="form-control pw-input" id="editEmail" maxlength="100" placeholder="nama@email.com">
              </div>
              <div class="d-flex justify-content-end">
                <button type="submit" class="pw-btn-primary" id="btnSaveProfil">
                  Simpan Perubahan
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="pw-row-divider"></div>

        <!-- Row 2: Keamanan Akun & Password -->
        <div class="pw-settings-row">
          <div class="pw-settings-info">
            <h3 class="pw-settings-title"><i class="bi bi-shield-lock me-1 text-primary"></i> Keamanan Akun</h3>
            <p class="pw-settings-desc">
              Pastikan akun Anda terlindungi dengan menggunakan kata sandi unik minimal 8 karakter.
            </p>
          </div>
          <div class="pw-settings-form">
            <div class="alert-inline error" id="passAlert">
              <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
              <span id="passAlertMsg"></span>
            </div>
            <div class="alert-inline success" id="passSuccess">
              <i class="bi bi-check-circle-fill flex-shrink-0"></i>
              <span>Password berhasil diperbarui.</span>
            </div>
            <form id="formPassword">
              <div class="mb-3">
                <label class="pw-label" for="passLama">Password Lama <span class="text-danger">*</span></label>
                <div class="position-relative">
                  <input type="password" class="form-control pw-input pe-5" id="passLama" maxlength="100" placeholder="Masukkan password saat ini">
                  <button type="button" class="pw-eye-btn" onclick="togglePasswordVisibility('passLama', this)" aria-label="Toggle password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
              <div class="mb-3">
                <label class="pw-label" for="passBaru">Password Baru <span class="text-danger">*</span> <span class="pw-hint">(min. 8 karakter)</span></label>
                <div class="position-relative">
                  <input type="password" class="form-control pw-input pe-5" id="passBaru" maxlength="100" placeholder="Masukkan password baru">
                  <button type="button" class="pw-eye-btn" onclick="togglePasswordVisibility('passBaru', this)" aria-label="Toggle password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
              <div class="mb-4">
                <label class="pw-label" for="passBaruConfirm">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                <div class="position-relative">
                  <input type="password" class="form-control pw-input pe-5" id="passBaruConfirm" maxlength="100" placeholder="Ulangi password baru">
                  <button type="button" class="pw-eye-btn" onclick="togglePasswordVisibility('passBaruConfirm', this)" aria-label="Toggle password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
              <div class="d-flex justify-content-end">
                <button type="submit" class="pw-btn-secondary" id="btnSavePass">
                  Perbarui Password
                </button>
              </div>
            </form>
          </div>
        </div>

      </div><!-- /tab-profil -->

      <!-- TAB: POTENSI SAYA -->
      <div class="tab-pane fade" id="tab-potensi" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
          <div>
            <h3 class="pw-settings-title mb-1"><i class="bi bi-shop me-1 text-primary"></i> Daftar Potensi Saya</h3>
            <p class="pw-settings-desc mb-0">Kelola usaha, produk, jasa, atau hunian yang telah Anda daftarkan di desa.</p>
          </div>
          <a href="/daftar-potensi" class="pw-btn-primary pw-btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Usaha Baru
          </a>
        </div>

        <div id="potensiLoadingState" class="text-center py-5">
          <div class="spinner-sm"></div> <span style="color:#64748B;font-size:.9rem;margin-left:.6rem;">Memuat data potensi...</span>
        </div>
        
        <div id="potensiEmptyState" class="pw-empty-state" style="display:none;">
          <div class="pw-empty-icon">
            <i class="bi bi-shop-window"></i>
          </div>
          <h4 class="pw-empty-title">Belum Ada Potensi Terdaftar</h4>
          <p class="pw-empty-desc">
            Daftarkan usaha, jasa, produk kuliner, atau hunian Anda agar warga dan tamu desa dapat dengan mudah menemukannya di katalog digital.
          </p>
          <a href="/daftar-potensi" class="pw-btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Daftarkan Sekarang
          </a>
        </div>

        <div id="potensiList" class="d-flex flex-column gap-3"></div>
      </div><!-- /tab-potensi -->

    </div><!-- /tab-content -->

  </div>
</main>

<!-- MODAL EDIT POTENSI (Struktur & ID Utuh Sesuai Aslinya) -->
<div class="modal fade" id="modalEditPotensi" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:24px;border:1px solid #E2E8F0;box-shadow:0 12px 36px rgba(9,74,116,.15);">
      <div class="modal-header" style="border-bottom:1px solid #E2E8F0;padding:1.25rem 1.5rem;">
        <h5 class="modal-title fw-bold" style="color:var(--pw-blue);font-size:1.15rem;">
          <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Data Potensi
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="alert-inline error" id="editPotensiAlert" style="margin-bottom:1rem;">
          <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span id="editPotensiAlertMsg"></span>
        </div>
        <input type="hidden" id="editPotensiId">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="pw-label">Nama Potensi <span class="text-danger">*</span></label>
            <input type="text" class="form-control pw-input" id="epNama" maxlength="100">
          </div>
          <div class="col-md-6">
            <label class="pw-label">Kategori</label>
            <select class="form-select pw-input" id="epKategori"><option value="">-- Pilih --</option></select>
          </div>
          <div class="col-12">
            <label class="pw-label">Deskripsi <span class="text-danger">*</span></label>
            <textarea class="form-control pw-input" id="epDeskripsi" rows="4" maxlength="1000" style="height:auto !important;border-radius:14px !important;"></textarea>
            <div class="form-text" style="font-size:.78rem;">Minimal 20 karakter.</div>
          </div>
          <div class="col-md-6">
            <label class="pw-label">Nomor Telepon / WA <span class="text-danger">*</span></label>
            <input type="tel" class="form-control pw-input" id="epWa" maxlength="20" inputmode="tel">
          </div>
          <div class="col-md-6">
            <label class="pw-label">Jenis / Subkategori</label>
            <input type="text" class="form-control pw-input" id="epJenis" maxlength="100">
          </div>
          <div class="col-12">
            <label class="pw-label">Alamat / Lokasi Usaha</label>
            <input type="text" class="form-control pw-input" id="epLokasi" maxlength="255">
          </div>
          <div class="col-md-6">
            <label class="pw-label">Jam Operasional</label>
            <input type="text" class="form-control pw-input" id="epJam" maxlength="100">
          </div>
          <div class="col-12">
            <label class="pw-label">Foto Baru <span class="pw-hint">(opsional, ganti semua foto lama)</span></label>
            <input type="file" class="form-control pw-input" id="epFoto" accept="image/jpeg,image/jpg,image/png,image/webp" multiple style="padding-top:.45rem;">
            <div class="form-text" style="font-size:.78rem;">Maks. 3 foto, 2 MB per foto. JPG/PNG/WebP.</div>
          </div>
          <div class="col-12" id="currentPhotoWrap" style="display:none;">
            <label class="pw-label">Foto Saat Ini</label>
            <div id="currentPhotos" class="d-flex gap-2 flex-wrap"></div>
            <div class="form-text" style="font-size:.78rem;">Klik × untuk hapus foto individual.</div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="border-top:1px solid #E2E8F0;padding:1rem 1.5rem;gap:.5rem;">
        <button type="button" class="pw-btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="pw-btn-primary" id="btnSavePotensi">
          Simpan Perubahan
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/warga-auth.js"></script>
<script>
'use strict';
var API_BASE = window.epWarga ? window.epWarga.apiBase : '/api';

/* ── Guard: redirect jika belum login (warga-auth.js sudah handle, ini fallback) ── */
if (!window.epWarga || !window.epWarga.isLoggedIn()) {
  window.location.replace('/register?next=profil-warga');
}

var currentUser = window.epWarga.getUser();
var categories  = [];

/* ── Toggle password eye ── */
function togglePasswordVisibility(id, btn) {
  var inp = document.getElementById(id);
  if (!inp) return;
  var isPass = inp.type === 'password';
  inp.type = isPass ? 'text' : 'password';
  var icon = btn.querySelector('i');
  if (icon) {
    icon.className = isPass ? 'bi bi-eye-slash' : 'bi bi-eye';
  }
}

/* ── Render header profil ── */
function renderProfileHeader() {
  var name = currentUser.name || 'Warga';
  var initial = name.trim().charAt(0).toUpperCase() || 'W';
  var avatarWrap = document.getElementById('avatarWrap');
  if (avatarWrap) {
    if (currentUser.avatar || currentUser.photo) {
      avatarWrap.innerHTML = '<img src="' + (currentUser.avatar || currentUser.photo) + '" alt="' + name + '" class="pw-avatar-img">';
    } else {
      avatarWrap.innerHTML = '<span class="pw-avatar-initial">' + initial + '</span>';
    }
  }

  var pName = document.getElementById('profileName');
  if (pName) pName.textContent = name;
  var pPhone = document.getElementById('profilePhone');
  if (pPhone) pPhone.innerHTML = '<i class="bi bi-telephone me-1"></i> ' + (currentUser.phone || '-');

  var sPhone = document.getElementById('statPhone');
  if (sPhone) sPhone.textContent = currentUser.phone || '-';
  var sEmail = document.getElementById('statEmail');
  if (sEmail) sEmail.textContent = currentUser.email || 'Belum diatur';

  var eNama = document.getElementById('editNama');
  if (eNama) eNama.value = currentUser.name || '';
  var ePhone = document.getElementById('editPhone');
  if (ePhone) ePhone.value = currentUser.phone || '';
  var eEmail = document.getElementById('editEmail');
  if (eEmail) eEmail.value = currentUser.email || '';
}

/* ── Alert helpers ── */
function showInlineAlert(id, msg) {
  var el = document.getElementById(id);
  var msgEl = document.getElementById(id + 'Msg');
  if (el) el.classList.add('show');
  if (msgEl) msgEl.textContent = msg;
}
function hideInlineAlert(id) {
  var el = document.getElementById(id); if (el) el.classList.remove('show');
}

/* ── API helper ── */
async function apiCall(method, path, body, isJson) {
  var token = window.epWarga.getToken();
  var headers = { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token };
  var opts = { method: method, headers: headers };
  if (body) {
    if (isJson !== false && !(body instanceof FormData)) {
      headers['Content-Type'] = 'application/json';
      opts.body = JSON.stringify(body);
    } else {
      opts.body = body;
    }
  }
  var res = await fetch(API_BASE + path, opts);
  if (res.status === 401) { window.epWarga.logout(); return null; }
  return { status: res.status, ok: res.ok, data: await res.json() };
}

/* ── Edit profil ── */
document.getElementById('formEditProfil').addEventListener('submit', async function(e) {
  e.preventDefault();
  hideInlineAlert('editAlert');
  hideInlineAlert('editSuccess');

  var nama  = document.getElementById('editNama').value.trim();
  var phone = document.getElementById('editPhone').value.trim().replace(/[\s\-()]/g,'').replace(/^\+?62(?=8)/,'0');
  var email = document.getElementById('editEmail').value.trim().toLowerCase() || null;

  if (!nama)  return showInlineAlert('editAlert', 'Nama wajib diisi.');
  if (!phone || !/^08[1-9][0-9]{7,10}$/.test(phone)) return showInlineAlert('editAlert', 'Nomor HP tidak valid.');

  document.getElementById('btnSaveProfil').disabled = true;
  var res = await apiCall('POST', '/warga/profile', { name: nama, phone: phone, email: email });
  document.getElementById('btnSaveProfil').disabled = false;

  if (!res) return;
  if (res.ok) {
    var newUser = Object.assign({}, currentUser, { name: nama, phone: phone, email: email });
    window.epWarga.saveSession(window.epWarga.getToken(), newUser);
    currentUser = newUser;
    renderProfileHeader();
    document.getElementById('editSuccess').classList.add('show');
    setTimeout(function() { hideInlineAlert('editSuccess'); }, 3000);
  } else {
    var msg = res.data.message || 'Gagal menyimpan.';
    if (res.data.errors) {
      var k = Object.keys(res.data.errors)[0];
      if (k) msg = res.data.errors[k][0];
    }
    showInlineAlert('editAlert', msg);
  }
});

/* ── Ganti password ── */
document.getElementById('formPassword').addEventListener('submit', async function(e) {
  e.preventDefault();
  hideInlineAlert('passAlert');
  hideInlineAlert('passSuccess');

  var lama   = document.getElementById('passLama').value;
  var baru   = document.getElementById('passBaru').value;
  var konfirm = document.getElementById('passBaruConfirm').value;

  if (!lama) return showInlineAlert('passAlert', 'Password lama wajib diisi.');
  if (!baru || baru.length < 8) return showInlineAlert('passAlert', 'Password baru minimal 8 karakter.');
  if (baru !== konfirm) return showInlineAlert('passAlert', 'Konfirmasi password tidak cocok.');

  document.getElementById('btnSavePass').disabled = true;
  var res = await apiCall('PUT', '/warga/password', {
    current_password: lama, password: baru, password_confirmation: konfirm
  });
  document.getElementById('btnSavePass').disabled = false;

  if (!res) return;
  if (res.ok) {
    document.getElementById('formPassword').reset();
    document.getElementById('passSuccess').classList.add('show');
    setTimeout(function() { hideInlineAlert('passSuccess'); }, 3000);
  } else {
    var msg = res.data.message || 'Gagal.';
    if (res.data.errors) {
      var k = Object.keys(res.data.errors)[0];
      if (k) msg = res.data.errors[k][0];
    }
    showInlineAlert('passAlert', msg);
  }
});

/* ── Load kategori ── */
async function loadCategories() {
  try {
    var res = await fetch(API_BASE + '/categories');
    var data = await res.json();
    if (data && Array.isArray(data.data)) {
      categories = data.data;
      var sel = document.getElementById('epKategori');
      categories.forEach(function(c) {
        var opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.name;
        sel.appendChild(opt);
      });
    }
  } catch(e) {}
}

/* ── Load potensi warga ── */
async function loadPotensi() {
  document.getElementById('potensiLoadingState').style.display = '';
  document.getElementById('potensiEmptyState').style.display   = 'none';
  document.getElementById('potensiList').innerHTML = '';

  var res = await apiCall('GET', '/warga/potensi?per_page=50');
  document.getElementById('potensiLoadingState').style.display = 'none';
  if (!res) return;

  var items = res.data.data || [];
  var total = res.data.meta ? res.data.meta.total : items.length;
  document.getElementById('potensiCount').textContent = total;
  var statCount = document.getElementById('statPotensiCount');
  if (statCount) statCount.textContent = total;

  if (!items.length) {
    document.getElementById('potensiEmptyState').style.display = '';
    return;
  }

  var list = document.getElementById('potensiList');
  items.forEach(function(p) {
    var statusHtml = '';
    if (p.status === 'approved') statusHtml = '<span class="status-badge badge-approved"><i class="bi bi-check-circle me-1"></i>Disetujui</span>';
    else if (p.status === 'pending') statusHtml = '<span class="status-badge badge-pending"><i class="bi bi-clock me-1"></i>Menunggu Verifikasi</span>';
    else if (p.status === 'rejected') statusHtml = '<span class="status-badge badge-rejected"><i class="bi bi-x-circle me-1"></i>Ditolak</span>';

    var photosHtml = '';
    if (p.images && p.images.length) {
      photosHtml = '<div class="d-flex gap-2 flex-wrap mt-2">';
      p.images.forEach(function(img) {
        photosHtml += '<img src="' + (img.path || '') + '" class="photo-thumb" onerror="this.style.display=\'none\'">';
      });
      photosHtml += '</div>';
    }

    var rejHtml = '';
    if (p.status === 'rejected' && p.rejection_reason) {
      rejHtml = '<div class="rejection-box"><i class="bi bi-info-circle me-1"></i><strong>Alasan penolakan:</strong> ' + p.rejection_reason + '</div>';
    }

    var isDeletionPending = !!p.deletion_requested_at;
    var canEdit = (p.status === 'pending' || p.status === 'rejected') && !isDeletionPending;

    // Label status penghapusan
    var delHtml = '';
    if (isDeletionPending) {
      delHtml = '<div class="mt-2"><span class="status-badge" style="background:rgba(220,53,69,.12);color:#8b1a24;border:1px solid rgba(220,53,69,.25);">' +
        '<i class="bi bi-hourglass-split me-1"></i>Menunggu Persetujuan Hapus Admin</span></div>';
    }

    var card = document.createElement('div');
    card.className = 'potensi-card';
    card.innerHTML =
      '<div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">' +
        '<div class="flex-grow-1">' +
          '<div class="potensi-card-title">' + p.name + '</div>' +
          '<div class="potensi-card-meta">' +
            (p.category ? p.category.name + ' &bull; ' : '') +
            new Date(p.created_at).toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}) +
          '</div>' +
          statusHtml +
          rejHtml +
          delHtml +
          photosHtml +
        '</div>' +
        '<div class="d-flex gap-1 flex-shrink-0 align-items-start">' +
          (canEdit ? '<button class="btn-ikon-edit" title="Edit Potensi" data-id="' + p.id + '"><i class="bi bi-pencil"></i></button>' : '') +
          (!isDeletionPending
            ? '<button class="btn-ikon-hapus" title="' + (p.status === 'approved' ? 'Ajukan Hapus ke Admin' : 'Hapus Potensi') + '" data-id="' + p.id + '" data-status="' + p.status + '"><i class="bi bi-trash"></i></button>'
            : '<button class="btn-ikon-hapus btn-cancel-del" title="Batalkan Permintaan Hapus" data-id="' + p.id + '" style="color:#f59e0b;border-color:#f59e0b;"><i class="bi bi-x-circle"></i></button>'
          ) +
        '</div>' +
      '</div>';

    card.querySelector('.btn-ikon-edit') && card.querySelector('.btn-ikon-edit').addEventListener('click', function() {
      openEditModal(p);
    });

    var hapusBtn = card.querySelector('.btn-ikon-hapus');
    if (hapusBtn) {
      hapusBtn.addEventListener('click', function() {
        var isCancelDel = this.classList.contains('btn-cancel-del');
        if (isCancelDel) {
          batalkanPermintaanHapus(p.id, p.name);
        } else {
          ajukanHapusPotensi(p.id, p.name, p.status);
        }
      });
    }

    list.appendChild(card);
  });
}

/* ── Ajukan / langsung hapus potensi ── */
async function ajukanHapusPotensi(id, nama, status) {
  var isApproved = (status === 'approved');
  var konfirmMsg = isApproved
    ? 'Potensi "' + nama + '" sudah disetujui. Karena tidak bisa langsung dihapus, permintaan penghapusan akan dikirim ke admin untuk ditinjau.\n\nLanjutkan?'
    : 'Hapus potensi "' + nama + '"? Tindakan ini tidak dapat dibatalkan.';
  if (!confirm(konfirmMsg)) return;
  var res = await apiCall('POST', '/warga/potensi/' + id + '/request-deletion');
  if (!res) return;
  if (res.ok) {
    alert(res.data.message || 'Berhasil.');
    loadPotensi();
  } else {
    alert(res.data.message || 'Gagal menghapus potensi.');
  }
}

/* ── Batalkan permintaan hapus ── */
async function batalkanPermintaanHapus(id, nama) {
  if (!confirm('Batalkan permintaan penghapusan untuk "' + nama + '"?')) return;
  var res = await apiCall('POST', '/warga/potensi/' + id + '/cancel-deletion');
  if (!res) return;
  if (res.ok) {
    loadPotensi();
  } else {
    alert(res.data.message || 'Gagal membatalkan.');
  }
}

/* ── Buka modal edit ── */
function openEditModal(p) {
  document.getElementById('editPotensiId').value    = p.id;
  document.getElementById('epNama').value           = p.name     || '';
  document.getElementById('epDeskripsi').value      = p.description || '';
  document.getElementById('epWa').value             = p.whatsapp  || '';
  document.getElementById('epJenis').value          = p.jenis     || '';
  document.getElementById('epLokasi').value         = p.location  || '';
  document.getElementById('epJam').value            = p.jam_operasional || '';
  document.getElementById('epKategori').value       = p.category_id || '';
  document.getElementById('epFoto').value           = '';
  hideInlineAlert('editPotensiAlert');

  // Foto saat ini
  var photoWrap = document.getElementById('currentPhotoWrap');
  var photoDiv  = document.getElementById('currentPhotos');
  photoDiv.innerHTML = '';
  if (p.images && p.images.length) {
    photoWrap.style.display = '';
    p.images.forEach(function(img) {
      var wrap = document.createElement('div');
      wrap.className = 'photo-thumb-wrap';
      wrap.innerHTML =
        '<img src="' + (img.path || '') + '" class="photo-thumb" onerror="this.style.display=\'none\'">' +
        '<button class="btn-del-photo" data-imgid="' + img.id + '" data-pid="' + p.id + '" title="Hapus foto">×</button>';
      wrap.querySelector('.btn-del-photo').addEventListener('click', async function() {
        var imgId = this.dataset.imgid;
        var pid   = this.dataset.pid;
        var r = await apiCall('DELETE', '/warga/potensi/' + pid + '/images/' + imgId);
        if (r && r.ok) { this.closest('.photo-thumb-wrap').remove(); }
        else alert(r ? (r.data.message || 'Gagal.') : 'Gagal.');
      });
      photoDiv.appendChild(wrap);
    });
  } else {
    photoWrap.style.display = 'none';
  }

  new bootstrap.Modal(document.getElementById('modalEditPotensi')).show();
}

/* ── Simpan edit potensi ── */
document.getElementById('btnSavePotensi').addEventListener('click', async function() {
  hideInlineAlert('editPotensiAlert');
  var id      = document.getElementById('editPotensiId').value;
  var nama    = document.getElementById('epNama').value.trim();
  var desk    = document.getElementById('epDeskripsi').value.trim();
  var wa      = document.getElementById('epWa').value.trim().replace(/[\s\-()]/g,'').replace(/^\+?62(?=8)/,'0');
  var jenis   = document.getElementById('epJenis').value.trim();
  var lokasi  = document.getElementById('epLokasi').value.trim();
  var jam     = document.getElementById('epJam').value.trim();
  var katId   = document.getElementById('epKategori').value;
  var fotoFiles = document.getElementById('epFoto').files;

  if (!nama) return showInlineAlert('editPotensiAlert', 'Nama potensi wajib diisi.');
  if (!desk || desk.length < 20) return showInlineAlert('editPotensiAlert', 'Deskripsi minimal 20 karakter.');
  if (!wa || !/^08[1-9][0-9]{7,10}$/.test(wa)) return showInlineAlert('editPotensiAlert', 'Nomor telepon tidak valid.');

  var fd = new FormData();
  fd.append('name', nama);
  fd.append('description', desk);
  fd.append('whatsapp', wa);
  if (jenis)  fd.append('jenis', jenis);
  if (lokasi) fd.append('location', lokasi);
  if (jam)    fd.append('jam_operasional', jam);
  if (katId)  fd.append('category_id', katId);
  for (var i = 0; i < Math.min(fotoFiles.length, 3); i++) {
    fd.append('images[' + i + ']', fotoFiles[i]);
  }

  this.disabled = true;
  var res = await apiCall('POST', '/warga/potensi/' + id, fd, false);
  this.disabled = false;

  if (!res) return;
  if (res.ok) {
    bootstrap.Modal.getInstance(document.getElementById('modalEditPotensi')).hide();
    loadPotensi();
  } else {
    var msg = res.data.message || 'Gagal menyimpan.';
    if (res.data.errors) {
      var k = Object.keys(res.data.errors)[0];
      if (k) msg = res.data.errors[k][0];
    }
    showInlineAlert('editPotensiAlert', msg);
  }
});

/* ── Tab potensi: load saat diklik ── */
document.getElementById('tab-potensi-btn').addEventListener('click', function() {
  loadPotensi();
});

/* ── Hash: buka tab potensi langsung ── */
if (window.location.hash === '#potensi') {
  setTimeout(function() { document.getElementById('tab-potensi-btn').click(); }, 100);
}

/* ── Init ── */
document.addEventListener('DOMContentLoaded', function() {
  renderProfileHeader();
  loadCategories();
});
</script>
<script src="assets/js/site-logo.js"></script>
</body>
</html>
