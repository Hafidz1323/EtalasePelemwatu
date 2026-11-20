/**
 * EtalasePelemwatu — Admin Authentication Module
 * Handles Admin Login, Logout, Session Guard, and Navbar States
 */

'use strict';

(function () {
  const API_BASE = (window.location.protocol === 'file:' || window.location.port === '5500') ? 'http://localhost:8000/api' : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');
  const API_LOGIN_URL = API_BASE + '/login';

  // Check login status
  function isAdminLoggedIn() {
    return localStorage.getItem('adminLoggedIn') === 'true';
  }

  // Auth Guard for admin.html and /admin route
  function checkAdminPageGuard() {
    const p = window.location.pathname;
    const isCurrentAdminPage =
      p.endsWith('admin.html') ||
      p.includes('/admin.html') ||
      p === '/admin' ||
      p.endsWith('/admin');
    if (isCurrentAdminPage && !isAdminLoggedIn()) {
      window.location.replace('/login?unauthorized=true');
    }
  }

  // Render Navbar Button (Login vs Logged-In Dropdown)
  function renderNavAdmin() {
    const navWraps = document.querySelectorAll('#navAdminWrap');
    if (!navWraps.length) return;

    const loggedIn = isAdminLoggedIn();

    navWraps.forEach(wrap => {
      if (loggedIn) {
        wrap.innerHTML = `
          <div class="dropdown">
            <button class="btn-nav-admin is-logged-in dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-shield-check me-1 text-success"></i> Panel Admin
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:10px;font-size:.88rem;min-width:180px;">
              <li><a class="dropdown-item py-2" href="/admin"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</a></li>
              <li><a class="dropdown-item py-2" href="/admin#kelola-potensi"><i class="bi bi-shop me-2 text-warning"></i>Kelola Potensi</a></li>
              <li><a class="dropdown-item py-2" href="/admin#galeri"><i class="bi bi-images me-2 text-info"></i>Galeri Kegiatan</a></li>
              <li><hr class="dropdown-divider my-1"></li>
              <li><button class="dropdown-item py-2 text-danger nav-btn-logout" type="button"><i class="bi bi-box-arrow-right me-2"></i>Logout</button></li>
            </ul>
          </div>
        `;
      } else {
        wrap.innerHTML = `
          <button type="button" class="btn-nav-admin" data-bs-toggle="modal" data-bs-target="#modalLoginAdmin">
            <i class="bi bi-person-lock me-1"></i> Login Admin
          </button>
        `;
      }
    });

    // Bind logout buttons in navbar
    document.querySelectorAll('.nav-btn-logout').forEach(btn => {
      btn.addEventListener('click', handleLogout);
    });
  }

  // Fungsi bersama: tangani 401 — hapus semua data sesi dan redirect ke halaman login.
  // Diekspor ke window agar bisa dipakai oleh admin.blade.php dan kode lain.
  function handleUnauthorized() {
    localStorage.removeItem('adminLoggedIn');
    localStorage.removeItem('adminUser');
    localStorage.removeItem('ep_admin_session');
    sessionStorage.removeItem('ep_admin_session');
    window.location.replace('/login?unauthorized=true');
  }
  window.epHandleUnauthorized = handleUnauthorized;

  // Handle Logout
  function handleLogout(e) {
    if (e) e.preventDefault();
    if (confirm('Apakah Anda yakin ingin keluar dari panel admin?')) {
      // Ambil token SEBELUM sesi dihapus, lalu kirim agar server mencabut token ini.
      var logoutToken = null;
      try {
        var rawSession = localStorage.getItem('ep_admin_session') || sessionStorage.getItem('ep_admin_session');
        if (rawSession) logoutToken = JSON.parse(rawSession).token || null;
      } catch (err) {}
      localStorage.removeItem('adminLoggedIn');
      localStorage.removeItem('adminUser');
      localStorage.removeItem('ep_admin_session');
      sessionStorage.removeItem('ep_admin_session');
      if (logoutToken) {
        try {
          fetch((typeof API_BASE !== 'undefined' ? API_BASE : '/api') + '/auth/logout', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + logoutToken }
          }).catch(() => {});
        } catch (err) {}
      }
      
      // If currently on admin page, redirect to index
      if (window.location.pathname.endsWith('admin.html') || window.location.pathname === '/admin' || window.location.pathname.endsWith('/admin')) {
        window.location.replace('/');
      } else {
        // Otherwise re-render navbar and show alert
        renderNavAdmin();
        if (typeof showToast === 'function') {
          showToast('Anda telah berhasil keluar dari akun admin.', 'info');
        } else {
          alert('Anda telah berhasil keluar.');
        }
      }
    }
  }

  // Bind Login Form
  function initLoginForm() {
    const form = document.getElementById('formLoginAdmin');
    if (!form) return;

    const alertBox = document.getElementById('loginAlertBox');
    const alertMsg = document.getElementById('loginAlertMsg');
    const submitBtn = document.getElementById('btnSubmitLogin');
    const usernameInput = document.getElementById('adminUsername');
    const passwordInput = document.getElementById('adminPassword');
    const togglePassBtn = document.getElementById('btnTogglePassword');
    const eyeIcon = document.getElementById('eyeIcon');

    // Toggle password visibility
    if (togglePassBtn && passwordInput) {
      togglePassBtn.addEventListener('click', () => {
        const isPass = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isPass ? 'text' : 'password');
        if (eyeIcon) {
          eyeIcon.className = isPass ? 'bi bi-eye-slash' : 'bi bi-eye';
        }
      });
    }


    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const username = usernameInput.value.trim();
      const password = passwordInput.value.trim();

      if (!username || !password) {
        if (alertBox && alertMsg) {
          alertMsg.textContent = 'Harap isi username dan kata sandi.';
          alertBox.classList.remove('d-none');
        }
        return;
      }

      // Hide previous alert & show loading
      if (alertBox) alertBox.classList.add('d-none');
      const originalBtnText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memverifikasi...';

      let loginSuccess = false;
      let authToken = null;
      let userData = { name: 'Administrator', role: 'admin' };
      let errorMsg = 'Email atau password salah.';

      // 1. Try backend API with timeout (10 detik agar server lambat tidak dianggap gagal)
      try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000);

        const response = await fetch(API_LOGIN_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: username, password }),
          signal: controller.signal
        });
        clearTimeout(timeoutId);

        if (response.ok) {
          const resData = await response.json();
          if (resData.status === 'success' && resData.data && resData.data.token) {
            loginSuccess = true;
            authToken = resData.data.token;
            if (resData.data.user) userData = resData.data.user;
          }
        } else if (response.status === 401) {
          errorMsg = 'Email atau password salah.';
        } else if (response.status === 429) {
          errorMsg = 'Terlalu banyak percobaan, coba lagi nanti.';
        }
      } catch (err) {
        // AbortError = timeout; TypeError = jaringan putus — keduanya tampil pesan yang sama
        errorMsg = 'Server tidak dapat dihubungi. Periksa koneksi Anda.';
      }

      // Handle Result
      if (loginSuccess) {
        localStorage.setItem('adminLoggedIn', 'true');
        localStorage.setItem('adminUser', JSON.stringify(userData));
        localStorage.setItem('ep_admin_session', JSON.stringify({ isLoggedIn: true, user: userData, token: authToken }));

        submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Login Berhasil!';
        submitBtn.classList.remove('btn-primary-custom');
        submitBtn.classList.add('btn-success');

        setTimeout(() => {
          window.location.href = '/admin';
        }, 600);
      } else {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        if (alertBox && alertMsg) {
          alertMsg.textContent = errorMsg;
          alertBox.classList.remove('d-none');
        }
        if (passwordInput) passwordInput.value = '';
      }
    });
  }

  // Check URL query parameters (auto open login modal if action=login)
  function checkUrlActions() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('action') === 'login' || params.get('login') === 'true') {
      const modalEl = document.getElementById('modalLoginAdmin');
      if (modalEl && typeof bootstrap !== 'undefined') {
        setTimeout(() => {
          const modal = new bootstrap.Modal(modalEl);
          modal.show();
        }, 300);
      }
    }
  }

  // Bind Admin Panel Page Logout Buttons
  function initAdminPageLogout() {
    const logoutBtns = [
      document.getElementById('btnLogoutSidebar'),
      document.getElementById('btnLogoutTopbar'),
      document.getElementById('sidebarNavLogout')
    ];

    logoutBtns.forEach(btn => {
      if (btn) btn.addEventListener('click', handleLogout);
    });
  }

  // Run on DOM ready
  document.addEventListener('DOMContentLoaded', () => {
    checkAdminPageGuard();
    renderNavAdmin();
    initLoginForm();
    checkUrlActions();
    initAdminPageLogout();
  });
})();

