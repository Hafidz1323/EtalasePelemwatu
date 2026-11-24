<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk & Daftar — EtalasePelemwatu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body.login-page {
      background: linear-gradient(135deg, #094A74 0%, #063553 50%, #041f33 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 1.5rem;
    }
    .login-container { width: 100%; max-width: 480px; }
    .login-card {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 20px 45px rgba(0, 0, 0, 0.28);
      padding: 2.25rem 2rem;
    }
    .auth-nav-tabs {
      display: flex;
      gap: 0.5rem;
      background: #f1f5f9;
      padding: 0.35rem;
      border-radius: 12px;
      margin-bottom: 1.5rem;
    }
    .auth-tab-btn {
      flex: 1;
      border: none;
      background: transparent;
      padding: 0.6rem 0.5rem;
      font-size: 0.9rem;
      font-weight: 600;
      color: #64748b;
      border-radius: 8px;
    }
    .auth-tab-btn.active {
      background: #ffffff;
      color: #094A74;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .input-group-custom { margin-bottom: 1rem; }
    .input-group-custom .form-label {
      font-size: 0.82rem;
      font-weight: 600;
      color: #094A74;
      margin-bottom: 0.35rem;
      display: block;
    }
    .input-icon-wrap { position: relative; }
    .input-icon-wrap i.prefix-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
    }
    .input-icon-wrap input {
      padding-left: 2.75rem;
      padding-right: 2.75rem;
      height: 46px;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
    }
    .btn-toggle-password {
      position: absolute;
      right: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #94a3b8;
      cursor: pointer;
    }
    .btn-submit-auth {
      background: #f2630f;
      color: #fff;
      border: none;
      border-radius: 10px;
      height: 48px;
      font-weight: 600;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      margin-top: 1rem;
    }
    .btn-submit-auth:hover { background: #d95305; color: #fff; }
    .auth-alert { display: none; border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.88rem; margin-bottom: 1.25rem; }
  </style>
</head>
<body class="login-page">

  <div class="login-container">
    <div class="text-center mb-4">
      <h3 class="fw-bold text-white mb-1">EtalasePelemwatu</h3>
      <p class="text-white-50" style="font-size:0.9rem;">Portal Layanan & Promosi Potensi Desa Pelemwatu</p>
    </div>

    <div class="login-card">
      <div class="auth-nav-tabs">
        <button type="button" class="auth-tab-btn active" id="tabBtnLogin" onclick="switchTab('login')">
          <i class="bi bi-box-arrow-in-right"></i> Masuk (Login)
        </button>
        <button type="button" class="auth-tab-btn" id="tabBtnRegister" onclick="switchTab('register')">
          <i class="bi bi-person-plus"></i> Daftar Warga
        </button>
      </div>

      <div id="authAlert" class="auth-alert alert">
        <span id="authAlertText"></span>
      </div>

      <!-- FORM LOGIN -->
      <form id="loginForm">
        <div class="input-group-custom">
          <label class="form-label" for="loginEmail">Email / Username</label>
          <div class="input-icon-wrap">
            <input type="text" id="loginEmail" class="form-control" placeholder="admin@pelemwatu.desa.id atau email Anda" required>
            <i class="bi bi-envelope prefix-icon"></i>
          </div>
        </div>

        <div class="input-group-custom">
          <label class="form-label" for="loginPassword">Kata Sandi</label>
          <div class="input-icon-wrap">
            <input type="password" id="loginPassword" class="form-control" placeholder="Masukkan kata sandi" required>
            <i class="bi bi-lock prefix-icon"></i>
            <button type="button" class="btn-toggle-password" onclick="togglePass('loginPassword', 'eyeIconLogin')">
              <i class="bi bi-eye" id="eyeIconLogin"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-submit-auth" id="btnLoginSubmit">
          <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
        </button>
      </form>

      <!-- FORM REGISTER WARGA -->
      <form id="registerForm" style="display:none;">
        <div class="input-group-custom">
          <label class="form-label">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
          <div class="input-icon-wrap">
            <input type="text" id="regName" class="form-control" placeholder="Contoh: Ahmad Fauzi" required>
            <i class="bi bi-person prefix-icon"></i>
          </div>
        </div>

        <div class="input-group-custom">
          <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
          <div class="input-icon-wrap">
            <input type="email" id="regEmail" class="form-control" placeholder="contoh@gmail.com" required>
            <i class="bi bi-envelope prefix-icon"></i>
          </div>
        </div>

        <div class="input-group-custom">
          <label class="form-label">Nomor WhatsApp Aktif</label>
          <div class="input-icon-wrap">
            <input type="text" id="regPhone" class="form-control" placeholder="081234567890">
            <i class="bi bi-whatsapp prefix-icon"></i>
          </div>
        </div>

        <div class="input-group-custom">
          <label class="form-label">Alamat / RT RW Dusun</label>
          <div class="input-icon-wrap">
            <input type="text" id="regAddress" class="form-control" placeholder="Dusun Pelemwatu RT 02 RW 01">
            <i class="bi bi-geo-alt prefix-icon"></i>
          </div>
        </div>

        <div class="row g-2">
          <div class="col-sm-6">
            <div class="input-group-custom">
              <label class="form-label">Kata Sandi (Min. 6) <span class="text-danger">*</span></label>
              <div class="input-icon-wrap">
                <input type="password" id="regPassword" class="form-control" placeholder="******" required>
                <i class="bi bi-lock prefix-icon"></i>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="input-group-custom">
              <label class="form-label">Konfirmasi Sandi <span class="text-danger">*</span></label>
              <div class="input-icon-wrap">
                <input type="password" id="regPasswordConfirmation" class="form-control" placeholder="******" required>
                <i class="bi bi-shield-check prefix-icon"></i>
              </div>
            </div>
          </div>
        </div>

        <button type="submit" class="btn-submit-auth" id="btnRegisterSubmit">
          <i class="bi bi-person-plus"></i> Daftarkan Akun Warga
        </button>
      </form>
    </div>

    <div class="text-center mt-3">
      <a href="/" class="text-white-50 text-decoration-none" style="font-size:0.9rem;">
        <i class="bi bi-arrow-left"></i> Kembali ke Beranda
      </a>
    </div>
  </div>

  <script>
    function switchTab(tab) {
      const isLogin = tab === 'login';
      document.getElementById('tabBtnLogin').classList.toggle('active', isLogin);
      document.getElementById('tabBtnRegister').classList.toggle('active', !isLogin);
      document.getElementById('loginForm').style.display = isLogin ? 'block' : 'none';
      document.getElementById('registerForm').style.display = isLogin ? 'none' : 'block';
      hideAlert();
    }

    function togglePass(inputId, iconId) {
      const inp = document.getElementById(inputId);
      const ico = document.getElementById(iconId);
      if (inp.type === 'password') {
        inp.type = 'text';
        ico.classList.replace('bi-eye', 'bi-eye-slash');
      } else {
        inp.type = 'password';
        ico.classList.replace('bi-eye-slash', 'bi-eye');
      }
    }

    function showAlert(msg, type = 'danger') {
      const box = document.getElementById('authAlert');
      box.className = 'auth-alert alert alert-' + type;
      document.getElementById('authAlertText').textContent = msg;
      box.style.display = 'block';
    }

    function hideAlert() {
      document.getElementById('authAlert').style.display = 'none';
    }

    if (window.location.hash === '#register' || window.location.pathname.includes('/register')) {
      switchTab('register');
    }

    document.getElementById('loginForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const email = document.getElementById('loginEmail').value.trim();
      const password = document.getElementById('loginPassword').value;
      const btn = document.getElementById('btnLoginSubmit');

      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';
      hideAlert();

      try {
        const res = await fetch('/api/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password })
        });
        const data = await res.json();

        if (res.ok && data.status === 'success') {
          const u = data.data.user;
          localStorage.setItem('authToken', data.data.token);
          localStorage.setItem('userData', JSON.stringify(u));

          if (u.role === 'admin') {
            localStorage.setItem('adminLoggedIn', 'true');
            localStorage.setItem('adminUser', JSON.stringify(u));
            window.location.href = '/admin';
          } else {
            localStorage.setItem('wargaLoggedIn', 'true');
            showAlert('Login berhasil! Mengalihkan...', 'success');
            setTimeout(() => { window.location.href = '/daftar-potensi'; }, 800);
          }
        } else {
          showAlert(data.message || 'Login gagal, periksa email dan kata sandi Anda.', 'danger');
        }
      } catch (err) {
        showAlert('Terjadi kesalahan koneksi server.', 'danger');
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang';
      }
    });

    document.getElementById('registerForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const name = document.getElementById('regName').value.trim();
      const email = document.getElementById('regEmail').value.trim();
      const phone = document.getElementById('regPhone').value.trim();
      const address = document.getElementById('regAddress').value.trim();
      const password = document.getElementById('regPassword').value;
      const password_confirmation = document.getElementById('regPasswordConfirmation').value;
      const btn = document.getElementById('btnRegisterSubmit');

      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mendaftarkan...';
      hideAlert();

      try {
        const res = await fetch('/api/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ name, email, phone, address, password, password_confirmation })
        });
        const data = await res.json();

        if (res.ok && data.status === 'success') {
          localStorage.setItem('authToken', data.data.token);
          localStorage.setItem('userData', JSON.stringify(data.data.user));
          localStorage.setItem('wargaLoggedIn', 'true');

          showAlert('Pendaftaran warga berhasil! Mengalihkan...', 'success');
          setTimeout(() => { window.location.href = '/daftar-potensi'; }, 1000);
        } else {
          showAlert(data.message || 'Pendaftaran gagal, periksa isian data Anda.', 'danger');
        }
      } catch (err) {
        showAlert('Gagal menghubungi server.', 'danger');
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-person-plus"></i> Daftarkan Akun Warga';
      }
    });
  </script>
</body>
</html>
