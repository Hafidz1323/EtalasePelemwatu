/**
 * EtalasePelemwatu — Warga Authentication Module
 * Mengelola sesi warga: register, login, logout, dan guard halaman.
 * Kunci localStorage: 'ep_warga_session' = { token, user: {id, name, email, phone, role} }
 */
'use strict';

(function () {
  const API_BASE = (window.location.protocol === 'file:' || window.location.port === '5500')
    ? 'http://localhost:8000/api'
    : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');

  /* ── Helpers sesi ── */
  function getWargaSession() {
    try { return JSON.parse(localStorage.getItem('ep_warga_session') || 'null'); } catch(e) { return null; }
  }
  function isWargaLoggedIn() {
    var s = getWargaSession();
    return !!(s && s.token && s.user && s.user.role === 'warga');
  }
  function getWargaToken() {
    var s = getWargaSession(); return s ? (s.token || null) : null;
  }
  function getWargaUser() {
    var s = getWargaSession(); return s ? (s.user || null) : null;
  }
  function saveWargaSession(token, user) {
    localStorage.setItem('ep_warga_session', JSON.stringify({ token: token, user: user }));
  }
  function clearWargaSession() {
    localStorage.removeItem('ep_warga_session');
  }

  /* ── Ekspor ke window ── */
  window.epWarga = {
    isLoggedIn:   isWargaLoggedIn,
    getToken:     getWargaToken,
    getUser:      getWargaUser,
    saveSession:  saveWargaSession,
    clearSession: clearWargaSession,
    apiBase:      API_BASE,
  };

  /* ── Guard halaman: profil-warga dan daftar-potensi wajib login ── */
  function checkWargaPageGuard() {
    var p = window.location.pathname;
    var isProfilPage   = p === '/profil-warga'   || p.endsWith('/profil-warga')   || p.endsWith('profil-warga.html');
    var isDaftarPage   = p === '/daftar-potensi' || p.endsWith('/daftar-potensi') || p.endsWith('daftar-potensi.html');
    if ((isProfilPage || isDaftarPage) && !isWargaLoggedIn()) {
      var next = isDaftarPage ? 'daftar-potensi' : 'profil-warga';
      window.location.replace('/register?next=' + next);
    }
  }

  /* ── Render navbar state warga ── */
  function renderWargaNavbar() {
    var wraps = document.querySelectorAll('#navWargaWrap');
    if (!wraps.length) return;
    var loggedIn = isWargaLoggedIn();
    var user = getWargaUser();

    wraps.forEach(function(wrap) {
      if (loggedIn && user) {
        // Sudah login: icon + nama singkat, klik buka dropdown
        var rawName = user.name || 'Profil';
        var escFn = window.escapeHtml || function(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); };
        var safeName = escFn(rawName);
        var firstName = escFn(rawName.split(' ')[0]);
        wrap.innerHTML =
          '<div class="dropdown">' +
            '<button class="ep-nav-user dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="' + safeName + '">' +
              '<i class="bi bi-person-circle"></i>' +
              '<span class="ep-nav-user-name">' + firstName + '</span>' +
            '</button>' +
            '<ul class="dropdown-menu dropdown-menu-end shadow-sm ep-user-menu">' +
              '<li class="ep-user-menu-header"><i class="bi bi-person-fill me-2"></i>' + safeName + '</li>' +
              '<li><hr class="dropdown-divider my-1"></li>' +
              '<li><a class="dropdown-item py-2" href="/profil-warga"><i class="bi bi-person me-2 text-primary"></i>Profil Saya</a></li>' +
              '<li><a class="dropdown-item py-2" href="/profil-warga#potensi"><i class="bi bi-shop me-2 text-warning"></i>Potensi Saya</a></li>' +
              '<li><a class="dropdown-item py-2" href="/daftar-potensi"><i class="bi bi-plus-circle me-2 text-success"></i>Daftarkan Potensi</a></li>' +
              '<li><hr class="dropdown-divider my-1"></li>' +
              '<li><button class="dropdown-item py-2 text-danger warga-btn-logout" type="button"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button></li>' +
            '</ul>' +
          '</div>';
      } else {
        // Belum login: icon saja, hover/klik buka dropdown Masuk / Daftar
        wrap.innerHTML =
          '<div class="dropdown ep-nav-guest">' +
            '<button class="ep-nav-user dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Masuk atau Daftar">' +
              '<i class="bi bi-person-circle"></i>' +
            '</button>' +
            '<ul class="dropdown-menu dropdown-menu-end shadow-sm ep-user-menu">' +
              '<li><a class="dropdown-item py-2 fw-semibold" href="/register#tab-masuk"><i class="bi bi-box-arrow-in-right me-2 text-primary"></i>Masuk</a></li>' +
              '<li><a class="dropdown-item py-2" href="/register#tab-daftar"><i class="bi bi-person-plus me-2 text-success"></i>Daftar Akun</a></li>' +
            '</ul>' +
          '</div>';
      }
    });

    document.querySelectorAll('.warga-btn-logout').forEach(function(btn) {
      btn.addEventListener('click', handleWargaLogout);
    });
  }

  /* ── Logout ── */
  function handleWargaLogout(e) {
    if (e) e.preventDefault();
    var token = getWargaToken();
    clearWargaSession();
    if (token) {
      fetch(API_BASE + '/warga/logout', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }
      }).catch(function() {});
    }
    var p = window.location.pathname;
    if (p === '/profil-warga' || p.endsWith('profil-warga.html') ||
        p === '/daftar-potensi' || p.endsWith('daftar-potensi.html')) {
      window.location.replace('/register');
    } else {
      renderWargaNavbar();
    }
  }
  window.epWarga.logout = handleWargaLogout;

  /* ── Jalankan saat DOM siap ── */
  document.addEventListener('DOMContentLoaded', function() {
    checkWargaPageGuard();
    renderWargaNavbar();

    // Pre-fill form daftar-potensi jika warga sedang login
    var form = document.getElementById('pendaftaranForm');
    if (form && isWargaLoggedIn()) {
      var user = getWargaUser();
      if (user) {
        var fNama  = document.getElementById('f-nama');
        var fTelp  = document.getElementById('f-telp');
        var fEmail = document.getElementById('f-email');
        if (fNama  && !fNama.value  && user.name)  fNama.value  = user.name;
        if (fTelp  && !fTelp.value  && user.phone) fTelp.value  = user.phone;
        if (fEmail && !fEmail.value && user.email)  fEmail.value = user.email;
      }
    }
  });
})();
