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
    body.login-page { background:linear-gradient(135deg,#094A74 0%,#063553 50%,#041f33 100%);min-height:100vh;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:1.5rem;position:relative;overflow-x:hidden; }
    .login-decor-circle { position:absolute;border-radius:50%;pointer-events:none;filter:blur(80px);opacity:.25; }
    .decor-1 { width:380px;height:380px;background:var(--clr-orange);top:-100px;right:-80px; }
    .decor-2 { width:320px;height:320px;background:var(--clr-yellow);bottom:-60px;left:-80px; }
    .login-card-container { width:100%;max-width:480px;position:relative;z-index:2; }
    .login-brand-header { text-align:center;margin-bottom:1.5rem; }
    .login-brand-header .logo-badge { width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,var(--clr-orange),#ff7b2b);color:#fff;font-weight:800;font-size:1.3rem;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 10px 25px rgba(242,99,15,.4);margin-bottom:.65rem; }
    .login-brand-header h1 { font-size:1.4rem;color:#fff;font-weight:700;margin-bottom:.2rem; }
    .login-brand-header p { color:rgba(255,255,255,.7);font-size:.88rem;margin-bottom:0; }
    .login-box { background:#fff;border-radius:var(--radius-lg);box-shadow:0 20px 45px rgba(0,0,0,.28); overflow:hidden; }
    .tab-nav { display:flex;border-bottom:2px solid #f0f2f5; }
    .tab-btn { flex:1;padding:.85rem 1rem;background:none;border:none;font-size:.9rem;font-weight:600;color:#6b7280;cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px;transition:color .2s,border-color .2s; }
    .tab-btn.active { color:var(--clr-orange);border-bottom-color:var(--clr-orange); }
    .tab-pane { display:none;padding:1.75rem 2rem; }
    .tab-pane.active { display:block; }
    @media(max-width:480px) { .tab-pane { padding:1.5rem 1.25rem; } }
    .input-group-custom { margin-bottom:1.1rem; }
    .input-group-custom .form-label { font-size:.82rem;font-weight:600;color:var(--clr-blue);margin-bottom:.3rem;display:block; }
    .input-icon-wrap { position:relative; }
    .input-icon-wrap i.prefix-icon { position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:var(--clr-text-light);font-size:.95rem;pointer-events:none; }
    .input-icon-wrap input { padding-left:2.6rem;padding-right:2.6rem;height:46px;font-size:.9rem;border:1.5px solid var(--clr-border);border-radius:var(--radius);transition:border-color .2s,box-shadow .2s; }
    .input-icon-wrap input:focus { border-color:var(--clr-orange);box-shadow:0 0 0 3px rgba(242,99,15,.12); }
    .btn-toggle-pass { position:absolute;right:.7rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--clr-text-light);padding:.3rem .4rem;cursor:pointer; }
    .btn-submit { background:var(--clr-orange);color:#fff;border:none;border-radius:var(--radius);height:46px;font-weight:700;font-size:.92rem;width:100%;transition:all .25s;box-shadow:0 4px 14px rgba(242,99,15,.3);cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.5rem; }
    .btn-submit:hover { background:#d9560c;transform:translateY(-1px); }
    .btn-submit:disabled { opacity:.7;cursor:not-allowed;transform:none; }
    .auth-alert { display:none;padding:.7rem 1rem;border-radius:var(--radius);font-size:.84rem;margin-bottom:1rem;align-items:center;gap:.5rem; }
    .auth-alert.show { display:flex; }
    .auth-alert.error { background:rgba(220,53,69,.1);border:1px solid rgba(220,53,69,.3);color:#b02a37; }
    .auth-alert.success { background:rgba(40,167,69,.1);border:1px solid rgba(40,167,69,.3);color:#1e7e34; }
    .switch-link { text-align:center;font-size:.85rem;color:#6b7280;margin-top:1.1rem; }
    .switch-link button { background:none;border:none;color:var(--clr-orange);font-weight:600;cursor:pointer;text-decoration:underline; }
    .login-footer-links { text-align:center;margin-top:1.25rem; }
    .login-footer-links a { color:rgba(255,255,255,.8);font-size:.85rem;text-decoration:none; }
    .login-footer-links a:hover { color:var(--clr-yellow); }
    .form-hint { font-size:.76rem;color:#9ca3af;margin-top:.25rem; }
  </style>
</head>
<body class="login-page">
  <div class="login-decor-circle decor-1"></div>
  <div class="login-decor-circle decor-2"></div>

  <div class="login-card-container">
    <div class="login-brand-header">
      <div class="logo-badge">EP</div>
      <h1>EtalasePelemwatu</h1>
      <p>Portal Warga Desa Pelemwatu</p>
    </div>

    <div class="login-box">
      <!-- Tab Nav -->
      <div class="tab-nav">
        <button class="tab-btn active" id="tabBtnDaftar" onclick="switchTab('daftar')">
          <i class="bi bi-person-plus me-1"></i> Daftar Akun
        </button>
        <button class="tab-btn" id="tabBtnMasuk" onclick="switchTab('masuk')">
          <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
        </button>
      </div>

      <!-- Tab: Daftar -->
      <div class="tab-pane active" id="pane-daftar">
        <div class="auth-alert error" id="regAlert"><i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span id="regAlertMsg"></span></div>

        <form id="formRegister" novalidate>
          <div class="input-group-custom">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <div class="input-icon-wrap">
              <input type="text" id="regNama" placeholder="Nama lengkap Anda" maxlength="100" autocomplete="name">
              <i class="bi bi-person prefix-icon"></i>
            </div>
          </div>
          <div class="input-group-custom">
            <label class="form-label">Nomor HP / WhatsApp <span class="text-danger">*</span></label>
            <div class="input-icon-wrap">
              <input type="tel" id="regPhone" placeholder="Contoh: 08123456789" maxlength="20" inputmode="tel" autocomplete="tel">
              <i class="bi bi-phone prefix-icon"></i>
            </div>
            <div class="form-hint">Dipakai untuk login. Harus nomor aktif.</div>
          </div>
          <div class="input-group-custom">
            <label class="form-label">Email <span class="text-muted" style="font-weight:400;">(opsional)</span></label>
            <div class="input-icon-wrap">
              <input type="email" id="regEmail" placeholder="email@contoh.com" maxlength="100" autocomplete="email">
              <i class="bi bi-envelope prefix-icon"></i>
            </div>
          </div>
          <div class="input-group-custom">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-icon-wrap">
              <input type="password" id="regPassword" placeholder="Minimal 8 karakter" maxlength="100" autocomplete="new-password">
              <i class="bi bi-lock prefix-icon"></i>
              <button type="button" class="btn-toggle-pass" data-target="regPassword"><i class="bi bi-eye"></i></button>
            </div>
          </div>
          <div class="input-group-custom">
            <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
            <div class="input-icon-wrap">
              <input type="password" id="regPasswordConfirm" placeholder="Ulangi password" maxlength="100" autocomplete="new-password">
              <i class="bi bi-lock prefix-icon"></i>
              <button type="button" class="btn-toggle-pass" data-target="regPasswordConfirm"><i class="bi bi-eye"></i></button>
            </div>
          </div>
          <button type="submit" class="btn-submit" id="btnRegister">
            <i class="bi bi-person-check"></i> <span id="btnRegText">Buat Akun</span>
          </button>
        </form>
        <div class="switch-link">Sudah punya akun? <button onclick="switchTab('masuk')">Masuk di sini</button></div>
      </div>

      <!-- Tab: Masuk -->
      <div class="tab-pane" id="pane-masuk">
        <div class="auth-alert error" id="loginAlert"><i class="bi bi-exclamation-circle-fill flex-shrink-0"></i><span id="loginAlertMsg"></span></div>

        <form id="formLogin" novalidate>
          <div class="input-group-custom">
            <label class="form-label">Nomor HP atau Email <span class="text-danger">*</span></label>
            <div class="input-icon-wrap">
              <input type="text" id="loginId" placeholder="08123456789 atau email@contoh.com" maxlength="100" autocomplete="username">
              <i class="bi bi-person prefix-icon"></i>
            </div>
          </div>
          <div class="input-group-custom">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-icon-wrap">
              <input type="password" id="loginPassword" placeholder="Password Anda" maxlength="100" autocomplete="current-password">
              <i class="bi bi-lock prefix-icon"></i>
              <button type="button" class="btn-toggle-pass" data-target="loginPassword"><i class="bi bi-eye"></i></button>
            </div>
          </div>
          <button type="submit" class="btn-submit" id="btnLogin">
            <i class="bi bi-box-arrow-in-right"></i> <span id="btnLoginText">Masuk</span>
          </button>
        </form>
        <div class="switch-link">Belum punya akun? <button onclick="switchTab('daftar')">Daftar sekarang</button></div>
      </div>
    </div><!-- /login-box -->

    <div class="login-footer-links">
      <a href="/"><i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  'use strict';
  var API_BASE = (window.location.protocol === 'file:' || window.location.port === '5500')
    ? 'http://localhost:8000/api'
    : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');

  /* ── Jika sudah login, langsung ke profil ── */
  (function() {
    try {
      var s = JSON.parse(localStorage.getItem('ep_warga_session') || 'null');
      if (s && s.token && s.user && s.user.role === 'warga') {
        var next = new URLSearchParams(window.location.search).get('next') || 'profil-warga';
        window.location.replace('/' + next);
      }
    } catch(e) {}
  })();

  /* ── Tab ── */
  function switchTab(tab) {
    document.getElementById('pane-daftar').classList.toggle('active', tab === 'daftar');
    document.getElementById('pane-masuk').classList.toggle('active',  tab === 'masuk');
    document.getElementById('tabBtnDaftar').classList.toggle('active', tab === 'daftar');
    document.getElementById('tabBtnMasuk').classList.toggle('active',  tab === 'masuk');
  }

  /* ── Toggle password visibility ── */
  document.querySelectorAll('.btn-toggle-pass').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var input = document.getElementById(this.dataset.target);
      if (!input) return;
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      this.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
  });

  /* ── Alert helper ── */
  function showAlert(id, msg) {
    var el = document.getElementById(id);
    var msgEl = document.getElementById(id + 'Msg');
    if (msgEl) msgEl.textContent = msg;
    if (el) el.classList.add('show');
  }
  function hideAlert(id) {
    var el = document.getElementById(id);
    if (el) el.classList.remove('show');
  }

  /* ── Normalisasi HP ── */
  function normalizePhone(s) {
    s = s.replace(/[\s\-()]/g, '');
    s = s.replace(/^\+?62(?=8)/, '0');
    return s;
  }

  /* ── Daftar ── */
  document.getElementById('formRegister').addEventListener('submit', async function(e) {
    e.preventDefault();
    hideAlert('regAlert');

    var nama     = document.getElementById('regNama').value.trim();
    var phone    = normalizePhone(document.getElementById('regPhone').value.trim());
    var email    = document.getElementById('regEmail').value.trim().toLowerCase() || null;
    var password = document.getElementById('regPassword').value;
    var confirm  = document.getElementById('regPasswordConfirm').value;

    if (!nama)             return showAlert('regAlert', 'Nama lengkap wajib diisi.');
    if (!phone)            return showAlert('regAlert', 'Nomor HP wajib diisi.');
    if (!/^08[1-9][0-9]{7,10}$/.test(phone)) return showAlert('regAlert', 'Nomor HP tidak valid. Contoh: 08123456789.');
    if (!password)         return showAlert('regAlert', 'Password wajib diisi.');
    if (password.length < 8) return showAlert('regAlert', 'Password minimal 8 karakter.');
    if (password !== confirm) return showAlert('regAlert', 'Konfirmasi password tidak cocok.');

    var btn = document.getElementById('btnRegister');
    var txt = document.getElementById('btnRegText');
    btn.disabled = true; txt.textContent = 'Memproses...';

    try {
      var res = await fetch(API_BASE + '/warga/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ name: nama, phone: phone, email: email, password: password, password_confirmation: confirm })
      });
      var data = await res.json();

      if (res.ok && data.status === 'success') {
        localStorage.setItem('ep_warga_session', JSON.stringify({ token: data.data.token, user: data.data.user }));
        txt.textContent = 'Berhasil!';
        setTimeout(function() { window.location.replace('/profil-warga'); }, 500);
        return;
      }

      var msg = 'Pendaftaran gagal.';
      if (data.errors) {
        var firstKey = Object.keys(data.errors)[0];
        if (firstKey && data.errors[firstKey][0]) msg = data.errors[firstKey][0];
      } else if (data.message) {
        msg = data.message;
      }
      showAlert('regAlert', msg);
    } catch(err) {
      showAlert('regAlert', 'Server tidak dapat dihubungi. Coba lagi.');
    }
    btn.disabled = false; txt.textContent = 'Buat Akun';
  });

  /* ── Masuk ── */
  document.getElementById('formLogin').addEventListener('submit', async function(e) {
    e.preventDefault();
    hideAlert('loginAlert');

    var loginId  = document.getElementById('loginId').value.trim();
    var password = document.getElementById('loginPassword').value;

    if (!loginId)  return showAlert('loginAlert', 'Nomor HP atau email wajib diisi.');
    if (!password) return showAlert('loginAlert', 'Password wajib diisi.');

    var btn = document.getElementById('btnLogin');
    var txt = document.getElementById('btnLoginText');
    btn.disabled = true; txt.textContent = 'Memverifikasi...';

    try {
      var res = await fetch(API_BASE + '/warga/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ login: loginId, password: password })
      });
      var data = await res.json();

      if (res.ok && data.status === 'success') {
        localStorage.setItem('ep_warga_session', JSON.stringify({ token: data.data.token, user: data.data.user }));
        txt.textContent = 'Berhasil!';
        var next = new URLSearchParams(window.location.search).get('next') || 'profil-warga';
        setTimeout(function() { window.location.replace('/' + next); }, 500);
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

  /* ── Auto open tab dari URL ── */
  (function() {
    var tab = new URLSearchParams(window.location.search).get('tab');
    if (tab === 'masuk') switchTab('masuk');
  })();
  </script>
</body>
</html>
