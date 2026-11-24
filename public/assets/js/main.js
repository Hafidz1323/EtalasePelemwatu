/**
 * EtalasePelemwatu — Main JavaScript
 * Landing Page Interactions
 */

'use strict';

// ── Utility ──────────────────────────────────────────────────
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

function showToast(message, type = 'info', duration = 3500) {
  const container = document.getElementById('toastContainer');
  if (!container) return;
  const icons = { info: 'bi-info-circle-fill', success: 'bi-check-circle-fill', error: 'bi-x-circle-fill' };
  const colors = { info: 'var(--riviera)', success: '#28a745', error: '#dc3545' };
  const toast = document.createElement('div');
  toast.className = `custom-toast ${type}`;
  toast.innerHTML = `<i class="bi ${icons[type] || icons.info}" style="color:${colors[type]};font-size:1.2rem;flex-shrink:0"></i><span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(40px)';
    toast.style.transition = 'all .35s ease';
    setTimeout(() => toast.remove(), 380);
  }, duration);
}

// ── Navbar Scroll Effect ──────────────────────────────────────
function initNavbar() {
  const nav = document.getElementById('mainNavbar');
  const header = document.getElementById('siteHeader');
  if (!nav) return;
  window.addEventListener('scroll', () => {
    const isScrolled = window.scrollY > 40;
    nav.classList.toggle('scrolled', isScrolled);
    if (header) header.classList.toggle('scrolled', isScrolled);
  }, { passive: true });

  // Active nav link based on scroll
  const sections = $$('section[id], div[id]');
  const navLinks = $$('#mainNavbar .nav-link[href*="#"]');
  window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(s => {
      if (window.scrollY >= s.offsetTop - 120) current = s.id;
    });
    navLinks.forEach(link => {
      link.classList.toggle('active', link.getAttribute('href').includes(current) && current !== '');
    });
  }, { passive: true });
}

// ── Back to Top ───────────────────────────────────────────────
function initBackToTop() {
  const btn = document.getElementById('backToTop');
  if (!btn) return;
  window.addEventListener('scroll', () => btn.classList.toggle('show', window.scrollY > 400), { passive: true });
  btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

// ── Scroll Reveal ─────────────────────────────────────────────
function initScrollReveal() {
  const els = $$('.reveal');
  if (!els.length) return;
  const obs = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
  }, { threshold: 0.12 });
  els.forEach(el => obs.observe(el));
}

// ── Counter Animation ─────────────────────────────────────────
function animateCounter(el, target, duration = 1800) {
  let start = 0;
  const step = target / (duration / 16);
  const timer = setInterval(() => {
    start += step;
    if (start >= target) { start = target; clearInterval(timer); }
    el.textContent = Math.floor(start) + (el.dataset.suffix || '');
  }, 16);
}

function initCounters() {
  const counters = $$('.stat-num[data-target]');
  if (!counters.length) return;
  const obs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        animateCounter(e.target, parseInt(e.target.dataset.target));
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.5 });
  counters.forEach(c => obs.observe(c));
}

// ── Profil Desa Tabs ──────────────────────────────────────────
function initProfilTabs() {
  const btns = $$('.profil-nav-btn');
  const panes = $$('.profil-tab-pane');
  btns.forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.dataset.tab;
      btns.forEach(b => b.classList.remove('active'));
      panes.forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      const pane = document.getElementById(target);
      if (pane) pane.classList.add('active');
    });
  });
}

// ── Katalog Filter ────────────────────────────────────────────
function initKatalogFilter() {
  const filterBtns = $$('.filter-btn');
  const getCards = () => $$('.potensi-card');
  const searchInput = document.getElementById('katalogSearch');
  const clearBtn = document.getElementById('katalogSearchClear');
  const resultInfo = document.getElementById('katalogSearchResultInfo');
  const resultText = document.getElementById('searchResultText');
  const emptyState = document.getElementById('katalogEmptyState');
  const emptyMsg = document.getElementById('emptyStateMsg');
  const resetBtn = document.getElementById('btnResetFilterSearch');
  const emptyResetBtn = document.getElementById('btnResetEmptyState');

  let currentCategory = 'all';
  let searchQuery = '';

  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, m => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[m]);
  }

  function applyFilters() {
    let visibleCount = 0;
    const query = searchQuery.trim().toLowerCase();

    getCards().forEach(card => {
      const category = card.dataset.category || '';
      const title = (card.querySelector('.potensi-name-tag') || card.querySelector('h5'))?.textContent.toLowerCase() || '';
      const desc = card.querySelector('p')?.textContent.toLowerCase() || '';
      const badge = card.querySelector('.potensi-badge')?.textContent.toLowerCase() || '';

      const matchCategory = (currentCategory === 'all' || category === currentCategory);
      const matchSearch = (!query || title.includes(query) || desc.includes(query) || badge.includes(query));

      if (matchCategory && matchSearch) {
        card.style.display = '';
        card.style.animation = 'fadeInUp .35s ease';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    // Toggle clear button on search input
    if (clearBtn) {
      clearBtn.style.display = query ? 'flex' : 'none';
    }

    // Toggle empty state
    if (emptyState) {
      if (visibleCount === 0) {
        emptyState.style.display = 'block';
        if (emptyMsg) {
          if (query && currentCategory !== 'all') {
            const catName = document.querySelector('.filter-btn.active')?.textContent.trim() || currentCategory;
            emptyMsg.textContent = `Tidak ditemukan potensi dalam kategori "${catName}" dengan kata kunci "${query}".`;
          } else if (query) {
            emptyMsg.textContent = `Tidak ditemukan potensi dengan kata kunci "${query}".`;
          } else {
            emptyMsg.textContent = 'Belum ada data potensi pada kategori ini.';
          }
        }
      } else {
        emptyState.style.display = 'none';
      }
    }

    // Update result info text
    if (resultInfo && resultText) {
      if (query || currentCategory !== 'all') {
        resultInfo.style.display = 'flex';
        let html = `Menampilkan <strong>${visibleCount}</strong> potensi`;
        if (query) {
          html += ` untuk kata kunci "<em>${escapeHtml(query)}</em>"`;
        }
        if (currentCategory !== 'all') {
          const catName = document.querySelector('.filter-btn.active')?.textContent.trim() || currentCategory;
          html += ` dalam kategori <strong>${escapeHtml(catName)}</strong>`;
        }
        resultText.innerHTML = html;
      } else {
        resultInfo.style.display = 'none';
      }
    }
  }

  // Tombol kata kunci cepat (Dimsum, Laundry, ...)
  $$('.quick-tag-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      searchQuery = btn.dataset.keyword || '';
      if (searchInput) searchInput.value = searchQuery;
      applyFilters();
    });
  });

  window.applyKatalogFilters = applyFilters;

  // Category buttons
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentCategory = btn.dataset.filter;
      applyFilters();
    });
  });

  // Search input typing
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      searchQuery = e.target.value;
      applyFilters();
    });

    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        searchInput.value = '';
        searchQuery = '';
        applyFilters();
      }
    });
  }

  // Clear search input button
  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      if (searchInput) {
        searchInput.value = '';
        searchQuery = '';
        searchInput.focus();
        applyFilters();
      }
    });
  }

  // Reset all filters & search
  function resetAll() {
    if (searchInput) searchInput.value = '';
    searchQuery = '';
    currentCategory = 'all';
    filterBtns.forEach(b => b.classList.toggle('active', b.dataset.filter === 'all'));
    applyFilters();
  }

  if (resetBtn) resetBtn.addEventListener('click', resetAll);
  if (emptyResetBtn) emptyResetBtn.addEventListener('click', resetAll);
}

// ── Katalog Detail Modal ──────────────────────────────────────
const katalogData = {
  'omah-dimsum': {
    nama: 'Omah Dimsum',
    kategori: 'Usaha & Produk',
    emoji: '🥟',
    bg: '#fff0e8',
    deskripsi: 'Usaha kuliner yang menyajikan berbagai jenis dimsum pilihan dengan cita rasa khas. Melayani pembelian langsung maupun pemesanan.',
    lokasi: 'Desa Pelemwatu, Kec. Menganti, Kab. Gresik',
    kontak: 'Hubungi melalui formulir pendaftaran',
    status: 'Terverifikasi',
  },
  'warung-prasmanan': {
    nama: 'Warung Prasmanan Selarasnasi',
    kategori: 'Usaha & Produk',
    emoji: '🍛',
    bg: '#fff0e8',
    deskripsi: 'Warung makan prasmanan yang menyajikan berbagai menu masakan rumahan. Menjadi pilihan masyarakat sekitar untuk makan siang dan malam.',
    lokasi: 'Desa Pelemwatu, Kec. Menganti, Kab. Gresik',
    kontak: 'Hubungi melalui formulir pendaftaran',
    status: 'Terverifikasi',
  },
  'ratu-frozen': {
    nama: 'Ratu Frozen Food Pelemwatu',
    kategori: 'Usaha & Produk',
    emoji: '🧊',
    bg: '#fff0e8',
    deskripsi: 'Usaha penjualan frozen food dengan berbagai pilihan produk beku berkualitas. Tersedia untuk kebutuhan rumah tangga dan usaha kuliner.',
    lokasi: 'Desa Pelemwatu, Kec. Menganti, Kab. Gresik',
    kontak: 'Hubungi melalui formulir pendaftaran',
    status: 'Terverifikasi',
  },
  'moni-laundry': {
    nama: 'Moni Laundry 24 Jam',
    kategori: 'Jasa',
    emoji: '👕',
    bg: '#e8edf5',
    deskripsi: 'Layanan jasa laundry yang beroperasi 24 jam untuk memenuhi kebutuhan pencucian pakaian masyarakat dengan cepat dan bersih.',
    lokasi: 'Desa Pelemwatu, Kec. Menganti, Kab. Gresik',
    kontak: 'Hubungi melalui formulir pendaftaran',
    status: 'Terverifikasi',
  },
  'one-laundry': {
    nama: 'One Laundry Koin',
    kategori: 'Jasa',
    emoji: '🪙',
    bg: '#e8edf5',
    deskripsi: 'Layanan laundry mandiri berbasis koin yang praktis dan terjangkau. Cocok untuk warga yang ingin mencuci pakaian secara mandiri.',
    lokasi: 'Desa Pelemwatu, Kec. Menganti, Kab. Gresik',
    kontak: 'Hubungi melalui formulir pendaftaran',
    status: 'Terverifikasi',
  },
  'my-kost': {
    nama: 'My Kost',
    kategori: 'Hunian',
    emoji: '🏠',
    bg: '#f5f0e8',
    deskripsi: 'Hunian kost yang nyaman dan terjangkau di lingkungan Desa Pelemwatu. Tersedia berbagai pilihan kamar yang dapat disesuaikan dengan kebutuhan.',
    lokasi: 'Desa Pelemwatu, Kec. Menganti, Kab. Gresik',
    kontak: 'Hubungi melalui formulir pendaftaran',
    status: 'Terverifikasi',
  },
  'rama-kost': {
    nama: 'RAMA Kost',
    kategori: 'Hunian',
    emoji: '🏡',
    bg: '#f5f0e8',
    deskripsi: 'Pilihan hunian kost dengan fasilitas lengkap di Desa Pelemwatu. Lingkungan nyaman dan strategis untuk warga dan pendatang baru.',
    lokasi: 'Desa Pelemwatu, Kec. Menganti, Kab. Gresik',
    kontak: 'Hubungi melalui formulir pendaftaran',
    status: 'Terverifikasi',
  },
};

function initDetailModal() {
  // Delegasi: berlaku juga untuk kartu yang dibuat dinamis dari API.
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-detail');
    if (!btn) return;
    const id = btn.dataset.id;
    const data = katalogData[id] || (window.__katalogDinamis || {})[id];
    if (!data) return;

    const modal = document.getElementById('detailModal');
    if (!modal) return;
    modal.querySelector('.modal-title').textContent = data.nama;
    modal.querySelector('#detail-img').style.background = data.bg;
    modal.querySelector('#detail-emoji').textContent = data.emoji;
    modal.querySelector('#detail-nama').textContent = data.nama;
    modal.querySelector('#detail-kategori').textContent = data.kategori;
    modal.querySelector('#detail-deskripsi').textContent = data.deskripsi;
    modal.querySelector('#detail-lokasi').textContent = data.lokasi;
    modal.querySelector('#detail-status').textContent = data.status;

    bootstrap.Modal.getOrCreateInstance(modal).show();
  });
}

// ── Katalog dinamis dari API ──────────────────────────────────
const KATEGORI_TAMPIL = {
  usaha:   { img: 'assets/img/foto-warung-desa.jpg',    emoji: '🍽️', bg: '#fff0e8' },
  jasa:    { img: 'assets/img/foto-daftar-potensi.jpg', emoji: '🧺', bg: '#e8f4ff' },
  hunian:  { img: 'assets/img/foto-daftar-potensi.jpg', emoji: '🏠', bg: '#eaf7ee' },
  barang:  { img: 'assets/img/foto-daftar-potensi.jpg', emoji: '🛍️', bg: '#fff8e1' },
  lainnya: { img: 'assets/img/foto-daftar-potensi.jpg', emoji: '📍', bg: '#f1f3f5' },
};

function slugKategori(cat) {
  const n = ((cat && cat.name) || '').toLowerCase();
  if (n.startsWith('usaha')) return 'usaha';
  if (n.startsWith('jasa')) return 'jasa';
  if (n.startsWith('hunian')) return 'hunian';
  if (n.startsWith('barang')) return 'barang';
  return 'lainnya';
}

function buatKartuPotensi(p) {
  const slug = slugKategori(p.category);
  const tampil = KATEGORI_TAMPIL[slug];
  const key = 'api-' + p.id;
  const desc = p.description || '';

  window.__katalogDinamis[key] = {
    nama: p.name,
    kategori: (p.category && p.category.name) || 'Lainnya',
    emoji: tampil.emoji,
    bg: tampil.bg,
    deskripsi: desc || '-',
    lokasi: p.location || p.address || 'Desa Pelemwatu, Kec. Menganti, Kab. Gresik',
    status: 'Terverifikasi',
  };

  const mk = (tag, cls, text) => {
    const n = document.createElement(tag);
    if (cls) n.className = cls;
    if (text != null) n.textContent = text;   // textContent: aman dari XSS
    return n;
  };

  const card = mk('div', 'potensi-card');
  card.dataset.category = slug;
  card.dataset.dinamis = '1';

  const imgWrap = mk('div', 'potensi-card-img');
  const img = document.createElement('img');
  img.src = tampil.img;
  img.alt = p.name;
  img.loading = 'lazy';
  imgWrap.appendChild(img);

  const body = mk('div', 'potensi-card-body');
  body.appendChild(mk('span', 'potensi-name-tag', p.name));
  body.appendChild(mk('p', '', desc.length > 160 ? desc.slice(0, 157).trimEnd() + '…' : desc));
  const btn = mk('button', 'btn-detail', 'Lihat Detail →');
  btn.type = 'button';
  btn.dataset.id = key;
  body.appendChild(btn);

  card.appendChild(imgWrap);
  card.appendChild(body);
  return card;
}

// ── Smooth Scroll for Anchor Links ───────────────────────────
function initSmoothScroll() {
  $$('a[href^="#"]').forEach(link => {
    link.addEventListener('click', (e) => {
      const target = link.getAttribute('href');
      if (target === '#') return;
      const el = document.querySelector(target);
      if (!el) return;
      e.preventDefault();
      // Close mobile menu if open
      const toggler = document.querySelector('.navbar-collapse');
      if (toggler && toggler.classList.contains('show')) {
        bootstrap.Collapse.getInstance(toggler)?.hide();
      }
      const navH = document.getElementById('siteHeader')?.offsetHeight || document.getElementById('mainNavbar')?.offsetHeight || 70;
      window.scrollTo({ top: el.offsetTop - navH, behavior: 'smooth' });
    });
  });
}

// ── Gallery Item Click ────────────────────────────────────────
function initGallery() {
  $$('.gallery-item').forEach(item => {
    item.addEventListener('click', () => showToast('Foto asli desa akan ditampilkan setelah gambar tersedia.', 'info'));
  });
}

// ── Map Placeholder Click ─────────────────────────────────────
function initMapPlaceholder() {
  const mp = $('.map-placeholder');
  if (mp) mp.addEventListener('click', () => showToast('Peta interaktif akan tersedia setelah koordinat desa dikonfigurasi.', 'info'));
}

// ── Init All ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  initNavbar();
  initBackToTop();
  initScrollReveal();
  initCounters();
  initProfilTabs();
  initKatalogFilter();
  initDetailModal();
  initSmoothScroll();
  initGallery();
  initMapPlaceholder();
});




// ── Load Katalog Dinamis dari API dengan Pagination & Loading State ──────────────────
let currentKatalogPage = 1;
const KATALOG_PER_PAGE = 6;

async function loadKatalogFromApi(page = 1) {
  currentKatalogPage = page;
  const grid = document.querySelector(".katalog-grid");
  const statPotensi = document.querySelector(".hero-stat-strip .hero-stat-item:last-child .hero-stat-value");
  if (!grid) return;

  // 1. Tampilkan Skeleton Loading State
  grid.innerHTML = `
    <div class="col-12 text-center py-5 my-3" id="katalogLoadingState">
      <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;color:#f2630f !important;">
        <span class="visually-hidden">Memuat data potensi desa...</span>
      </div>
      <p class="mt-3 text-muted fw-semibold" style="font-size:0.95rem;">Sedang memuat data potensi lokal Desa Pelemwatu...</p>
    </div>
  `;

  try {
    const res = await fetch(`/api/potensi?page=${page}&per_page=${KATALOG_PER_PAGE}`);
    const json = await res.json();

    if (res.ok && json.status === "success") {
      const items = json.data || [];
      const pagination = json.pagination || null;

      if (pagination && statPotensi) {
        statPotensi.textContent = pagination.total + "+";
      }

      if (items.length === 0) {
        grid.innerHTML = `
          <div class="col-12 text-center py-5" id="katalogEmptyState">
            <div class="empty-icon text-muted mb-3" style="font-size:3rem;"><i class="bi bi-inbox"></i></div>
            <h5 class="fw-bold">Belum Ada Potensi Terdaftar</h5>
            <p class="text-muted small">Saat ini belum ada potensi yang disetujui untuk ditampilkan di katalog.</p>
          </div>
        `;
        renderPaginationControls(null);
        return;
      }

      // 2. Render Cards
      let html = "";
      items.forEach(item => {
        const catMap = { 1: "usaha", 2: "jasa", 3: "hunian", 4: "barang" };
        const catSlug = catMap[item.category_id] || "usaha";
        const catName = item.category ? item.category.name : "Usaha & Produk";
        const foto = (item.images && item.images.length > 0) ? item.images[0].image_path : "assets/img/foto-warung-desa.jpg";
        const waLink = item.whatsapp ? `https://wa.me/${item.whatsapp}` : "#";

        html += `
          <div class="potensi-card" data-category="${catSlug}">
            <div class="potensi-card-img position-relative">
              <img src="${foto}" alt="Foto ${item.name}" loading="lazy" style="height:190px;object-fit:cover;width:100%;border-top-left-radius:12px;border-top-right-radius:12px;">
              <span class="potensi-badge badge-${catSlug}">${catName}</span>
            </div>
            <div class="potensi-card-body p-3">
              <h5 class="fw-bold mb-1 text-truncate" title="${item.name}">${item.name}</h5>
              <p class="text-muted small mb-2"><i class="bi bi-person me-1"></i>${item.owner_name} &bull; <i class="bi bi-geo-alt"></i> ${item.location || "Pelemwatu"}</p>
              <p class="potensi-desc small text-secondary" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:38px;">${item.description || "Potensi lokal masyarakat Desa Pelemwatu."}</p>
              <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                <a href="${waLink}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1" aria-label="Hubungi pemilik ${item.name} via WhatsApp"><i class="bi bi-whatsapp"></i> Hubungi</a>
                <span class="badge bg-light text-dark border d-flex align-items-center gap-1"><i class="bi bi-patch-check-fill text-primary"></i> Terverifikasi</span>
              </div>
            </div>
          </div>
        `;
      });

      // Tambahkan empty state filter
      html += `
        <div id="katalogEmptyState" class="katalog-empty-state col-12 text-center py-5" style="display:none;">
          <div class="empty-icon text-muted mb-2" style="font-size:2.5rem;"><i class="bi bi-search"></i></div>
          <h5 class="fw-bold">Potensi Tidak Ditemukan</h5>
          <p id="emptyStateMsg" class="text-muted small">Tidak ada potensi lokal yang cocok dengan kata kunci pencarian Anda.</p>
          <button type="button" class="btn btn-sm btn-outline-primary" id="btnResetEmptyState">
            Tampilkan Semua Potensi
          </button>
        </div>
      `;

      grid.innerHTML = html;

      // 3. Render Pagination
      renderPaginationControls(pagination);

      // Re-init filter
      initKatalogFilter();
    }
  } catch (err) {
    grid.innerHTML = `
      <div class="col-12 text-center py-5">
        <p class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat data potensi. Silakan segarkan halaman.</p>
      </div>
    `;
  }
}

function renderPaginationControls(pagination) {
  let navContainer = document.getElementById("katalogPaginationNav");
  if (!navContainer) {
    const katalogSection = document.getElementById("katalog");
    if (!katalogSection) return;
    navContainer = document.createElement("div");
    navContainer.id = "katalogPaginationNav";
    navContainer.className = "d-flex justify-content-center align-items-center gap-2 mt-4 flex-wrap";
    const container = katalogSection.querySelector(".container") || katalogSection;
    container.appendChild(navContainer);
  }

  if (!pagination || pagination.last_page <= 1) {
    navContainer.innerHTML = "";
    return;
  }

  let btns = "";
  btns += `<button class="btn btn-sm btn-outline-secondary ${pagination.current_page <= 1 ? "disabled" : ""}" onclick="loadKatalogFromApi(${pagination.current_page - 1})" aria-label="Halaman Sebelumnya"><i class="bi bi-chevron-left"></i></button>`;

  for (let i = 1; i <= pagination.last_page; i++) {
    btns += `<button class="btn btn-sm ${i === pagination.current_page ? "btn-primary" : "btn-outline-secondary"}" style="${i === pagination.current_page ? "background:#f2630f;border-color:#f2630f;" : ""}" onclick="loadKatalogFromApi(${i})" aria-label="Ke halaman ${i}">${i}</button>`;
  }

  btns += `<button class="btn btn-sm btn-outline-secondary ${pagination.current_page >= pagination.last_page ? "disabled" : ""}" onclick="loadKatalogFromApi(${pagination.current_page + 1})" aria-label="Halaman Berikutnya"><i class="bi bi-chevron-right"></i></button>`;

  navContainer.innerHTML = btns;
}
