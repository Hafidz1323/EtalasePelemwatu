<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar / Masuk Warga — EtalasePelemwatu</title>
  <meta name="description" content="Daftar akun atau masuk sebagai warga Desa Pelemwatu untuk mengelola potensi lokal Anda.">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* ── Halaman: putih ── */
    body.auth-page {
      background: #fff;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* ── Navbar ── */
    #siteHeader { position: relative; z-index: 100; }
    #mainNavbar { background: #fff; box-shadow: 0 2px 12px rgba(0,0,0,.07); }

    /* ── Wrapper split ── */
    .auth-wrap {
      flex: 1;
      display: flex;
      align-items: stretch;
      position: relative;
      z-index: 2;
    }

    /* ── LEFT: teks di atas gradient ── */
    .auth-left {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 3rem 3rem 3rem 4rem;
    }
    .auth-left-heading {
      font-size: clamp(1.9rem, 2.8vw, 2.8rem);
      font-weight: 800;
      color: var(--clr-blue, #094A74);
      line-height: 1.2;
      margin-bottom: 1rem;
    }
    .auth-left-heading span { color: var(--clr-orange,#F2630F); }
    .auth-left-desc {
      font-size: 1rem;
      color: #4b5563;
      line-height: 1.65;
      max-width: 400px;
      margin-bottom: 2rem;
    }
    .auth-features { display: flex; flex-direction: column; gap: .7rem; }
    .auth-feature {
      display: flex;
      align-items: center;
      gap: .75rem;
      color: #374151;
      font-size: .9rem;
    }
    .auth-feature i {
      font-size: 1rem;
      color: var(--clr-orange,#F2630F);
      flex-shrink: 0;
      width: 20px;
      text-align: center;
    }

    /* ── RIGHT: card putih + border kiri ── */
    .auth-right {
      width: 100%;
      max-width: 480px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 2.5rem;
      border-left: 1px solid #e5e7eb;
    }
    .auth-card {
      background: #fff;
      border-radius: 20px;
      padding: 2.25rem 2rem;
      box-shadow: 0 4px 24px rgba(0,0,0,.08);
      border: 1px solid #e5e7eb;
    }

    /* ── Tab Nav ── */
    .auth-tabs {
      display: flex;
      background: #f3f4f6;
      border-radius: 999px;
      padding: 4px;
      margin-bottom: 1.75rem;
      gap: 2px;
    }
    .auth-tab-btn {
      flex: 1;
      padding: .6rem 1rem;
      background: none;
      border: none;
      font-size: .88rem;
      font-weight: 600;
      color: #6b7280;
      cursor: pointer;
      border-radius: 999px;
      transition: all .2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: .4rem;
    }
    .auth-tab-btn.active {
      background: var(--clr-orange,#F2630F);
      color: #fff;
      box-shadow: 0 4px 12px rgba(242,99,15,.3);
    }

    /* ── Form heading ── */
    .auth-card-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--clr-blue,#094A74);
      margin-bottom: .25rem;
    }
    .auth-card-sub {
      font-size: .85rem;
      color: #6b7280;
      margin-bottom: 1.25rem;
    }
    .auth-card-sub button {
      background: none;
      border: none;
      color: var(--clr-orange,#F2630F);
      font-weight: 600;
      cursor: pointer;
      padding: 0;
    }
    .auth-card-sub button:hover { text-decoration: underline; }

    /* ── Fields ── */
    .auth-field { margin-bottom: 1rem; }
    .auth-field label {
      font-size: .8rem;
      font-weight: 600;
      color: var(--clr-blue,#094A74);
      margin-bottom: .3rem;
      display: block;
    }
    .auth-input {
      position: relative;
    }
    .auth-input i.fi {
      position: absolute;
      left: .95rem;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      font-size: .9rem;
      pointer-events: none;
    }
    .auth-input input {
      width: 100%;
      height: 46px;
      padding: 0 2.5rem 0 2.65rem;
      font-size: .9rem;
      background: #f9fafb;
      border: 1.5px solid #e5e7eb;
      border-radius: 999px;
      color: #111827;
      outline: none;
      transition: border-color .2s, box-shadow .2s;
    }
    .auth-input input:focus {
      border-color: var(--clr-orange,#F2630F);
      background: #fff;
      box-shadow: 0 0 0 3px rgba(242,99,15,.12);
    }
    .auth-input input::placeholder { color: #c4c9d4; }
    .btn-eye {
      position: absolute;
      right: .8rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #9ca3af;
      padding: .25rem;
      cursor: pointer;
      font-size: .9rem;
    }
    .btn-eye:hover { color: #374151; }
    .auth-hint {
      font-size: .74rem;
      color: #9ca3af;
      margin-top: .25rem;
      padding-left: .5rem;
    }

    /* ── Submit ── */
    .btn-auth {
      width: 100%;
      height: 48px;
      background: var(--clr-orange,#F2630F);
      color: #fff;
      border: none;
      border-radius: 999px;
      font-weight: 700;
      font-size: .93rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: .5rem;
      cursor: pointer;
      transition: all .2s;
      box-shadow: 0 5px 15px rgba(242,99,15,.35);
      margin-top: 1.25rem;
    }
    .btn-auth:hover { background: #d9560c; transform: translateY(-1px); }
    .btn-auth:disabled { opacity: .7; cursor: not-allowed; transform: none; }

    /* ── Alert ── */
    .auth-alert {
      display: none;
      padding: .65rem .9rem;
      border-radius: 10px;
      font-size: .83rem;
      margin-bottom: .9rem;
      align-items: center;
      gap: .45rem;
    }
    .auth-alert.show { display: flex; }
    .auth-alert.error   { background: rgba(220,53,69,.08);  border: 1px solid rgba(220,53,69,.25);  color: #b02a37; }
    .auth-alert.success { background: rgba(40,167,69,.08);  border: 1px solid rgba(40,167,69,.25);  color: #1a6e30; }

    /* ── Pane ── */
    .auth-pane { display: none; }
    .auth-pane.active { display: block; }

    /* ── Back link ── */
    .auth-back {
      text-align: center;
      margin-top: 1.1rem;
    }
    .auth-back a {
      font-size: .84rem;
      color: #6b7280;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: .35rem;
      transition: color .2s;
    }
    .auth-back a:hover { color: var(--clr-orange,#F2630F); }

    /* ── LEFT: beri border pemisah tipis ── */
    .auth-left {
      border-right: 1px solid #f0f0f0;
    }

    /* ── Responsive ── */
    @media (max-width: 991px) {
      .auth-left { display: none; }
      .auth-right { max-width: 100%; padding: 2rem 1.25rem; }
      .auth-wrap { justify-content: center; }
    }
  </style>
</head>
<body class="auth-page">

  <!-- NAVBAR -->
  <header id="siteHeader">
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
            <li class="nav-item"><a class="nav-link" href="/">Beranda</a></li>
            <li class="nav-item"><a class="nav-link" href="/#profil-desa">Profil Desa</a></li>
            <li class="nav-item"><a class="nav-link" href="/#pengenalan-etalase">Tentang</a></li>
            <li class="nav-item"><a class="nav-link" href="/#katalog">Katalog</a></li>
            <li class="nav-item"><a class="nav-link" href="/#layanan">Layanan</a></li>
            <li class="nav-item"><a class="nav-link" href="/#kontak-section">Kontak</a></li>
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

  <!-- SPLIT WRAP -->
  <div class="auth-wrap">

    <!-- LEFT: Teks di atas background gradient -->
    <div class="auth-left">
      <h1 class="auth-left-heading">
        Usaha warga Pelemwatu<br>
        dalam satu <span>katalog digital</span>.
      </h1>
      <p class="auth-left-desc">
        Daftarkan usaha, jasa, atau produk kamu supaya mudah ditemukan
        oleh warga dan tamu yang berkunjung ke desa.
      </p>
      <div class="auth-features">
        <div class="auth-feature">
          <i class="bi bi-person-check"></i>
          Gratis, tanpa biaya pendaftaran
        </div>
        <div class="auth-feature">
          <i class="bi bi-camera"></i>
          Upload foto dan info kontak WhatsApp
        </div>
        <div class="auth-feature">
          <i class="bi bi-shield-check"></i>
          Potensi diverifikasi admin desa sebelum tampil
        </div>
        <div class="auth-feature">
          <i class="bi bi-pencil-square"></i>
          Kelola dan edit data kapan saja lewat profil
        </div>
      </div>
    </div>

    <!-- RIGHT: Card form putih -->
    <div class="auth-right">
      <div class="auth-card">

        <!-- Tab pill -->
        <div class="auth-tabs">
          <button class="auth-tab-btn active" id="tabBtnDaftar" onclick="switchTab('daftar')">
            <i class="bi bi-person-plus"></i> Daftar Akun
          </button>
          <button class="auth-tab-btn" id="tabBtnMasuk" onclick="switchTab('masuk')">
            <i class="bi bi-box-arrow-in-right"></i> Masuk
          </button>
        </div>

        <!-- PANE: DAFTAR -->
        <div class="auth-pane active" id="pane-daftar">
          <div class="auth-card-title">Buat akun warga</div>
          <p class="auth-card-sub">
            Sudah punya akun? <button onclick="switchTab('masuk')">Masuk di sini</button>
          </p>

          <div class="auth-alert error" id="regAlert">
            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
            <span id="regAlertMsg"></span>
          </div>
          <div class="auth-alert success" id="regAlertOk">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <span id="regAlertOkMsg"></span>
          </div>

          <form id="formRegister" novalidate>
            <div class="auth-field">
              <label>Nama Lengkap <span class="text-danger">*</span></label>
              <div class="auth-input">
                <input type="text" id="regNama" placeholder="Nama lengkap" maxlength="100" autocomplete="name">
                <i class="bi bi-person fi"></i>
              </div>
            </div>
            <div class="auth-field">
              <label>Nomor HP / WhatsApp <span class="text-danger">*</span></label>
              <div class="auth-input">
                <input type="tel" id="regPhone" placeholder="08123456789" maxlength="20" inputmode="tel" autocomplete="tel">
                <i class="bi bi-phone fi"></i>
              </div>
              <div class="auth-hint">Dipakai untuk login. Harus nomor aktif.</div>
            </div>
            <div class="auth-field">
              <label>Email <span class="text-muted fw-normal">(opsional)</span></label>
              <div class="auth-input">
                <input type="email" id="regEmail" placeholder="email@contoh.com" maxlength="100" autocomplete="email">
                <i class="bi bi-envelope fi"></i>
              </div>
            </div>
            <div class="auth-field">
              <label>Password <span class="text-danger">*</span></label>
              <div class="auth-input">
                <input type="password" id="regPassword" placeholder="Minimal 8 karakter" maxlength="100" autocomplete="new-password">
                <i class="bi bi-lock fi"></i>
                <button type="button" class="btn-eye" data-target="regPassword"><i class="bi bi-eye"></i></button>
              </div>
            </div>
            <div class="auth-field">
              <label>Konfirmasi Password <span class="text-danger">*</span></label>
              <div class="auth-input">
                <input type="password" id="regPasswordConfirm" placeholder="Ulangi password" maxlength="100" autocomplete="new-password">
                <i class="bi bi-lock fi"></i>
                <button type="button" class="btn-eye" data-target="regPasswordConfirm"><i class="bi bi-eye"></i></button>
              </div>
            </div>
            <button type="submit" class="btn-auth" id="btnRegister">
              <i class="bi bi-person-check"></i>
              <span id="btnRegText">Buat Akun</span>
            </button>
          </form>
        </div>

        <!-- PANE: MASUK -->
        <div class="auth-pane" id="pane-masuk">
          <div class="auth-card-title">Masuk ke akun</div>
          <p class="auth-card-sub">
            Belum punya akun? <button onclick="switchTab('daftar')">Daftar sekarang</button>
          </p>

          <div class="auth-alert error" id="loginAlert">
            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
            <span id="loginAlertMsg"></span>
          </div>
          <div class="auth-alert success" id="loginAlertOk">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <span id="loginAlertOkMsg"></span>
          </div>

          <form id="formLogin" novalidate>
            <div class="auth-field">
              <label>Nomor HP atau Email <span class="text-danger">*</span></label>
              <div class="auth-input">
                <input type="text" id="loginId" placeholder="08123456789 atau email@contoh.com" maxlength="100" autocomplete="username">
                <i class="bi bi-person fi"></i>
              </div>
            </div>
            <div class="auth-field">
              <label>Password <span class="text-danger">*</span></label>
              <div class="auth-input">
                <input type="password" id="loginPassword" placeholder="Password" maxlength="100" autocomplete="current-password">
                <i class="bi bi-lock fi"></i>
                <button type="button" class="btn-eye" data-target="loginPassword"><i class="bi bi-eye"></i></button>
              </div>
            </div>
            <button type="submit" class="btn-auth" id="btnLogin">
              <i class="bi bi-box-arrow-in-right"></i>
              <span id="btnLoginText">Masuk</span>
            </button>
          </form>
        </div>

      </div><!-- /auth-card -->

      <div class="auth-back">
        <a href="/"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
      </div>
    </div><!-- /auth-right -->

  </div><!-- /auth-wrap -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  'use strict';
  var API_BASE = (window.location.protocol === 'file:' || window.location.port === '5500')
    ? 'http://localhost:8000/api'
    : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');

  /* Sudah login → redirect */
  (function() {
    try {
      var s = JSON.parse(localStorage.getItem('ep_warga_session') || 'null');
      if (s && s.token && s.user && s.user.role === 'warga') {
        var next = new URLSearchParams(window.location.search).get('next') || 'profil-warga';
        window.location.replace('/' + next);
      }
    } catch(e) {}
  })();

  /* Tab */
  function switchTab(tab) {
    document.getElementById('pane-daftar').classList.toggle('active', tab === 'daftar');
    document.getElementById('pane-masuk').classList.toggle('active',  tab === 'masuk');
    document.getElementById('tabBtnDaftar').classList.toggle('active', tab === 'daftar');
    document.getElementById('tabBtnMasuk').classList.toggle('active',  tab === 'masuk');
  }

  /* Toggle password */
  document.querySelectorAll('.btn-eye').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var input = document.getElementById(this.dataset.target);
      if (!input) return;
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      this.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
  });

  /* Alert helpers */
  function showAlert(alertId, msg) {
    var el = document.getElementById(alertId);
    var sp = el ? el.querySelector('span') : null;
    if (sp) sp.textContent = msg;
    if (el) el.classList.add('show');
  }
  function clearAlerts() {
    ['regAlert','regAlertOk','loginAlert','loginAlertOk'].forEach(function(id) {
      var el = document.getElementById(id);
      if (el) el.classList.remove('show');
    });
  }

  /* Normalisasi HP */
  function normalizePhone(s) {
    s = s.replace(/[\s\-()+]/g, '');
    s = s.replace(/^\+?62(?=8)/, '0');
    return s;
  }

  /* DAFTAR */
  document.getElementById('formRegister').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearAlerts();

    var nama    = document.getElementById('regNama').value.trim();
    var phone   = normalizePhone(document.getElementById('regPhone').value.trim());
    var email   = document.getElementById('regEmail').value.trim().toLowerCase() || null;
    var pass    = document.getElementById('regPassword').value;
    var confirm = document.getElementById('regPasswordConfirm').value;

    if (!nama)                                return showAlert('regAlert', 'Nama lengkap wajib diisi.');
    if (!phone)                               return showAlert('regAlert', 'Nomor HP wajib diisi.');
    if (!/^08[1-9][0-9]{7,10}$/.test(phone)) return showAlert('regAlert', 'Nomor HP tidak valid. Contoh: 08123456789.');
    if (!pass)                                return showAlert('regAlert', 'Password wajib diisi.');
    if (pass.length < 8)                      return showAlert('regAlert', 'Password minimal 8 karakter.');
    if (pass !== confirm)                     return showAlert('regAlert', 'Konfirmasi password tidak cocok.');

    var btn = document.getElementById('btnRegister');
    var txt = document.getElementById('btnRegText');
    btn.disabled = true; txt.textContent = 'Memproses...';

    try {
      var res  = await fetch(API_BASE + '/warga/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ name: nama, phone: phone, email: email, password: pass, password_confirmation: confirm })
      });
      var data = await res.json();

      if (res.ok && data.status === 'success') {
        localStorage.setItem('ep_warga_session', JSON.stringify({ token: data.data.token, user: data.data.user }));
        showAlert('regAlertOk', 'Akun berhasil dibuat! Mengalihkan...');
        setTimeout(function() { window.location.replace('/profil-warga'); }, 800);
        return;
      }

      var msg = 'Pendaftaran gagal.';
      if (data.errors) {
        var k = Object.keys(data.errors)[0];
        if (k && data.errors[k][0]) msg = data.errors[k][0];
      } else if (data.message) msg = data.message;
      showAlert('regAlert', msg);
    } catch(err) {
      showAlert('regAlert', 'Server tidak dapat dihubungi. Coba lagi.');
    }
    btn.disabled = false; txt.textContent = 'Buat Akun';
  });

  /* MASUK */
  document.getElementById('formLogin').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearAlerts();

    var loginId = document.getElementById('loginId').value.trim();
    var pass    = document.getElementById('loginPassword').value;

    if (!loginId) return showAlert('loginAlert', 'Nomor HP atau email wajib diisi.');
    if (!pass)    return showAlert('loginAlert', 'Password wajib diisi.');

    var btn = document.getElementById('btnLogin');
    var txt = document.getElementById('btnLoginText');
    btn.disabled = true; txt.textContent = 'Memverifikasi...';

    try {
      var res  = await fetch(API_BASE + '/warga/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ login: loginId, password: pass })
      });
      var data = await res.json();

      if (res.ok && data.status === 'success') {
        localStorage.setItem('ep_warga_session', JSON.stringify({ token: data.data.token, user: data.data.user }));
        showAlert('loginAlertOk', 'Login berhasil! Mengalihkan...');
        var next = new URLSearchParams(window.location.search).get('next') || 'profil-warga';
        setTimeout(function() { window.location.replace('/' + next); }, 700);
        return;
      }

      var msg = data.message || 'Nomor HP/email atau password salah.';
      if (res.status === 429) msg = 'Terlalu banyak percobaan. Coba lagi nanti.';
      showAlert('loginAlert', msg);
    } catch(err) {
      showAlert('loginAlert', 'Server tidak dapat dihubungi. Coba lagi.');
    }
    btn.disabled = false; txt.textContent = 'Masuk';
  });

  /* Auto open tab dari URL */
  (function() {
    var tab = new URLSearchParams(window.location.search).get('tab');
    if (tab === 'masuk') switchTab('masuk');
  })();

  /* Navbar state */
  (function() {
    try {
      var s = JSON.parse(localStorage.getItem('ep_warga_session') || 'null');
      if (s && s.token && s.user) {
        var wrap = document.getElementById('navWargaWrap');
        var adminWrap = document.getElementById('navAdminWrap');
        if (wrap) wrap.innerHTML = '<a href="/profil-warga" style="display:inline-flex;align-items:center;gap:.35rem;color:#fff;text-decoration:none;font-size:.88rem;"><i class="bi bi-person-circle"></i>' + (s.user.name || 'Profil') + '</a>';
        if (adminWrap) adminWrap.style.display = 'none';
      }
    } catch(e) {}
  })();
  </script>
</body>
</html>
