/**
 * Shared Utilities for EtalasePelemwatu
 * - escapeHtml (XSS sanitizer)
 * - sanitizeSrc (safe media/image URL: only allows '/uploads/', 'assets/', or 'https://')
 * - showToast (Bootstrap-styled toast notification)
 */
(function () {
  'use strict';

  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function sanitizeSrc(url, fallback) {
    fallback = fallback || 'assets/img/logo-desa.png';
    if (!url || typeof url !== 'string') return fallback;
    var trimmed = url.trim();
    if (trimmed.startsWith('/uploads/') ||
        trimmed.startsWith('uploads/') ||
        trimmed.startsWith('/assets/') ||
        trimmed.startsWith('assets/') ||
        trimmed.startsWith('https://')) {
      return escapeHtml(trimmed);
    }
    return fallback;
  }

  function showToast(type, message) {
    // If only 1 argument passed as message
    if (arguments.length === 1) {
      message = type;
      type = 'info';
    }

    var container = document.getElementById('toastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toastContainer';
      container.style.cssText = 'position:fixed;bottom:1.5rem;right:1.5rem;z-index:99999;display:flex;flex-direction:column;gap:.6rem;pointer-events:none;';
      document.body.appendChild(container);
    }

    var icons = {
      success: 'bi-check-circle-fill',
      error: 'bi-x-circle-fill',
      warning: 'bi-exclamation-triangle-fill',
      info: 'bi-info-circle-fill'
    };
    var colors = {
      success: '#28a745',
      error: '#dc3545',
      warning: '#ffc107',
      info: '#0d6efd'
    };

    var toast = document.createElement('div');
    toast.className = 'custom-toast toast-fade-in';
    toast.style.cssText = 'display:flex;align-items:center;gap:.75rem;padding:.75rem 1.25rem;border-radius:12px;background:#ffffff;color:#1e293b;box-shadow:0 8px 24px rgba(0,0,0,0.18);border:1px solid #e2e8f0;font-size:.88rem;font-weight:600;pointer-events:auto;min-width:240px;max-width:380px;transition:all .3s ease;';

    var iconClass = icons[type] || icons.info;
    var iconColor = colors[type] || colors.info;

    var iconEl = document.createElement('i');
    iconEl.className = 'bi ' + iconClass;
    iconEl.style.cssText = 'color:' + iconColor + ';font-size:1.2rem;flex-shrink:0;';

    var msgEl = document.createElement('span');
    msgEl.textContent = message;

    toast.appendChild(iconEl);
    toast.appendChild(msgEl);
    container.appendChild(toast);

    setTimeout(function () {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      setTimeout(function () {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
      }, 350);
    }, 3500);
  }

  window.escapeHtml = escapeHtml;
  window.sanitizeSrc = sanitizeSrc;
  window.showToast = showToast;
  if (!window.showAdminToast) {
    window.showAdminToast = function (msg, type) { showToast(type || 'info', msg); };
  }
})();
