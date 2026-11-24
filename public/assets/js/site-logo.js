(function () {
  'use strict';

  function initSiteLogo() {
    var targets = document.querySelectorAll('[data-site-logo]');
    if (!targets.length) return;

    var apiBase = (window.location.protocol === 'file:' || window.location.port === '5500')
      ? 'http://127.0.0.1:8000/api'
      : '/api';

    fetch(apiBase + '/settings/logo')
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.status === 'success' && res.data && res.data.logo_url) {
          var logoUrl = res.data.logo_url;
          document.querySelectorAll('[data-site-logo]').forEach(function (el) {
            if (el.tagName === 'IMG') {
              el.src = logoUrl;
              el.style.display = '';
            }
          });
          document.querySelectorAll('.logo-fallback').forEach(function (fb) {
            fb.style.display = 'none';
          });
        }
      })
      .catch(function () {});
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSiteLogo);
  } else {
    initSiteLogo();
  }
})();
