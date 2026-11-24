/**
 * EtalasePelemwatu — Galeri Horizontal Slider
 * Vanilla JS with Pointer Events (touch/mouse drag), stagger text transitions,
 * dot windowing (max 5 dots), lightbox integration, and accessibility support.
 */
(function () {
  'use strict';

  function GaleriSlider(containerEl) {
    this.container = containerEl;
    this.viewport = this.container.querySelector('.gs-viewport');
    this.track = this.container.querySelector('.gs-track');
    this.sidebar = this.container.querySelector('.gs-sidebar');
    this.content = this.container.querySelector('.gs-content');
    this.titleEl = this.container.querySelector('#gsTitle');
    this.descEl = this.container.querySelector('#gsDesc');
    this.btnView = this.container.querySelector('#gsBtnView');
    this.prevBtn = this.container.querySelector('#gsPrev');
    this.nextBtn = this.container.querySelector('#gsNext');
    this.rail = this.container.querySelector('#gsRail');
    this.dotsWindow = this.container.querySelector('#gsDotsWindow');
    this.counterCurrent = this.container.querySelector('#gsCounterCurrent');
    this.counterTotal = this.container.querySelector('#gsCounterTotal');

    this.currentIndex = 0;
    this.items = [];
    this.isTracking = false;
    this.isDragging = false;
    this.startX = 0;
    this.startY = 0;
    this.startTime = 0;
    this.currentDeltaX = 0;
    this.dragJustEnded = false;
    this.trackStartOffset = 0;
    this.isTextAnimating = false;

    this.init();
  }

  GaleriSlider.prototype.init = function () {
    this.collectItems();
    if (this.items.length === 0) return;

    this.renderDots();
    this.bindEvents();
    this.updateUI(false);

    // Initial positioning after layout stabilizes
    var self = this;
    requestAnimationFrame(function () {
      self.recalculate();
    });
  };

  GaleriSlider.prototype.collectItems = function () {
    var cards = Array.from(this.track.querySelectorAll('.gs-card'));
    this.items = cards.map(function (card, idx) {
      return {
        index: idx,
        element: card,
        src: card.dataset.src || '',
        caption: card.dataset.caption || '',
        desc: card.dataset.desc || ''
      };
    });

    if (this.counterTotal) {
      this.counterTotal.textContent = String(this.items.length);
    }
  };

  GaleriSlider.prototype.renderDots = function () {
    if (!this.dotsWindow) return;
    this.dotsWindow.innerHTML = '';

    if (this.items.length <= 1) {
      if (this.rail) this.rail.style.display = 'none';
      if (this.counterCurrent && this.counterCurrent.parentElement) {
        this.counterCurrent.parentElement.style.display = 'none';
      }
      return;
    }

    if (this.rail) this.rail.style.display = '';
    if (this.counterCurrent && this.counterCurrent.parentElement) {
      this.counterCurrent.parentElement.style.display = '';
    }

    var self = this;
    this.items.forEach(function (item, idx) {
      var dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'gs-dot' + (idx === 0 ? ' is-active' : '');
      dot.dataset.index = String(idx);
      dot.setAttribute('aria-label', 'Buka foto kegiatan nomor ' + (idx + 1));
      dot.innerHTML = '<span class="gs-dot-num">' + (idx + 1) + '</span>';

      dot.addEventListener('click', function (e) {
        e.preventDefault();
        self.goTo(idx);
      });

      self.dotsWindow.appendChild(dot);
    });
  };

  GaleriSlider.prototype.updateDotsWindow = function () {
    if (!this.dotsWindow) return;
    var dots = Array.from(this.dotsWindow.querySelectorAll('.gs-dot'));
    var total = dots.length;
    if (total <= 1) return;

    var windowSize = 5;
    var start = 0;
    var end = total;

    if (total > windowSize) {
      start = Math.max(0, Math.min(this.currentIndex - 2, total - windowSize));
      end = start + windowSize;
    }

    var self = this;
    dots.forEach(function (dot, idx) {
      dot.classList.remove('is-active', 'gs-dot--edge', 'gs-dot--hidden');

      if (idx === self.currentIndex) {
        dot.classList.add('is-active');
        dot.setAttribute('aria-current', 'true');
      } else {
        dot.removeAttribute('aria-current');
      }

      if (total > windowSize) {
        if (idx < start || idx >= end) {
          dot.classList.add('gs-dot--hidden');
        } else if ((idx === start && start > 0) || (idx === end - 1 && end < total)) {
          dot.classList.add('gs-dot--edge');
        }
      }
    });

    if (this.counterCurrent) {
      this.counterCurrent.textContent = String(this.currentIndex + 1);
    }
  };

  GaleriSlider.prototype.bindEvents = function () {
    var self = this;

    // Prev & Next Buttons
    if (this.prevBtn) {
      this.prevBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (self.currentIndex > 0) {
          self.goTo(self.currentIndex - 1);
        }
      });
    }

    if (this.nextBtn) {
      this.nextBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (self.currentIndex < self.items.length - 1) {
          self.goTo(self.currentIndex + 1);
        }
      });
    }

    // "Lihat Foto" button -> open lightbox
    if (this.btnView) {
      this.btnView.addEventListener('click', function (e) {
        e.preventDefault();
        self.openLightboxForActive();
      });
    }

    // Card interactions
    this.items.forEach(function (item, idx) {
      item.element.addEventListener('click', function (e) {
        if (self.dragJustEnded) {
          e.preventDefault();
          e.stopPropagation();
          return;
        }

        if (idx === self.currentIndex) {
          self.openLightboxForActive();
        } else {
          self.goTo(idx);
        }
      });
    });

    // Pointer Events for Mouse Drag & Touch Swipe
    if (this.viewport) {
      this.viewport.addEventListener('pointerdown', function (e) {
        self.onPointerDown(e);
      });
      this.viewport.addEventListener('pointermove', function (e) {
        self.onPointerMove(e);
      });
      this.viewport.addEventListener('pointerup', function (e) {
        self.onPointerUp(e);
      });
      this.viewport.addEventListener('pointercancel', function (e) {
        self.onPointerUp(e);
      });
    }

    // Keyboard navigation when container or slider has focus
    this.container.addEventListener('keydown', function (e) {
      // Don't interfere if lightbox is open
      var lb = document.getElementById('lightbox');
      if (lb && lb.style.display !== 'none') return;

      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        if (self.currentIndex > 0) self.goTo(self.currentIndex - 1);
      } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        if (self.currentIndex < self.items.length - 1) self.goTo(self.currentIndex + 1);
      } else if (e.key === 'Home') {
        e.preventDefault();
        self.goTo(0);
      } else if (e.key === 'End') {
        e.preventDefault();
        self.goTo(self.items.length - 1);
      }
    });

    // Horizontal wheel / trackpad scroll
    if (this.viewport) {
      this.viewport.addEventListener('wheel', function (e) {
        if (Math.abs(e.deltaX) > Math.abs(e.deltaY) && Math.abs(e.deltaX) > 15) {
          e.preventDefault();
          if (e.deltaX > 0 && self.currentIndex < self.items.length - 1) {
            self.goTo(self.currentIndex + 1);
          } else if (e.deltaX < 0 && self.currentIndex > 0) {
            self.goTo(self.currentIndex - 1);
          }
        }
      }, { passive: false });
    }

    // Tab switch & Resize handling
    var profilNav = document.querySelector('.profil-nav');
    if (profilNav) {
      profilNav.addEventListener('click', function (e) {
        var btn = e.target.closest('.profil-nav-btn');
        if (btn && btn.dataset.tab === 'tab-galeri') {
          setTimeout(function () {
            self.recalculate();
          }, 60);
        }
      });
    }

    window.addEventListener('resize', function () {
      self.recalculate();
    });

    if (window.ResizeObserver) {
      var ro = new ResizeObserver(function (entries) {
        for (var i = 0; i < entries.length; i++) {
          if (entries[i].contentRect.width > 0) {
            self.recalculate();
          }
        }
      });
      ro.observe(this.container);
    }
  };

  GaleriSlider.prototype.onPointerDown = function (e) {
    if (e.button !== 0) return; // Only primary button
    this.isTracking = true;
    this.isDragging = false;
    this.startX = e.clientX;
    this.startY = e.clientY;
    this.startTime = performance.now();
    this.currentDeltaX = 0;

    var curCard = this.items[this.currentIndex] ? this.items[this.currentIndex].element : null;
    this.trackStartOffset = curCard ? curCard.offsetLeft : 0;
  };

  GaleriSlider.prototype.onPointerMove = function (e) {
    if (!this.isTracking) return;

    var dx = e.clientX - this.startX;
    var dy = e.clientY - this.startY;

    if (!this.isDragging) {
      // If user is scrolling vertically, cancel slider drag
      if (Math.abs(dy) > Math.abs(dx) && Math.abs(dy) > 8) {
        this.isTracking = false;
        return;
      }
      if (Math.abs(dx) > 8) {
        this.isDragging = true;
        try {
          this.viewport.setPointerCapture(e.pointerId);
        } catch (err) {}
        this.track.style.transition = 'none';
      }
    }

    if (this.isDragging) {
      e.preventDefault();

      // Resistance at edges
      if ((this.currentIndex === 0 && dx > 0) ||
          (this.currentIndex === this.items.length - 1 && dx < 0)) {
        this.currentDeltaX = dx * 0.3;
      } else {
        this.currentDeltaX = dx;
      }

      this.track.style.transform = 'translateX(' + (-this.trackStartOffset + this.currentDeltaX) + 'px)';
    }
  };

  GaleriSlider.prototype.onPointerUp = function (e) {
    if (!this.isTracking) return;

    try {
      this.viewport.releasePointerCapture(e.pointerId);
    } catch (err) {}

    this.isTracking = false;
    this.track.style.transition = '';

    if (this.isDragging) {
      var self = this;
      this.dragJustEnded = true;
      setTimeout(function () {
        self.dragJustEnded = false;
      }, 150);

      var duration = performance.now() - this.startTime;
      var velocity = this.currentDeltaX / Math.max(duration, 1);
      var curCard = this.items[this.currentIndex] ? this.items[this.currentIndex].element : null;
      var cardWidth = curCard ? curCard.offsetWidth : 300;
      var threshold = cardWidth * 0.15;

      if (this.currentDeltaX < -threshold || velocity < -0.35) {
        if (this.currentIndex < this.items.length - 1) {
          this.goTo(this.currentIndex + 1);
        } else {
          this.recalculate();
        }
      } else if (this.currentDeltaX > threshold || velocity > 0.35) {
        if (this.currentIndex > 0) {
          this.goTo(this.currentIndex - 1);
        } else {
          this.recalculate();
        }
      } else {
        this.recalculate();
      }
    }

    this.isDragging = false;
  };

  GaleriSlider.prototype.goTo = function (index) {
    if (index < 0 || index >= this.items.length) return;
    if (index === this.currentIndex) {
      this.recalculate();
      return;
    }

    this.currentIndex = index;
    this.updateUI(true);
  };

  GaleriSlider.prototype.updateUI = function (animateText) {
    var curItem = this.items[this.currentIndex];
    if (!curItem) return;

    // Update active card class
    var self = this;
    this.items.forEach(function (item, idx) {
      if (idx === self.currentIndex) {
        item.element.classList.add('is-active');
        item.element.setAttribute('aria-current', 'true');
      } else {
        item.element.classList.remove('is-active');
        item.element.removeAttribute('aria-current');
      }
    });

    // Move track
    this.recalculate();

    // Update dots & counter
    this.updateDotsWindow();

    // Update prev/next buttons
    if (this.prevBtn) {
      this.prevBtn.disabled = this.currentIndex === 0;
      this.prevBtn.setAttribute('aria-disabled', String(this.currentIndex === 0));
    }
    if (this.nextBtn) {
      this.nextBtn.disabled = this.currentIndex === this.items.length - 1;
      this.nextBtn.setAttribute('aria-disabled', String(this.currentIndex === this.items.length - 1));
    }

    // Update text
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!animateText || prefersReducedMotion) {
      this.applyText(curItem);
    } else {
      this.animateTextChange(curItem);
    }
  };

  GaleriSlider.prototype.applyText = function (item) {
    if (this.titleEl) {
      this.titleEl.textContent = item.caption || '';
    }
    if (this.descEl) {
      this.descEl.textContent = item.desc || 'Dokumentasi kegiatan masyarakat dan aktivitas di Desa Pelemwatu.';
    }
    if (this.btnView) {
      this.btnView.setAttribute('aria-label', 'Lihat foto: ' + (item.caption || ''));
    }
  };

  GaleriSlider.prototype.animateTextChange = function (item) {
    if (!this.content || this.isTextAnimating) {
      this.applyText(item);
      return;
    }

    this.isTextAnimating = true;
    var self = this;

    // Phase 1: exit fade & translateY
    this.content.classList.remove('gs-text-enter');
    this.content.classList.add('gs-text-exit');

    setTimeout(function () {
      self.applyText(item);
      self.content.classList.remove('gs-text-exit');
      self.content.classList.add('gs-text-enter');

      setTimeout(function () {
        self.content.classList.remove('gs-text-enter');
        self.isTextAnimating = false;
      }, 350);
    }, 200);
  };

  GaleriSlider.prototype.recalculate = function () {
    if (!this.track || !this.items.length) return;
    var curCard = this.items[this.currentIndex] ? this.items[this.currentIndex].element : null;
    if (!curCard) return;

    var offset = curCard.offsetLeft;
    this.track.style.transform = 'translateX(-' + offset + 'px)';
  };

  GaleriSlider.prototype.openLightboxForActive = function () {
    if (this.dragJustEnded) return;

    var data = this.items.map(function (item) {
      return {
        src: item.src,
        caption: item.caption,
        desc: item.desc
      };
    });

    if (typeof window.openLightbox === 'function') {
      window.openLightbox(data, this.currentIndex);
    }
  };

  GaleriSlider.prototype.updateData = function (newData) {
    if (!Array.isArray(newData) || !this.track) return;

    if (newData.length === 0) {
      this.container.innerHTML =
        '<div class="desa-info-card py-5 text-center text-muted gs-empty">' +
          '<i class="bi bi-images me-2" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>' +
          '<span>Belum ada foto kegiatan.</span>' +
        '</div>';
      return;
    }

    this.track.innerHTML = '';
    var self = this;
    newData.forEach(function (g, idx) {
      var safeSrc = window.sanitizeSrc ? window.sanitizeSrc(g.image_path) : g.image_path;
      var card = document.createElement('div');
      card.className = 'gs-card' + (idx === 0 ? ' is-active' : '');
      card.dataset.index = String(idx);
      card.dataset.src = safeSrc;
      card.dataset.caption = g.caption || '';
      card.dataset.desc = g.description || '';
      card.setAttribute('role', 'group');
      card.setAttribute('aria-roledescription', 'slide');
      card.setAttribute('aria-label', (idx + 1) + ' dari ' + newData.length + ': ' + (g.caption || ''));

      var img = document.createElement('img');
      img.src = safeSrc;
      img.alt = g.caption || '';
      img.loading = idx < 2 ? 'eager' : 'lazy';
      img.draggable = false;

      var inner = document.createElement('div');
      inner.className = 'gs-card-inner';
      inner.appendChild(img);

      var overlay = document.createElement('div');
      overlay.className = 'gs-card-overlay';
      overlay.innerHTML = '<span class="gs-card-zoom-badge"><i class="bi bi-zoom-in me-1"></i>Perbesar</span>';
      inner.appendChild(overlay);

      card.appendChild(inner);
      self.track.appendChild(card);
    });

    this.currentIndex = 0;
    this.collectItems();
    this.renderDots();
    this.bindEvents();
    this.updateUI(false);
  };

  // Auto-init on DOMContentLoaded
  function init() {
    var container = document.getElementById('gsContainer');
    if (container) {
      window.galeriSlider = new GaleriSlider(container);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  window.initGaleriSlider = init;
  window.updateGaleriSlider = function (data) {
    if (window.galeriSlider) {
      window.galeriSlider.updateData(data);
    } else {
      init();
    }
  };
})();
