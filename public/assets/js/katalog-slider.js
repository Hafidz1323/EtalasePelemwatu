/* ============================================================
   Katalog Slider — fetch API, search, filter, detail modal
   Versi: 2.1 — fix image path, API_BASE, filter, email
   ============================================================ */
(function () {
  /* ── API Base: pakai window.APP_URL jika ada (di-inject Blade),
        fallback ke origin+path heuristik untuk XAMPP / artisan serve / Vite ── */
  var API_BASE = (function () {
    if (window.APP_URL) return window.APP_URL.replace(/\/$/, '') + '/api';
    var proto = window.location.protocol;
    var host  = window.location.host;
    var path  = window.location.pathname; // mis. /EtalasePelemwatu/public/
    // Deteksi sub-folder: potong sampai "public" lalu tambah /api
    var pubIdx = path.toLowerCase().indexOf('/public');
    if (pubIdx !== -1) return proto + '//' + host + path.substring(0, pubIdx + 7) + '/api';
    // artisan serve / Vite biasanya di root
    return proto + '//' + host + '/api';
  }());

  var track       = document.getElementById('ksTrack');
  var viewport    = document.getElementById('ksViewport');
  var btnPrev     = document.getElementById('ksPrev');
  var btnNext     = document.getElementById('ksNext');
  var searchInput = document.getElementById('ksSearchInput');
  var searchClear = document.getElementById('ksSearchClear');
  var emptyState  = document.getElementById('ksEmpty');
  if (!track) return;

  var allItems  = [];   // semua data dari API
  var filtered  = [];   // setelah filter/search
  var currentIdx = 0;
  var activeCat  = '';

  /* ── Jumlah card visible berdasarkan lebar ── */
  function getVisible() {
    var w = window.innerWidth;
    if (w >= 1200) return 4;
    if (w >= 900)  return 3;
    if (w >= 600)  return 2;
    return 1;
  }

  /* ── Lebar satu card + gap ── */
  function cardStep() {
    var card = track.querySelector('.ks-card');
    if (!card) return 0;
    var gap = 18; // sesuai CSS gap: 1.1rem ≈ 17.6px
    return card.offsetWidth + gap;
  }

  /* ── Index maksimal ── */
  function maxIdx() {
    return Math.max(0, filtered.length - getVisible());
  }

  /* ── Geser ke posisi ── */
  function goTo(idx) {
    currentIdx = Math.max(0, Math.min(idx, maxIdx()));
    track.style.transform = 'translateX(-' + (currentIdx * cardStep()) + 'px)';
    if (btnPrev) btnPrev.disabled = currentIdx === 0;
    if (btnNext) btnNext.disabled = currentIdx >= maxIdx();
  }

  /* ── Render card dari data item ── */
  function buildCard(item) {
    var cat    = item.category ? item.category.name : (item.jenis || 'Usaha');
    var name   = item.name || '—';
    var jenis  = item.jenis || '';
    var deskr  = item.description || '';
    var lokasi = item.location || item.address || '';
    var wa     = item.whatsapp || '';
    var jam    = item.jam_operasional || '';

    // Foto pertama — resource mengembalikan images[].path (bukan image_path)
    var imgSrc = '';
    var images = item.images || [];
    if (images.length) {
      imgSrc = images[0].path || images[0].image_path || images[0].url || '';
      // pastikan path absolut
      if (imgSrc && !imgSrc.startsWith('http') && !imgSrc.startsWith('/')) {
        imgSrc = '/' + imgSrc;
      }
    }

    var imgHtml = '';
    if (imgSrc) {
      var safeImg = window.sanitizeSrc ? window.sanitizeSrc(imgSrc, '') : imgSrc;
      imgHtml = '<img src="' + safeImg + '" alt="' + esc(name) + '" loading="lazy" onerror="this.style.display=\'none\'">';
    } else {
      imgHtml = '<div class="ks-card-placeholder" style="width:100%;height:100%;min-height:220px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, #1E3B2C 0%, #294e3b 100%);"><i class="bi bi-shop" style="font-size:2.5rem;color:rgba(255,255,255,.2);"></i></div>';
    }

    var div = document.createElement('div');
    div.className = 'ks-card';
    div.innerHTML =
      imgHtml +
      '<div class="ks-card-overlay">' +
        '<span class="ks-card-cat">' + esc(cat) + '</span>' +
        '<div class="ks-card-name">' + esc(name) + '</div>' +
        '<div class="ks-card-meta">' + esc(jenis || 'Desa Pelemwatu') + '</div>' +
        '<button class="ks-card-detail-btn" type="button">Lihat Detail</button>' +
      '</div>';

    // Simpan data ke card untuk modal
    div.dataset.name   = name;
    div.dataset.cat    = cat;
    div.dataset.jenis  = jenis;
    div.dataset.deskr  = deskr;
    div.dataset.lokasi = lokasi;
    div.dataset.wa     = wa;
    div.dataset.jam    = jam;
    div.dataset.img    = imgSrc;

    div.querySelector('.ks-card-detail-btn').addEventListener('click', function (e) {
      e.stopPropagation();
      openDetail(div.dataset);
    });

    return div;
  }

  /* ── Escape HTML ── */
  function esc(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g,  '&amp;')
      .replace(/</g,  '&lt;')
      .replace(/>/g,  '&gt;')
      .replace(/"/g,  '&quot;');
  }

  /* ── Render semua card ke track ── */
  function renderCards() {
    track.style.transform = 'translateX(0)';
    track.innerHTML = '';
    currentIdx = 0;
    if (btnPrev) btnPrev.style.display = '';
    if (btnNext) btnNext.style.display = '';

    if (!filtered.length) {
      if (emptyState) emptyState.style.display = 'flex';
      if (viewport)   viewport.style.display   = 'none';
      if (btnPrev)    btnPrev.disabled = true;
      if (btnNext)    btnNext.disabled = true;
      return;
    }
    if (emptyState) emptyState.style.display = 'none';
    if (viewport)   viewport.style.display   = '';
    filtered.forEach(function (item) {
      track.appendChild(buildCard(item));
    });
    goTo(0);
  }

  /* ── Tampilkan Error State ── */
  function showErrorState(err) {
    console.error('Data potensi gagal dimuat:', err);
    if (emptyState) emptyState.style.display = 'none';
    if (viewport)   viewport.style.display   = '';
    if (btnPrev)    btnPrev.style.display    = 'none';
    if (btnNext)    btnNext.style.display    = 'none';

    track.style.transform = 'translateX(0)';
    track.innerHTML =
      '<div class="ks-empty ks-error">' +
        '<i class="bi bi-exclamation-triangle" style="font-size:2rem;color:#ef4444;"></i>' +
        '<p>Data potensi gagal dimuat.</p>' +
        '<button type="button" class="ks-retry-btn" id="ksRetryBtn">Coba lagi</button>' +
      '</div>';

    var retryBtn = document.getElementById('ksRetryBtn');
    if (retryBtn) {
      retryBtn.addEventListener('click', function () {
        fetchAll();
      });
    }
  }

  /* ── Terapkan filter kategori + search ── */
  function applyFilter() {
    var q   = searchInput ? searchInput.value.trim().toLowerCase() : '';
    var cat = activeCat.toLowerCase();

    filtered = allItems.filter(function (item) {
      // Filter kategori: cocokkan category.name persis (case-insensitive)
      if (cat) {
        var itemCat = (item.category && item.category.name) ? item.category.name.trim().toLowerCase() : '';
        if (itemCat !== cat) return false;
      }
      // Search: nama, deskripsi, jenis, lokasi
      if (q) {
        var haystack = [
          item.name, item.description, item.jenis,
          item.location, item.address,
          (item.category && item.category.name) || ''
        ].join(' ').toLowerCase();
        if (haystack.indexOf(q) === -1) return false;
      }
      return true;
    });

    // Tampilkan / sembunyikan tombol clear
    if (searchClear) {
      searchClear.style.display = (searchInput && searchInput.value) ? '' : 'none';
    }

    renderCards();
  }

  /* ── Fetch dari API ── */
  function fetchAll() {
    if (emptyState) emptyState.style.display = 'none';
    if (viewport)   viewport.style.display   = '';
    if (btnPrev)    btnPrev.style.display    = '';
    if (btnNext)    btnNext.style.display    = '';

    // Tampilkan skeleton
    track.style.transform = 'translateX(0)';
    track.innerHTML =
      '<div class="ks-skeleton"></div>' +
      '<div class="ks-skeleton"></div>' +
      '<div class="ks-skeleton"></div>' +
      '<div class="ks-skeleton"></div>';

    fetch(API_BASE + '/potensi?per_page=50')
      .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(function (res) {
        var data = res.data || res;
        // Jika pagination wrapper (array dalam .data)
        if (data && typeof data === 'object' && !Array.isArray(data) && data.data) {
          data = data.data;
        }
        allItems = Array.isArray(data) ? data : [];
        filtered = allItems.slice();
        renderCards();
      })
      .catch(function (err) {
        showErrorState(err);
      });
  }

  /* ── Modal detail ── */
  function openDetail(d) {
    var modal = document.getElementById('detailModal');
    if (!modal) return;

    function set(id, val) {
      var el = document.getElementById(id);
      if (el) el.textContent = val || '—';
    }

    set('dm-nama',         d.name);
    set('dm-cat',          d.cat);
    set('dm-nama-potensi', d.name);
    set('dm-jenis',        (d.jenis && d.jenis !== '-' && d.jenis !== 'null') ? d.jenis : '—');
    set('dm-jam',          (d.jam && d.jam !== '-' && d.jam !== 'null') ? d.jam : '—');
    set('dm-lokasi',       (d.lokasi && d.lokasi !== '-' && d.lokasi !== 'null') ? d.lokasi : '—');
    set('dm-deskripsi',    (d.deskr && d.deskr !== '-' && d.deskr !== 'null') ? d.deskr : '—');

    // Email — tidak ada di public resource, sembunyikan baris jika kosong
    var emailEl     = document.getElementById('dm-email');
    var emailField  = document.getElementById('dm-email-field');
    if (emailEl) emailEl.textContent = '—';
    if (emailField) emailField.style.display = 'none';

    // WhatsApp: tampilkan sebagai link klik
    var waEl = document.getElementById('dm-wa');
    if (waEl) {
      if (d.wa && d.wa !== '-' && d.wa !== '—' && d.wa !== 'null') {
        var num = d.wa.replace(/\D/g, '');
        if (num.startsWith('0')) num = '62' + num.substring(1);
        waEl.innerHTML =
          '<a href="https://wa.me/' + num + '" target="_blank" rel="noopener" ' +
          'style="color:var(--clr-orange);font-weight:600;">' +
          esc(d.wa) + ' <i class="bi bi-whatsapp"></i></a>';
      } else {
        waEl.textContent = '—';
      }
    }

    // Foto
    var fotoEl        = document.getElementById('dm-foto');
    var placeholderEl = document.getElementById('dm-foto-placeholder');
    if (fotoEl && placeholderEl) {
      if (d.img) {
        fotoEl.src          = (window.sanitizeSrc ? window.sanitizeSrc(d.img, '') : d.img);
        fotoEl.style.display = '';
        placeholderEl.style.display = 'none';
      } else {
        fotoEl.style.display        = 'none';
        placeholderEl.style.display = '';
      }
    }

    var bsModal = window.bootstrap && bootstrap.Modal.getOrCreateInstance(modal);
    if (bsModal) bsModal.show();
  }

  /* ── Event listeners ── */
  if (btnPrev) btnPrev.addEventListener('click', function () { goTo(currentIdx - 1); });
  if (btnNext) btnNext.addEventListener('click', function () { goTo(currentIdx + 1); });
  window.addEventListener('resize', function () { goTo(currentIdx); });

  if (searchInput) {
    searchInput.addEventListener('input', applyFilter);
    searchInput.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { searchInput.value = ''; applyFilter(); }
    });
  }
  if (searchClear) {
    searchClear.addEventListener('click', function () {
      if (searchInput) searchInput.value = '';
      applyFilter();
      if (searchInput) searchInput.focus();
    });
  }

  // Filter tag buttons (Event delegation)
  var filterTags = document.getElementById('ksFilterTags');
  if (filterTags) {
    filterTags.addEventListener('click', function (e) {
      var btn = e.target.closest('.ks-tag');
      if (!btn || !filterTags.contains(btn)) return;
      filterTags.querySelectorAll('.ks-tag').forEach(function (b) {
        b.classList.remove('active');
      });
      btn.classList.add('active');
      activeCat = btn.dataset.cat || '';
      applyFilter();
    });
  }

  /* ── Fetch Kategori Dinamis ── */
  function fetchCategories() {
    var tagsWrap = document.getElementById('ksFilterTags');
    if (!tagsWrap) return;

    fetch(API_BASE + '/categories')
      .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
      })
      .then(function (res) {
        var cats = res.data || [];
        if (!Array.isArray(cats) || cats.length === 0) return;

        // Ambil atau buat tombol "Semua"
        var allBtn = tagsWrap.querySelector('.ks-tag[data-cat=""]');
        tagsWrap.innerHTML = '';
        if (allBtn) {
          tagsWrap.appendChild(allBtn);
        } else {
          var defaultAll = document.createElement('button');
          defaultAll.type = 'button';
          defaultAll.className = 'ks-tag active';
          defaultAll.dataset.cat = '';
          defaultAll.textContent = 'Semua';
          tagsWrap.appendChild(defaultAll);
        }

        cats.forEach(function (cat) {
          if (!cat || !cat.name) return;
          var btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'ks-tag' + (activeCat.toLowerCase() === cat.name.trim().toLowerCase() ? ' active' : '');
          btn.dataset.cat = cat.name;
          btn.textContent = cat.name; // Aman dari XSS
          tagsWrap.appendChild(btn);
        });
      })
      .catch(function (err) {
        console.warn('Gagal memuat kategori dinamis:', err);
        // Jika gagal, tombol "Semua" yang ada di HTML tetap berfungsi
      });
  }

  // Touch / drag
  var startX = 0, dragging = false;
  if (track) {
    track.addEventListener('mousedown', function (e) { startX = e.clientX; dragging = true; });
    document.addEventListener('mouseup', function (e) {
      if (!dragging) return; dragging = false;
      var diff = startX - e.clientX;
      if (Math.abs(diff) > 50) goTo(currentIdx + (diff > 0 ? 1 : -1));
    });
    track.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', function (e) {
      var diff = startX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 40) goTo(currentIdx + (diff > 0 ? 1 : -1));
    });
  }

  /* ── Init (Paralel fetch potensi & kategori) ── */
  fetchAll();
  fetchCategories();
}());
