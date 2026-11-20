<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Warga — EtalasePelemwatu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body { background:#f4f7fb;padding-top:72px; }
    .profile-header { background:linear-gradient(135deg,#094a74,#0a6098);color:#fff;padding:2.5rem 0 1.5rem;margin-bottom:2rem; }
    .profile-avatar { width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,.3);background:#1a7ab0;display:flex;align-items:center;justify-content:center;font-size:2rem;color:#fff; }
    .profile-name { font-size:1.4rem;font-weight:700;margin-bottom:.2rem; }
    .profile-role { font-size:.82rem;opacity:.75;background:rgba(255,255,255,.15);border-radius:999px;padding:.2rem .75rem;display:inline-block; }
    .status-badge { font-size:.75rem;padding:.25rem .65rem;border-radius:999px;font-weight:600; }
    .badge-pending  { background:rgba(255,193,7,.15);color:#856404; }
    .badge-approved { background:rgba(40,167,69,.12);color:#1a6e30; }
    .badge-rejected { background:rgba(220,53,69,.1);color:#8b1a24; }
    .potensi-card { border:1.5px solid #e5e9f0;border-radius:14px;padding:1.1rem 1.25rem;background:#fff;margin-bottom:.85rem;transition:box-shadow .2s; }
    .potensi-card:hover { box-shadow:0 4px 16px rgba(9,74,116,.1); }
    .potensi-card-title { font-weight:700;color:var(--clr-blue);font-size:.95rem; }
    .potensi-card-meta { font-size:.8rem;color:#9ca3af;margin-top:.25rem; }
    .rejection-box { background:rgba(220,53,69,.07);border:1px solid rgba(220,53,69,.2);border-radius:8px;padding:.6rem .9rem;font-size:.82rem;color:#8b1a24;margin-top:.5rem; }
    .section-title { font-size:1rem;font-weight:700;color:var(--clr-blue);margin-bottom:1rem;display:flex;align-items:center;gap:.5rem; }
    .tab-wrapper .nav-link { color:#6b7280;font-weight:600;font-size:.9rem;border-radius:10px 10px 0 0; }
    .tab-wrapper .nav-link.active { color:var(--clr-orange);border-color:#dee2e6 #dee2e6 #fff; }
    .alert-inline { display:none;padding:.6rem 1rem;border-radius:8px;font-size:.84rem;margin-bottom:1rem; }
    .alert-inline.show { display:flex;align-items:center;gap:.5rem; }
    .spinner-sm { width:1.1rem;height:1.1rem;border:.2rem solid rgba(0,0,0,.12);border-top-color:var(--clr-orange);border-radius:50%;animation:spin .7s linear infinite;display:inline-block;vertical-align:middle; }
    @keyframes spin { to { transform:rotate(360deg); } }
    .empty-state { text-align:center;padding:3rem 1rem;color:#9ca3af; }
    .empty-state i { font-size:3rem;margin-bottom:.75rem;display:block;opacity:.4; }
    .btn-ikon-hapus { background:none;border:none;padding:.25rem .4rem;color:#dc3545;cursor:pointer;border-radius:6px; }
    .btn-ikon-hapus:hover { background:rgba(220,53,69,.1); }
    .btn-ikon-edit { background:none;border:none;padding:.25rem .4rem;color:var(--clr-blue);cursor:pointer;border-radius:6px; }
    .btn-ikon-edit:hover { background:rgba(9,74,116,.1); }
    .photo-thumb { width:60px;height:60px;object-fit:cover;border-radius:8px;border:1.5px solid #e5e9f0;cursor:pointer; }
    .photo-thumb-wrap { position:relative;display:inline-block; }
    .btn-del-photo { position:absolute;top:-6px;right:-6px;width:18px;height:18px;background:#dc3545;color:#fff;border:none;border-radius:50%;font-size:.6rem;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1; }
  </style>
</head>
<body>

<!-- NAVBAR -->
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

<!-- PROFILE HEADER -->
<div class="profile-header" id="profileHeader">
  <div class="container">
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <div class="profile-avatar" id="avatarWrap"><i class="bi bi-person"></i></div>
      <div>
        <div class="profile-name" id="profileName">Memuat...</div>
        <div class="profile-role">Warga Desa Pelemwatu</div>
        <div style="font-size:.82rem;opacity:.7;margin-top:.3rem;" id="profilePhone"></div>
      </div>
    </div>
  </div>
</div>

<!-- MAIN CONTENT -->
<div class="container pb-5">
  <div class="tab-wrapper">
    <ul class="nav nav-tabs mb-0" id="profilTabs">
      <li class="nav-item"><button class="nav-link active" id="tab-profil-btn" data-bs-toggle="tab" data-bs-target="#tab-profil">
        <i class="bi bi-person-gear me-1"></i>Profil Saya
      </button></li>
      <li class="nav-item"><button class="nav-link" id="tab-potensi-btn" data-bs-toggle="tab" data-bs-target="#tab-potensi">
        <i class="bi bi-shop me-1"></i>Potensi Saya <span class="badge bg-secondary ms-1 rounded-pill" id="potensiCount" style="font-size:.7rem;">0</span>
      </button></li>
    </ul>
    <div class="tab-content bg-white border border-top-0 rounded-bottom p-4" style="border-radius:0 8px 8px 8px;">

      <!-- TAB: PROFIL -->
      <div class="tab-pane fade show active" id="tab-profil">
        <div class="row g-4">

          <!-- Edit Profil -->
          <div class="col-lg-6">
            <div class="section-title"><i class="bi bi-person-lines-fill"></i>Edit Data Profil</div>
            <div class="alert-inline error" id="editAlert" style="background:rgba(220,53,69,.1);border:1px solid rgba(220,53,69,.25);color:#8b1a24;">
              <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span id="editAlertMsg"></span>
            </div>
            <div class="alert-inline" id="editSuccess" style="background:rgba(40,167,69,.1);border:1px solid rgba(40,167,69,.25);color:#1a6e30;">
              <i class="bi bi-check-circle-fill flex-shrink-0"></i><span>Profil berhasil diperbarui.</span>
            </div>
            <form id="formEditProfil">
              <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:.85rem;color:var(--clr-blue);">Nama Lengkap *</label>
                <input type="text" class="form-control" id="editNama" maxlength="100">
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:.85rem;color:var(--clr-blue);">Nomor HP *</label>
                <input type="tel" class="form-control" id="editPhone" maxlength="20" inputmode="tel">
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:.85rem;color:var(--clr-blue);">Email <span class="text-muted fw-normal">(opsional)</span></label>
                <input type="email" class="form-control" id="editEmail" maxlength="100">
              </div>
              <button type="submit" class="btn btn-primary px-4" id="btnSaveProfil" style="background:var(--clr-orange);border-color:var(--clr-orange);font-weight:600;">
                Simpan Perubahan
              </button>
            </form>
          </div>

          <!-- Ganti Password -->
          <div class="col-lg-6">
            <div class="section-title"><i class="bi bi-shield-lock"></i>Ganti Password</div>
            <div class="alert-inline" id="passAlert" style="background:rgba(220,53,69,.1);border:1px solid rgba(220,53,69,.25);color:#8b1a24;">
              <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span id="passAlertMsg"></span>
            </div>
            <div class="alert-inline" id="passSuccess" style="background:rgba(40,167,69,.1);border:1px solid rgba(40,167,69,.25);color:#1a6e30;">
              <i class="bi bi-check-circle-fill flex-shrink-0"></i><span>Password berhasil diperbarui.</span>
            </div>
            <form id="formPassword">
              <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:.85rem;color:var(--clr-blue);">Password Lama *</label>
                <input type="password" class="form-control" id="passLama" maxlength="100">
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:.85rem;color:var(--clr-blue);">Password Baru * <span class="text-muted fw-normal">(min. 8 karakter)</span></label>
                <input type="password" class="form-control" id="passBaru" maxlength="100">
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:.85rem;color:var(--clr-blue);">Konfirmasi Password Baru *</label>
                <input type="password" class="form-control" id="passBaruConfirm" maxlength="100">
              </div>
              <button type="submit" class="btn btn-outline-secondary px-4" id="btnSavePass" style="font-weight:600;">
                Perbarui Password
              </button>
            </form>
          </div>

        </div>
      </div><!-- /tab-profil -->

      <!-- TAB: POTENSI SAYA -->
      <div class="tab-pane fade" id="tab-potensi">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
          <div class="section-title mb-0"><i class="bi bi-shop"></i>Potensi Saya</div>
          <a href="/daftar-potensi" class="btn btn-sm px-3" style="background:var(--clr-orange);color:#fff;font-weight:600;border-radius:8px;">
            <i class="bi bi-plus-lg me-1"></i>Daftar Potensi Baru
          </a>
        </div>

        <div id="potensiLoadingState" class="text-center py-4">
          <div class="spinner-sm"></div> <span style="color:#9ca3af;font-size:.88rem;margin-left:.5rem;">Memuat data...</span>
        </div>
        <div id="potensiEmptyState" class="empty-state" style="display:none;">
          <i class="bi bi-shop"></i>
          <div>Anda belum memiliki potensi yang terdaftar.</div>
          <a href="/daftar-potensi" class="btn btn-sm mt-3" style="background:var(--clr-orange);color:#fff;font-weight:600;border-radius:8px;">
            Daftarkan Sekarang
          </a>
        </div>
        <div id="potensiList"></div>
      </div><!-- /tab-potensi -->

    </div>
  </div>
</div>

<!-- MODAL EDIT POTENSI -->
<div class="modal fade" id="modalEditPotensi" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" style="color:var(--clr-blue);">
          <i class="bi bi-pencil-square me-2"></i>Edit Potensi
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="alert-inline" id="editPotensiAlert" style="background:rgba(220,53,69,.1);border:1px solid rgba(220,53,69,.25);color:#8b1a24;margin-bottom:1rem;">
          <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span id="editPotensiAlertMsg"></span>
        </div>
        <input type="hidden" id="editPotensiId">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold" style="font-size:.84rem;">Nama Potensi *</label>
            <input type="text" class="form-control" id="epNama" maxlength="100">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" style="font-size:.84rem;">Kategori</label>
            <select class="form-select" id="epKategori"><option value="">-- Pilih --</option></select>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold" style="font-size:.84rem;">Deskripsi *</label>
            <textarea class="form-control" id="epDeskripsi" rows="4" maxlength="1000"></textarea>
            <div class="form-text">Minimal 20 karakter.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" style="font-size:.84rem;">Nomor Telepon / WA *</label>
            <input type="tel" class="form-control" id="epWa" maxlength="20" inputmode="tel">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" style="font-size:.84rem;">Jenis / Subkategori</label>
            <input type="text" class="form-control" id="epJenis" maxlength="100">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold" style="font-size:.84rem;">Alamat / Lokasi Usaha</label>
            <input type="text" class="form-control" id="epLokasi" maxlength="255">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" style="font-size:.84rem;">Jam Operasional</label>
            <input type="text" class="form-control" id="epJam" maxlength="100">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold" style="font-size:.84rem;">Foto Baru <span class="text-muted fw-normal">(opsional, ganti semua foto lama)</span></label>
            <input type="file" class="form-control" id="epFoto" accept="image/jpeg,image/jpg,image/png,image/webp" multiple>
            <div class="form-text">Maks. 3 foto, 2 MB per foto. JPG/PNG/WebP.</div>
          </div>
          <div class="col-12" id="currentPhotoWrap" style="display:none;">
            <label class="form-label fw-semibold" style="font-size:.84rem;">Foto Saat Ini</label>
            <div id="currentPhotos" class="d-flex gap-2 flex-wrap"></div>
            <div class="form-text">Klik × untuk hapus foto individual.</div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn px-4 fw-600" id="btnSavePotensi" style="background:var(--clr-orange);color:#fff;font-weight:600;">
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

/* ── Render header profil ── */
function renderProfileHeader() {
  document.getElementById('profileName').textContent = currentUser.name || 'Warga';
  document.getElementById('profilePhone').textContent = currentUser.phone || '';
  document.getElementById('editNama').value  = currentUser.name  || '';
  document.getElementById('editPhone').value = currentUser.phone || '';
  document.getElementById('editEmail').value = currentUser.email || '';
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
    document.getElementById('profileName').textContent  = nama;
    document.getElementById('profilePhone').textContent = phone;
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
  document.getElementById('potensiCount').textContent = res.data.meta ? res.data.meta.total : items.length;

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
            (p.category ? p.category.name + ' · ' : '') +
            new Date(p.created_at).toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}) +
          '</div>' +
          statusHtml +
          rejHtml +
          delHtml +
          photosHtml +
        '</div>' +
        '<div class="d-flex gap-1 flex-shrink-0 align-items-start">' +
          (canEdit ? '<button class="btn-ikon-edit" title="Edit" data-id="' + p.id + '"><i class="bi bi-pencil-fill"></i></button>' : '') +
          (!isDeletionPending
            ? '<button class="btn-ikon-hapus" title="' + (p.status === 'approved' ? 'Ajukan Hapus ke Admin' : 'Hapus') + '" data-id="' + p.id + '" data-status="' + p.status + '"><i class="bi bi-trash-fill"></i></button>'
            : '<button class="btn-ikon-hapus btn-cancel-del" title="Batalkan Permintaan Hapus" data-id="' + p.id + '" style="color:#f59e0b;border-color:#f59e0b;"><i class="bi bi-x-circle-fill"></i></button>'
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
</body>
</html>
