<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftarkan Potensi Lokal — EtalasePelemwatu</title>
  <meta name="description" content="Formulir pendaftaran potensi lokal Desa Pelemwatu. Daftarkan usaha, produk, jasa, atau hunian Anda.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .form-page-content { padding-top: 80px; }
    .form-card { background:var(--white); border-radius:24px; box-shadow:0 8px 40px rgba(9,74,116,.1),0 2px 8px rgba(0,0,0,.04); max-width:680px; margin:0 auto; overflow:hidden; }
    .form-card-header { display:none; }
    .form-card-body { padding:2rem 2rem 1.75rem; }
    @media (max-width:576px){ .form-card-body{ padding:1.5rem 1.25rem 1.25rem; } }
    #stepIndicator { display:block !important; margin-bottom:1.5rem; }
    .step-progress-label { font-size:.8rem; font-weight:600; color:var(--text-mid,#6b7280); margin-bottom:.45rem; }
    .step-progress-track { height:4px; background:rgba(9,74,116,.1); border-radius:999px; overflow:hidden; }
    .step-progress-fill  { height:100%; background:var(--clr-orange,#f2630f); border-radius:999px; transition:width .4s cubic-bezier(.4,0,.2,1); }
    .step-heading    { font-size:1.4rem; font-weight:800; color:var(--clr-blue,#094a74); margin-bottom:.25rem; line-height:1.25; }
    .step-subheading { font-size:.88rem; color:var(--text-mid,#6b7280); margin-bottom:1.25rem; }
    .form-section-title { font-size:.78rem; font-weight:700; color:var(--text-mid,#6b7280); text-transform:uppercase; letter-spacing:.07em; margin:1.25rem 0 .6rem; display:flex; align-items:center; gap:.4rem; }
    .form-section-title:first-child { margin-top:0; }
    .form-control { border-radius:12px !important; border:1.5px solid var(--border,#d1d9e0) !important; background:#fafcff !important; font-size:.9rem !important; padding:.6rem .9rem !important; transition:border-color .2s,box-shadow .2s !important; }
    .form-control:focus { border-color:var(--clr-orange,#f2630f) !important; box-shadow:0 0 0 3px rgba(242,99,15,.12) !important; background:var(--white) !important; }
    .form-label { font-size:.82rem !important; font-weight:600 !important; color:var(--clr-blue,#094a74) !important; margin-bottom:.35rem !important; }
    .kat-pill { display:inline-flex; align-items:center; gap:.4rem; padding:.45rem 1rem; border-radius:999px; border:1.5px solid var(--border,#d1d9e0); background:var(--white); cursor:pointer; font-size:.88rem; font-weight:500; color:var(--clr-blue,#094a74); transition:border-color .18s,background .18s,box-shadow .18s; user-select:none; }
    .kat-pill:hover { border-color:var(--clr-orange,#f2630f); background:rgba(242,99,15,.04); }
    .kat-pill.selected { border-color:var(--clr-orange,#f2630f); background:rgba(242,99,15,.08); box-shadow:0 0 0 2px rgba(242,99,15,.15); font-weight:600; }
    .kat-pill .kat-check { display:none; color:var(--clr-orange,#f2630f); }
    .kat-pill.selected .kat-check { display:inline; }
    .kat-pill input[type="radio"] { display:none; }
    .kat-pills-wrap { display:flex; flex-wrap:wrap; gap:.5rem; }
    .kategori-card { display:none !important; }
    .upload-dropzone { border:2px dashed var(--border,#c9d6e3); border-radius:14px; padding:2rem 1.5rem; text-align:center; cursor:pointer; transition:border-color .2s,background .2s; background:#fafcff; }
    .upload-dropzone:hover, .upload-dropzone.dragover { border-color:var(--clr-orange,#f2630f); background:rgba(242,99,15,.04); }
    .upload-dropzone .dz-title { font-size:.95rem; font-weight:700; color:var(--clr-blue,#094a74); margin:.5rem 0 .3rem; }
    .upload-dropzone .dz-hint  { font-size:.78rem; color:var(--text-light,#9ca3af); margin-bottom:.9rem; }
    .upload-dropzone .dz-btn   { display:inline-block; padding:.38rem 1.2rem; border-radius:999px; border:1.5px solid var(--clr-blue,#094a74); background:var(--white); color:var(--clr-blue,#094a74); font-size:.84rem; font-weight:600; cursor:pointer; }
    .upload-area { display:none !important; }
    #photoPreview { display:none; gap:.75rem; flex-wrap:wrap; margin-top:.75rem; }
    #photoPreview img { width:80px; height:80px; object-fit:cover; border-radius:8px; border:2px solid var(--border); }
    .step-btn-wrap { display:flex; justify-content:flex-end; gap:.65rem; margin-top:1.5rem; }
    .btn-step-back { padding:.55rem 1.4rem; border-radius:14px; border:1.5px solid #d1d9e0; background:#f3f4f6; color:#6b7280; font-size:.9rem; font-weight:600; cursor:pointer; }
    .btn-step-back:hover { background:#e5e7eb; }
    .btn-step-next { padding:.55rem 1.6rem; border-radius:14px; border:none; background:var(--clr-orange,#f2630f); color:var(--white); font-size:.9rem; font-weight:700; cursor:pointer; box-shadow:0 2px 10px rgba(242,99,15,.22); }
    .btn-step-next:hover { background:#d9560c; }
    .info-sidebar-card { background:var(--white);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;margin-bottom:1.25rem;box-shadow:var(--shadow-sm); }
    .info-sidebar-card-header { background:var(--clr-blue);padding:.9rem 1.25rem;color:var(--white);font-weight:700;font-size:.9rem;display:flex;align-items:center;gap:.5rem; }
    .info-sidebar-card-body { padding:1.25rem; }
    .step-guide-item { display:flex;gap:.75rem;margin-bottom:.9rem;align-items:flex-start; }
    .step-guide-item:last-child { margin-bottom:0; }
    .step-guide-num { width:26px;height:26px;background:var(--clr-orange);color:var(--white);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.78rem;font-weight:700;flex-shrink:0; }
    .step-guide-text { font-size:.88rem;color:var(--clr-blue);padding-top:.2rem; }
    .inline-err { display:none;background:rgba(220,53,69,.1);border:1px solid rgba(220,53,69,.3);border-radius:8px;padding:.65rem 1rem;color:#842029;font-size:.85rem;align-items:center;gap:.5rem;margin-top:1rem; }
  </style>
</head>
<body class="form-page">

<!-- NAVBAR -->
<header id="siteHeader">
  <nav class="navbar navbar-expand-lg" id="mainNavbar">
    <div class="container">
      <a class="navbar-brand-wrap" href="index.html">
        <img class="brand-logo" src="assets/img/logo-desa.png" alt="Logo Desa Pelemwatu" data-site-logo>
        <div class="brand-text-wrap">
          <span class="brand-name">EtalasePelemwatu</span>
          <span class="brand-sub">Desa Pelemwatu</span>
        </div>
      </a>
      <button class="navbar-toggler ms-auto me-2" type="button"
              data-bs-toggle="collapse" data-bs-target="#navMenuForm">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenuForm">
        <ul class="navbar-nav ms-auto gap-1">
          <li class="nav-item"><a class="nav-link" href="index.html#hero-desa">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="index.html#profil-desa">Profil Desa</a></li>
          <li class="nav-item"><a class="nav-link" href="index.html#pengenalan-etalase">Tentang Etalase</a></li>
          <li class="nav-item"><a class="nav-link" href="index.html#katalog">Katalog Potensi</a></li>
          <li class="nav-item"><a class="nav-link" href="index.html#layanan">Layanan</a></li>
          <li class="nav-item"><a class="nav-link" href="index.html#kontak-section">Kontak</a></li>
        </ul>
        <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0 ms-lg-3 flex-wrap">
          <a href="daftar-potensi.html" class="nav-link btn-nav-daftar">Daftarkan Potensimu</a>
          <div id="navAdminWrap">
            <button type="button" class="btn-nav-admin" data-bs-toggle="modal" data-bs-target="#modalLoginAdmin">
              Login Admin
            </button>
          </div>
        </div>
      </div>
    </div>
  </nav>
</header>

<div class="form-page-content">
  <!-- PAGE HEADER -->
  <div class="form-page-header">
    <div class="container" style="position:relative;z-index:2;">
      <div class="form-breadcrumb">
        <a href="index.html"><i class="bi bi-house me-1"></i>Beranda</a>
        <span><i class="bi bi-chevron-right" style="font-size:.7rem;"></i></span>
        <span style="color:var(--clr-yellow);">Daftarkan Potensi</span>
      </div>
      <h1><i class="bi bi-pencil-square me-2" style="color:var(--clr-yellow);"></i>Daftarkan Potensi Lokal</h1>
      <p>Isi formulir berikut untuk mendaftarkan usaha, produk, jasa, hunian, atau barang milik Anda ke dalam katalog EtalasePelemwatu. Pendaftaran bersifat gratis.</p>
    </div>
  </div>

  <!-- MAIN -->
  <div class="container py-5">
    <div class="row g-4">

      <!-- LEFT: FORM -->
      <div class="col-lg-8">

        <!-- Step Indicator -->
        <!-- Step Indicator (progress bar) -->
        <div id="stepIndicator" style="max-width:680px;margin:0 auto 1.5rem;">
          <div class="step-progress-label" id="stepProgressLabel">Langkah 1/3</div>
          <div class="step-progress-track">
            <div class="step-progress-fill" id="stepProgressFill" style="width:33.33%;"></div>
          </div>
          <!-- Hidden items lama (dibutuhkan JS updateStepUI) -->
          <div id="stepItem1" style="display:none;"><div id="stepCircle1"></div></div>
          <div id="stepItem2" style="display:none;"><div id="stepCircle2"></div></div>
          <div id="stepItem3" style="display:none;"><div id="stepCircle3"></div></div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
          <div class="form-card-header">
            <h4><i class="bi bi-clipboard-text me-2"></i>Formulir Pendaftaran Potensi</h4>
          </div>
          <div class="form-card-body">
            <form id="pendaftaranForm" novalidate>

              <!-- STEP 1 -->
              <div class="form-step-pane active" id="step-pane-1">
                <h2 class="step-heading">Identitas Pendaftar</h2>
                <p class="step-subheading">Isi data diri Anda dengan benar agar tim kami dapat menghubungi Anda.</p>
                <div class="form-section-title"><i class="bi bi-person-circle"></i> Identitas Pendaftar</div>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Nama Lengkap <span class="required-mark">*</span></label>
                    <input type="text" class="form-control" id="f-nama" placeholder="Masukkan nama lengkap Anda" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">NIK</label>
                    <input type="text" class="form-control" id="f-nik" placeholder="16 digit NIK (opsional)" maxlength="16">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Nomor Telepon <span class="required-mark">*</span></label>
                    <input type="tel" class="form-control" id="f-telp" placeholder="Contoh: 08123456789" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="f-email" placeholder="email@contoh.com (opsional)">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Alamat Rumah <span class="required-mark">*</span></label>
                    <textarea class="form-control" id="f-alamat" rows="2" placeholder="Alamat lengkap di Desa Pelemwatu" required></textarea>
                  </div>
                </div>

                <div class="form-section-title"><i class="bi bi-tag"></i> Kategori Potensi</div>
                <p style="font-size:.88rem;color:var(--text-mid);" class="mb-3">Pilih kategori yang paling sesuai. <span class="required-mark">*</span></p>
                <!-- Pills kategori (nilai radio tetap ada di DOM tersembunyi untuk kompatibilitas JS) -->
                <div class="kat-pills-wrap">
                  <label class="kat-pill" id="kat-usaha">
                    <input type="radio" name="kategori" value="usaha">
                    <i class="bi bi-check-circle-fill kat-check"></i>
                    <i class="bi bi-shop"></i> Usaha &amp; Produk
                  </label>
                  <label class="kat-pill" id="kat-jasa">
                    <input type="radio" name="kategori" value="jasa">
                    <i class="bi bi-check-circle-fill kat-check"></i>
                    <i class="bi bi-tools"></i> Jasa
                  </label>
                  <label class="kat-pill" id="kat-hunian">
                    <input type="radio" name="kategori" value="hunian">
                    <i class="bi bi-check-circle-fill kat-check"></i>
                    <i class="bi bi-house"></i> Hunian
                  </label>
                  <label class="kat-pill" id="kat-barang">
                    <input type="radio" name="kategori" value="barang">
                    <i class="bi bi-check-circle-fill kat-check"></i>
                    <i class="bi bi-bag"></i> Barang Warga
                  </label>
                </div>

                <div class="inline-err" id="err1">
                  <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                  <span id="err1msg">Periksa kembali data Anda.</span>
                </div>
                <div class="step-btn-wrap">
                  <button type="button" class="btn-step-next" id="btnNext1">
                    Lanjutkan <i class="bi bi-arrow-right ms-1"></i>
                  </button>
                </div>
              </div><!-- /step-pane-1 -->

              <!-- STEP 2 -->
              <div class="form-step-pane" id="step-pane-2">
                <h2 class="step-heading">Detail &amp; Deskripsi</h2>
                <p class="step-subheading">Lengkapi informasi potensi dan unggah foto dokumentasi (opsional).</p>
                <div class="form-section-title"><i class="bi bi-info-circle"></i> Informasi Potensi</div>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Nama Potensi / Usaha <span class="required-mark">*</span></label>
                    <input type="text" class="form-control" id="f-nama-potensi" placeholder="Nama usaha, produk, atau jasa Anda" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Jenis / Subkategori</label>
                    <input type="text" class="form-control" id="f-jenis" placeholder="Contoh: Laundry kiloan, Kost putra">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Deskripsi <span class="required-mark">*</span></label>
                    <textarea class="form-control" id="f-deskripsi" rows="4" placeholder="Jelaskan potensi Anda secara singkat dan jelas..." required></textarea>
                    <div class="form-text">Minimal 20 karakter.</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Alamat / Lokasi Potensi</label>
                    <input type="text" class="form-control" id="f-lokasi" placeholder="Alamat usaha jika berbeda dari rumah">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Jam Operasional</label>
                    <input type="text" class="form-control" id="f-jam" placeholder="Contoh: Senin–Sabtu, 08.00–20.00 WIB">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Kontak Usaha</label>
                    <input type="tel" class="form-control" id="f-kontak-usaha" placeholder="Nomor yang dapat dihubungi calon pelanggan">
                  </div>
                </div>

                <div class="form-section-title mt-3"><i class="bi bi-camera"></i> Foto Dokumentasi</div>
                <p style="font-size:.88rem;color:var(--text-mid);" class="mb-3">Upload foto yang relevan (opsional, maks. 3 foto).</p>
                <!-- Dropzone -->
                <div class="upload-dropzone" id="uploadArea">
                  <input type="file" id="fileInput" accept="image/jpeg,image/jpg,image/png,image/webp" multiple style="display:none;">
                  <i class="bi bi-image" style="font-size:2rem;color:var(--clr-blue);opacity:.45;display:block;margin-bottom:.5rem;"></i>
                  <div class="dz-title">Pilih file atau seret ke sini</div>
                  <div class="dz-hint">JPG, PNG, WebP, hingga 10 MB per foto, maks. 3 foto</div>
                  <button type="button" class="dz-btn" id="dzBtnTelusuri">Telusuri file</button>
                </div>
                <div id="photoPreview"></div>

                <div class="inline-err" id="err2">
                  <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                  <span id="err2msg">Periksa kembali data Anda.</span>
                </div>
                <div class="step-btn-wrap">
                  <button type="button" class="btn-step-back" id="btnPrev2">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                  </button>
                  <button type="button" class="btn-step-next" id="btnNext2">
                    Lanjutkan <i class="bi bi-arrow-right ms-1"></i>
                  </button>
                </div>
              </div><!-- /step-pane-2 -->

              <!-- STEP 3 -->
              <div class="form-step-pane" id="step-pane-3">
                <h2 class="step-heading">Konfirmasi</h2>
                <p class="step-subheading">Periksa kembali data Anda sebelum mengirim pendaftaran.</p>
                <div class="form-section-title"><i class="bi bi-check2-circle"></i> Konfirmasi Data</div>
                <div class="preview-card mb-4">
                  <div class="row g-3">
                    <div class="col-md-6"><div class="preview-label">Nama Pendaftar</div><div class="preview-value" id="prev-nama">—</div></div>
                    <div class="col-md-6"><div class="preview-label">Nomor Telepon</div><div class="preview-value" id="prev-telp">—</div></div>
                    <div class="col-md-6"><div class="preview-label">Kategori Potensi</div><div class="preview-value" id="prev-kategori">—</div></div>
                    <div class="col-md-6"><div class="preview-label">Nama Potensi / Usaha</div><div class="preview-value" id="prev-potensi">—</div></div>
                    <div class="col-12"><div class="preview-label">Deskripsi</div><div class="preview-value" id="prev-deskripsi" style="font-size:.9rem;font-weight:400;color:var(--text-mid);">—</div></div>
                    <div class="col-md-6"><div class="preview-label">Lokasi</div><div class="preview-value" id="prev-lokasi">—</div></div>
                    <div class="col-md-6"><div class="preview-label">Jam Operasional</div><div class="preview-value" id="prev-jam">—</div></div>
                  </div>
                </div>

                <div class="alert mb-3" style="background:rgba(9,74,116,.07);border-left:3px solid var(--clr-blue);border-radius:8px;font-size:.85rem;color:var(--clr-blue);">
                  <i class="bi bi-info-circle me-1"></i>
                  Setelah dikirim, pendaftaran Anda akan ditinjau oleh pengelola EtalasePelemwatu. Anda akan dihubungi melalui nomor telepon yang didaftarkan.
                </div>

                <div class="form-check mb-4">
                  <input class="form-check-input" type="checkbox" id="agreeCheck">
                  <label class="form-check-label" for="agreeCheck" style="font-size:.9rem;color:var(--text-mid);">
                    Saya menyatakan bahwa data yang saya isi adalah <strong>benar</strong> dan merupakan
                    potensi lokal yang nyata di <strong>Desa Pelemwatu</strong>.
                  </label>
                </div>

                <div class="inline-err" id="err3">
                  <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                  <span id="err3msg">Periksa kembali data Anda.</span>
                </div>
                <div class="step-btn-wrap">
                  <button type="button" class="btn-step-back" id="btnPrev3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                  </button>
                  <button type="button" class="btn-step-next" id="btnSubmit">
                    <i class="bi bi-send me-1"></i> Kirim Pendaftaran
                  </button>
                </div>
              </div><!-- /step-pane-3 -->

            </form>

            <!-- SUCCESS STATE -->
            <div id="successState" style="display:none;">
              <div class="success-state">
                <div class="success-icon">
                  <i class="bi bi-check-circle-fill" style="color:#28a745;font-size:2.8rem;"></i>
                </div>
                <h3 style="color:var(--clr-blue);">Pendaftaran Berhasil Dikirim!</h3>
                <p style="color:var(--text-mid);max-width:440px;margin:0 auto 1rem;">
                  Terima kasih telah mendaftarkan potensi lokal Anda. Tim pengelola EtalasePelemwatu akan
                  meninjau pendaftaran Anda dan menghubungi Anda melalui nomor telepon yang telah didaftarkan.
                </p>
                <p style="font-size:.88rem;color:var(--text-light);margin-bottom:2rem;">
                  Nomor referensi: <strong id="refNumber" style="color:var(--clr-orange);">#EP-0000</strong>
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                  <a href="index.html" class="btn-riviera-custom">
                    <i class="bi bi-house"></i> Kembali ke Beranda
                  </a>
                  <button type="button" class="btn-outline-custom" id="btnDaftarLagi">
                    <i class="bi bi-plus-circle"></i> Daftarkan Potensi Lain
                  </button>
                </div>
              </div>
            </div>

          </div><!-- /form-card-body -->
        </div><!-- /form-card -->
      </div><!-- /col-lg-8 -->

      <!-- RIGHT: SIDEBAR -->
      <div class="col-lg-4">
        <div style="position:sticky;top:100px;">
          <div class="info-sidebar-card">
            <div class="info-sidebar-card-header"><i class="bi bi-journal-text"></i> Panduan Pendaftaran</div>
            <div class="info-sidebar-card-body">
              <div class="step-guide-item"><div class="step-guide-num">1</div><div class="step-guide-text">Isi data identitas pendaftar dengan benar.</div></div>
              <div class="step-guide-item"><div class="step-guide-num">2</div><div class="step-guide-text">Pilih kategori potensi yang sesuai.</div></div>
              <div class="step-guide-item"><div class="step-guide-num">3</div><div class="step-guide-text">Lengkapi informasi dan deskripsi potensi.</div></div>
              <div class="step-guide-item"><div class="step-guide-num">4</div><div class="step-guide-text">Upload foto dokumentasi (opsional).</div></div>
              <div class="step-guide-item"><div class="step-guide-num">5</div><div class="step-guide-text">Konfirmasi data dan kirim pendaftaran.</div></div>
              <div class="step-guide-item"><div class="step-guide-num">6</div><div class="step-guide-text">Tunggu verifikasi dari pengelola EtalasePelemwatu.</div></div>
            </div>
          </div>
          <div class="info-sidebar-card">
            <div class="info-sidebar-card-header"><i class="bi bi-shield-check"></i> Ketentuan Pendaftaran</div>
            <div class="info-sidebar-card-body">
              <ul style="list-style:none;padding:0;margin:0;">
                <li style="display:flex;gap:.6rem;margin-bottom:.75rem;font-size:.87rem;color:var(--clr-blue);">
                  <i class="bi bi-check-circle-fill mt-1" style="color:#28a745;flex-shrink:0;"></i>
                  <span>Hanya untuk warga Desa Pelemwatu.</span>
                </li>
                <li style="display:flex;gap:.6rem;margin-bottom:.75rem;font-size:.87rem;color:var(--clr-blue);">
                  <i class="bi bi-check-circle-fill mt-1" style="color:#28a745;flex-shrink:0;"></i>
                  <span>Data harus valid dan dapat diverifikasi.</span>
                </li>
                <li style="display:flex;gap:.6rem;margin-bottom:.75rem;font-size:.87rem;color:var(--clr-blue);">
                  <i class="bi bi-check-circle-fill mt-1" style="color:#28a745;flex-shrink:0;"></i>
                  <span>Foto harus sesuai dengan potensi yang didaftarkan.</span>
                </li>
                <li style="display:flex;gap:.6rem;font-size:.87rem;color:var(--clr-blue);">
                  <i class="bi bi-check-circle-fill mt-1" style="color:#28a745;flex-shrink:0;"></i>
                  <span>Pendaftaran bersifat <strong>gratis</strong>.</span>
                </li>
              </ul>
            </div>
          </div>
          <div class="info-sidebar-card">
            <div class="info-sidebar-card-header"><i class="bi bi-question-circle"></i> Butuh Bantuan?</div>
            <div class="info-sidebar-card-body">
              <p style="font-size:.87rem;color:var(--clr-blue);margin-bottom:1rem;">
                Hubungi pengelola EtalasePelemwatu atau datangi kantor Desa Pelemwatu untuk bantuan pengisian formulir.
              </p>
              <div style="background:var(--clr-bg);border-radius:8px;padding:.85rem;font-size:.84rem;color:var(--clr-blue);margin-bottom:.6rem;">
                <i class="bi bi-geo-alt me-1" style="color:var(--clr-orange);"></i>
                Jl. Raya Pelemwatu No. 128, Kec. Menganti, Kab. Gresik
              </div>
              <div style="background:var(--clr-bg);border-radius:8px;padding:.85rem;font-size:.84rem;color:var(--clr-blue);margin-bottom:.6rem;">
                <i class="bi bi-clock me-1" style="color:var(--clr-orange);"></i>
                Senin – Jumat, 08.00 – 16.00 WIB
              </div>
              <a href="https://wa.me/6282245730256?text=Halo%2C%20saya%20butuh%20bantuan%20pengisian%20formulir%20EtalasePelemwatu."
                 target="_blank" rel="noopener noreferrer"
                 class="w-100 justify-content-center mt-3"
                 style="display:flex;align-items:center;gap:.5rem;background:#25D366;color:#fff;
                        padding:.6rem 1rem;border-radius:8px;font-size:.85rem;font-weight:600;
                        text-decoration:none;border:none;">
                <i class="bi bi-whatsapp"></i> Chat WhatsApp
              </a>
              <a href="index.html#kontak-section" class="btn-outline-custom w-100 justify-content-center mt-2" style="font-size:.85rem;">
                <i class="bi bi-envelope"></i> Lihat Info Kontak
              </a>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /row -->
  </div><!-- /container -->
</div><!-- /form-page-content -->

<!-- FOOTER -->
<footer id="main-footer" class="py-4">
  <div class="container">
    <div class="row g-3 align-items-center">
      <div class="col-md-6">
        <div class="footer-brand" style="font-size:1rem;">
          <i class="bi bi-shop-window me-2" style="color:var(--clr-yellow);"></i>EtalasePelemwatu
        </div>
        <p class="footer-desc mt-1" style="font-size:.8rem;">
          Platform Digital Potensi Lokal Desa Pelemwatu, Kec. Menganti, Kab. Gresik, Jawa Timur.
        </p>
      </div>
      <div class="col-md-6 text-md-end">
        <ul class="footer-links d-flex gap-3 justify-content-md-end flex-wrap" style="margin:0;">
          <li><a href="index.html">Beranda</a></li>
          <li><a href="index.html#katalog">Katalog</a></li>
          <li><a href="index.html#kontak-section">Kontak</a></li>
          <li><a href="admin.html">Admin</a></li>
        </ul>
      </div>
    </div>

    <hr class="footer-divider">
    <div class="footer-bottom">&copy; 2026 EtalasePelemwatu. Platform Digital Desa Pelemwatu.</div>
  </div>
</footer>

<div id="toastContainer"></div>
<button id="backToTop" title="Kembali ke atas"><i class="bi bi-arrow-up"></i></button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
'use strict';

/* ── Inline Error Helper ── */
function showErr(num, msg) {
  var el = document.getElementById('err' + num);
  var m  = document.getElementById('err' + num + 'msg');
  if (m) m.textContent = msg;
  if (el) { el.style.display = 'flex'; setTimeout(function(){ el.style.display='none'; }, 4000); }
}

/* ── Step UI ── */
var currentStep = 1;

function updateStepUI(step) {
  document.querySelectorAll('.form-step-pane').forEach(function(p){ p.classList.remove('active'); });
  var pane = document.getElementById('step-pane-' + step);
  if (pane) pane.classList.add('active');
  // Update progress bar
  var lbl  = document.getElementById('stepProgressLabel');
  var fill = document.getElementById('stepProgressFill');
  if (lbl)  lbl.textContent = 'Langkah ' + step + '/3';
  if (fill) fill.style.width = (step / 3 * 100).toFixed(2) + '%';
  // Legacy hidden items (still in DOM, no-op)
  for (var i = 1; i <= 3; i++) {
    var item   = document.getElementById('stepItem'   + i);
    var circle = document.getElementById('stepCircle' + i);
    if (!item || !circle) continue;
    item.classList.remove('active', 'done');
    if (i < step)        { item.classList.add('done');   circle.innerHTML = '<i class="bi bi-check2"></i>'; }
    else if (i === step) { item.classList.add('active'); circle.textContent = i; }
    else                 { circle.textContent = i; }
  }
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function goToStep(step) {
  currentStep = step;
  document.getElementById('successState').style.display    = 'none';
  document.getElementById('pendaftaranForm').style.display = '';
  document.getElementById('stepIndicator').style.display   = 'block';
  updateStepUI(step);
  if (step === 3) fillPreview();
}

/* ── Validation ── */
function validateStep1() {
  var nama   = document.getElementById('f-nama').value.trim();
  var telp   = document.getElementById('f-telp').value.trim();
  var alamat = document.getElementById('f-alamat').value.trim();
  var kat    = document.querySelector('input[name="kategori"]:checked');
  if (!nama)   { showErr(1, 'Nama lengkap wajib diisi.');      document.getElementById('f-nama').focus();   return false; }
  if (!telp)   { showErr(1, 'Nomor telepon wajib diisi.');     document.getElementById('f-telp').focus();   return false; }
  if (!alamat) { showErr(1, 'Alamat rumah wajib diisi.');      document.getElementById('f-alamat').focus(); return false; }
  if (!kat)    { showErr(1, 'Silakan pilih kategori potensi.');                                               return false; }
  return true;
}
function validateStep2() {
  var namaP = document.getElementById('f-nama-potensi').value.trim();
  var desk  = document.getElementById('f-deskripsi').value.trim();
  if (!namaP)         { showErr(2, 'Nama potensi/usaha wajib diisi.'); document.getElementById('f-nama-potensi').focus(); return false; }
  if (!desk)          { showErr(2, 'Deskripsi wajib diisi.');          document.getElementById('f-deskripsi').focus();    return false; }
  if (desk.length < 20) { showErr(2, 'Deskripsi minimal 20 karakter.');  document.getElementById('f-deskripsi').focus(); return false; }
  return true;
}

/* ── Preview ── */
function fillPreview() {
  var g = function(id){ var el=document.getElementById(id); return el ? el.value.trim() : ''; };
  var kat    = document.querySelector('input[name="kategori"]:checked');
  var katMap = { usaha: 'Usaha & Produk', jasa: 'Jasa', hunian: 'Hunian', barang: 'Barang Warga' };
  var katLbl = kat ? (katMap[kat.value] || kat.value) : '—';
  document.getElementById('prev-nama').textContent      = g('f-nama') || '—';
  document.getElementById('prev-telp').textContent      = g('f-telp') || '—';
  document.getElementById('prev-kategori').textContent  = katLbl;
  document.getElementById('prev-potensi').textContent   = g('f-nama-potensi') || '—';
  document.getElementById('prev-deskripsi').textContent = g('f-deskripsi')    || '—';
  document.getElementById('prev-lokasi').textContent    = g('f-lokasi') || '(Tidak diisi)';
  document.getElementById('prev-jam').textContent       = g('f-jam')    || '(Tidak diisi)';
}

/* ── Buttons ── */
document.getElementById('btnNext1').addEventListener('click', function(){ if (validateStep1()) goToStep(2); });
document.getElementById('btnPrev2').addEventListener('click', function(){ goToStep(1); });
document.getElementById('btnNext2').addEventListener('click', function(){ if (validateStep2()) goToStep(3); });
document.getElementById('btnPrev3').addEventListener('click', function(){ goToStep(2); });

document.getElementById('btnSubmit').addEventListener('click', async function () {
  if (!document.getElementById('agreeCheck').checked) {
    showErr(3, 'Anda harus menyetujui pernyataan sebelum mengirim.');
    return;
  }

  const btnSubmit = this;
  btnSubmit.disabled = true;
  btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';

  try {
    const kategori = document.querySelector('input[name="kategori"]:checked');

    const categoryMap = {
      usaha: 1,
      jasa: 2,
      hunian: 3,
      barang: 4
    };

    const payload = {
      category_id: categoryMap[kategori.value],
      name: document.getElementById('f-nama-potensi').value.trim(),
      owner_name: document.getElementById('f-nama').value.trim(),
      whatsapp:
        document.getElementById('f-kontak-usaha').value.trim() ||
        document.getElementById('f-telp').value.trim(),
      address: document.getElementById('f-alamat').value.trim(),
      description: document.getElementById('f-deskripsi').value.trim(),
      location:
        document.getElementById('f-lokasi').value.trim() ||
        document.getElementById('f-alamat').value.trim(),
      status: 'pending'
    };

    const API_BASE = (window.location.protocol === 'file:' || window.location.port === '5500') ? 'http://localhost:8000/api' : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');
    const response = await fetch(API_BASE + '/potensi', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    });

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.message || 'Gagal mengirim pendaftaran.');
    }

    document.getElementById('pendaftaranForm').style.display = 'none';
    document.getElementById('stepIndicator').style.display = 'none';
    document.getElementById('successState').style.display = 'block';

    document.getElementById('refNumber').textContent =
      '#EP-' + result.data.id;

    window.scrollTo({ top: 0, behavior: 'smooth' });

  } catch (error) {
    console.error('Gagal mengirim pendaftaran:', error);
    showErr(3, error.message || 'Terjadi kesalahan saat mengirim data.');
  } finally {
    btnSubmit.disabled = false;
    btnSubmit.innerHTML = '<i class="bi bi-send me-1"></i> Kirim Pendaftaran';
  }
});

document.getElementById('btnDaftarLagi').addEventListener('click', function(){
  document.getElementById('pendaftaranForm').reset();
  document.querySelectorAll('.kat-pill').forEach(function(p){ p.classList.remove('selected'); });
  var prev = document.getElementById('photoPreview');
  prev.style.display = 'none'; prev.innerHTML = '';
  document.getElementById('successState').style.display    = 'none';
  document.getElementById('pendaftaranForm').style.display = '';
  document.getElementById('stepIndicator').style.display   = 'block';
  goToStep(1);
});

/* ── Kategori Pills ── */
document.querySelectorAll('.kat-pill').forEach(function(pill){
  pill.addEventListener('click', function(){
    document.querySelectorAll('.kat-pill').forEach(function(p){ p.classList.remove('selected'); });
    this.classList.add('selected');
    var radio = this.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
  });
});

/* ── File Upload (Dropzone) ── */
var uploadArea = document.getElementById('uploadArea');
var fileInput  = document.getElementById('fileInput');
var dzBtn      = document.getElementById('dzBtnTelusuri');
var PHOTO_ALLOWED = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
var PHOTO_MAX     = 10 * 1024 * 1024; // 10 MB

if (dzBtn) dzBtn.addEventListener('click', function(e){ e.stopPropagation(); fileInput.click(); });
uploadArea.addEventListener('click', function(){ fileInput.click(); });
uploadArea.addEventListener('dragover', function(e){ e.preventDefault(); this.classList.add('dragover'); });
uploadArea.addEventListener('dragleave', function(){ this.classList.remove('dragover'); });
uploadArea.addEventListener('drop', function(e){
  e.preventDefault(); this.classList.remove('dragover');
  if (e.dataTransfer.files.length) handleFiles(e.dataTransfer.files);
});
fileInput.addEventListener('change', function(){ if (this.files.length) handleFiles(this.files); });

function handleFiles(files) {
  var preview = document.getElementById('photoPreview');
  preview.innerHTML = '';
  var valid = Array.from(files).filter(function(file){
    if (PHOTO_ALLOWED.indexOf(file.type) === -1){
      showErr(2, 'Format foto tidak didukung. Gunakan JPG, PNG, atau WebP.');
      return false;
    }
    if (file.size > PHOTO_MAX){
      showErr(2, 'Ukuran foto melebihi 10 MB: ' + file.name);
      return false;
    }
    return true;
  }).slice(0, 3);
  if (!valid.length){ preview.style.display = 'none'; return; }
  preview.style.display = 'flex';
  valid.forEach(function(file){
    var reader = new FileReader();
    reader.onload = function(e){
      var img = document.createElement('img'); img.src = e.target.result;
      preview.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
}

/* ── Navbar scroll ── */
window.addEventListener('scroll', function () {
  var s = window.scrollY > 40;
  var n = document.getElementById('mainNavbar');
  var h = document.getElementById('siteHeader');
  if (n) n.classList.toggle('scrolled', s);
  if (h) h.classList.toggle('scrolled', s);
}, { passive: true });

/* ── Back to Top ── */
var btt = document.getElementById('backToTop');
window.addEventListener('scroll', function(){ btt.classList.toggle('show', window.scrollY > 400); }, { passive: true });
btt.addEventListener('click', function(){ window.scrollTo({ top: 0, behavior: 'smooth' }); });
</script>
<!-- ============================================================
     MODAL LOGIN ADMIN
     ============================================================ -->
<div class="modal fade modal-login-admin" id="modalLoginAdmin" tabindex="-1" aria-labelledby="modalLoginAdminLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
    <div class="modal-content">
      <div class="modal-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="login-icon-badge">
            <i class="bi bi-shield-lock-fill"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold mb-0" id="modalLoginAdminLabel" style="color:var(--clr-blue);font-size:1.1rem;">Login Administrator</h5>
            <small class="text-muted" style="font-size:.78rem;">Portal EtalasePelemwatu</small>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="formLoginAdmin" novalidate>
        <div class="modal-body p-4">
          <div id="loginAlertBox" class="alert alert-danger py-2 px-3 mb-3 d-none" style="font-size:.84rem;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <span id="loginAlertMsg">Username atau kata sandi tidak sesuai.</span>
          </div>

          <div class="mb-3">
            <label for="adminUsername" class="form-label fw-semibold" style="color:var(--clr-blue);font-size:.88rem;">Username / Email</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0" style="color:var(--clr-text-light);">
                <i class="bi bi-person"></i>
              </span>
              <input type="text" class="form-control border-start-0" id="adminUsername" placeholder="Masukkan username atau email" required autocomplete="username">
            </div>
          </div>

          <div class="mb-3">
            <label for="adminPassword" class="form-label fw-semibold" style="color:var(--clr-blue);font-size:.88rem;">Kata Sandi</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0" style="color:var(--clr-text-light);">
                <i class="bi bi-key"></i>
              </span>
              <input type="password" class="form-control border-start-0 border-end-0" id="adminPassword" placeholder="Masukkan kata sandi" required autocomplete="current-password">
              <button class="btn btn-outline-secondary border-start-0" type="button" id="btnTogglePassword" style="background:#fff;border-color:var(--clr-border);">
                <i class="bi bi-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
          <button type="submit" class="btn-primary-custom w-100 justify-content-center" id="btnSubmitLogin" style="height:44px;">
            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Panel Admin
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="assets/js/auth.js"></script>
<script>
(function () {
  var API_BASE = (window.location.protocol === 'file:' || window.location.port === '5500')
    ? 'http://localhost:8000/api'
    : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');

  fetch(API_BASE + '/settings/logo')
    .then(function (r) { return r.json(); })
    .then(function (res) {
      if (res && res.status === 'success' && res.data && res.data.path) {
        document.querySelectorAll('[data-site-logo]').forEach(function (img) {
          img.src = res.data.path;
        });
      }
    })
    .catch(function () { /* biarkan logo bawaan jika request gagal */ });
})();
</script>
</body>
</html>
