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
    /* A. Teks tidak meluber di kartu Konfirmasi */
    .preview-card .row > [class*="col"] { min-width:0; }
    .preview-value { overflow-wrap:anywhere; word-break:break-word; }
    #prev-deskripsi { white-space:pre-line; }
    /* D. Saran email */
    #emailSuggest { display:none; font-size:.8rem; color:var(--text-mid,#6b7280); margin-top:.3rem; }
    #emailSuggest button { background:none; border:none; padding:0; color:var(--clr-orange,#f2630f); font-weight:600; font-size:.8rem; cursor:pointer; text-decoration:underline; }
  </style>
</head>
<body class="form-page">

<!-- NAVBAR -->
<header id="siteHeader">
  <nav class="navbar navbar-expand-lg" id="mainNavbar">
    <div class="container">
      <a class="navbar-brand-wrap" href="/">
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
        <ul class="navbar-nav mx-auto gap-1">
          <li class="nav-item"><a class="nav-link" href="/#hero-desa">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="/#profil-desa">Profil Desa</a></li>
          <li class="nav-item"><a class="nav-link" href="/#pengenalan-etalase">Tentang</a></li>
          <li class="nav-item"><a class="nav-link" href="/#katalog">Katalog</a></li>
          <li class="nav-item"><a class="nav-link" href="/#layanan">Layanan</a></li>
          <li class="nav-item"><a class="nav-link" href="/#kontak-section">Kontak</a></li>
        </ul>
        <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0 ms-lg-2 flex-wrap">
          <div id="navWargaWrap"></div>
          <div id="navAdminWrap">
            <button type="button" class="btn-nav-admin" data-bs-toggle="modal" data-bs-target="#modalLoginAdmin">
              Login Admin
            </button>
          </div>
          <a href="/daftar-potensi" class="nav-link btn-nav-daftar active" id="navBtnDaftar">
            Daftarkan Potensi
            <span class="nav-cta-arrow"><i class="bi bi-arrow-up-right"></i></span>
          </a>
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
        <a href="/"><i class="bi bi-house me-1"></i>Beranda</a>
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
                    <input type="text" class="form-control" id="f-nama" placeholder="Masukkan nama lengkap Anda" required maxlength="100">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Nomor Telepon <span class="required-mark">*</span></label>
                    <input type="tel" class="form-control" id="f-telp" placeholder="Contoh: 08123456789" required inputmode="tel" maxlength="20">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="f-email" placeholder="email@contoh.com (opsional)" maxlength="100">
                    <div id="emailSuggest"></div>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Alamat Rumah <span class="required-mark">*</span></label>
                    <textarea class="form-control" id="f-alamat" rows="2" placeholder="Alamat lengkap di Desa Pelemwatu" required maxlength="255"></textarea>
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
                <div class="inline-err" id="kategoriLoadErr" style="margin-top:.6rem;">
                  <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                  <span>Gagal memuat daftar kategori dari server. Pilihan kategori mungkin tidak akurat.</span>
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
                    <input type="text" class="form-control" id="f-nama-potensi" placeholder="Nama usaha, produk, atau jasa Anda" required maxlength="100">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Jenis / Subkategori</label>
                    <input type="text" class="form-control" id="f-jenis" placeholder="Contoh: Laundry kiloan, Kost putra" maxlength="100">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Deskripsi <span class="required-mark">*</span></label>
                    <textarea class="form-control" id="f-deskripsi" rows="4" placeholder="Jelaskan potensi Anda secara singkat dan jelas..." required maxlength="1000"></textarea>
                    <div class="form-text">Minimal 20 karakter dan 3 kata. Tulis dengan kalimat yang jelas.</div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Alamat / Lokasi Potensi</label>
                    <input type="text" class="form-control" id="f-lokasi" placeholder="Alamat usaha jika berbeda dari rumah" maxlength="255">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Jam Operasional</label>
                    <input type="text" class="form-control" id="f-jam" placeholder="Contoh: Senin–Sabtu, 08.00–20.00 WIB" maxlength="100">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Kontak Usaha</label>
                    <input type="tel" class="form-control" id="f-kontak-usaha" placeholder="Nomor yang dapat dihubungi calon pelanggan" inputmode="tel" maxlength="20">
                  </div>
                </div>

                <div class="form-section-title mt-3"><i class="bi bi-camera"></i> Foto Dokumentasi</div>
                <p style="font-size:.88rem;color:var(--text-mid);" class="mb-3">Upload foto yang relevan (opsional, maks. 3 foto).</p>
                <!-- Dropzone -->
                <div class="upload-dropzone" id="uploadArea">
                  <input type="file" id="fileInput" accept="image/jpeg,image/jpg,image/png,image/webp" multiple style="display:none;">
                  <i class="bi bi-image" style="font-size:2rem;color:var(--clr-blue);opacity:.45;display:block;margin-bottom:.5rem;"></i>
                  <div class="dz-title">Pilih file atau seret ke sini</div>
                  <div class="dz-hint">JPG, PNG, WebP, hingga 2 MB per foto, maks. 3 foto</div>
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
                    <div class="col-12"><div class="preview-label">Alamat Rumah</div><div class="preview-value" id="prev-alamat">—</div></div>
                    <div class="col-md-6"><div class="preview-label">Email</div><div class="preview-value" id="prev-email">—</div></div>
                    <div class="col-md-6"><div class="preview-label">Kategori Potensi</div><div class="preview-value" id="prev-kategori">—</div></div>
                    <div class="col-md-6"><div class="preview-label">Nama Potensi / Usaha</div><div class="preview-value" id="prev-potensi">—</div></div>
                    <div class="col-md-6"><div class="preview-label">Jenis Potensi</div><div class="preview-value" id="prev-jenis">—</div></div>
                    <div class="col-12"><div class="preview-label">Deskripsi</div><div class="preview-value" id="prev-deskripsi" style="font-size:.9rem;font-weight:400;color:var(--text-mid);">—</div></div>
                    <div class="col-md-6"><div class="preview-label">Lokasi</div><div class="preview-value" id="prev-lokasi">—</div></div>
                    <div class="col-md-6"><div class="preview-label">Jam Operasional</div><div class="preview-value" id="prev-jam">—</div></div>
                    <div class="col-md-6"><div class="preview-label">Kontak Usaha</div><div class="preview-value" id="prev-kontak-usaha">—</div></div>
                    <div class="col-md-6"><div class="preview-label">Jumlah Foto</div><div class="preview-value" id="prev-foto">—</div></div>
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
                  <a href="/" class="btn-riviera-custom">
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
                {{ $desa['alamat'] }}
              </div>
              <div style="background:var(--clr-bg);border-radius:8px;padding:.85rem;font-size:.84rem;color:var(--clr-blue);margin-bottom:.6rem;">
                <i class="bi bi-clock me-1" style="color:var(--clr-orange);"></i>
                {{ $desa['jam_operasional'] }}
              </div>
              <a href="https://wa.me/{{ $desa['whatsapp'] }}?text=Halo%2C%20saya%20butuh%20bantuan%20pengisian%20formulir%20EtalasePelemwatu."
                 target="_blank" rel="noopener noreferrer"
                 class="w-100 justify-content-center mt-3"
                 style="display:flex;align-items:center;gap:.5rem;background:#25D366;color:#fff;
                        padding:.6rem 1rem;border-radius:8px;font-size:.85rem;font-weight:600;
                        text-decoration:none;border:none;">
                <i class="bi bi-whatsapp"></i> Chat WhatsApp
              </a>
              <a href="{{ url('/#kontak-section') }}" class="btn-outline-custom w-100 justify-content-center mt-2" style="font-size:.85rem;">
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
          <li><a href="/">Beranda</a></li>
          <li><a href="/#katalog">Katalog</a></li>
          <li><a href="/#kontak-section">Kontak</a></li>
          <li><a href="/admin">Admin</a></li>
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
  if (el) {
    el.style.display = 'flex';
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    clearTimeout(el._hideTimer);
    el._hideTimer = setTimeout(function(){ el.style.display = 'none'; }, 5000);
  }
}

/* ── Helpers ── */
function cleanSpaces(s) { return s.replace(/\s+/g, ' ').trim(); }

function normalizePhone(raw) {
  // Hapus spasi, tanda hubung, tanda kurung
  var s = raw.replace(/[\s\-()]/g, '');
  // Ubah awalan +62 atau 62 (tanpa leading 0) menjadi 0
  s = s.replace(/^\+?62(?=8)/, '0');
  return s;
}

function validatePhone(raw) {
  var s = raw.replace(/[\s\-()]/g, '');
  s = s.replace(/^\+?62(?=8)/, '0');
  return /^(?:\+?62|0)8[1-9][0-9]{7,10}$/.test(raw.replace(/[\s\-()]/g, ''))
      || /^08[1-9][0-9]{7,10}$/.test(s);
}

function markInvalid(id) {
  var el = document.getElementById(id);
  if (!el) return;
  el.classList.add('is-invalid');
  el.addEventListener('input', function onInput() {
    el.classList.remove('is-invalid');
    el.removeEventListener('input', onInput);
  });
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

/* ── B. Validasi ── */
function validateStep1() {
  // Nama Lengkap
  var namaEl = document.getElementById('f-nama');
  var nama = cleanSpaces(namaEl.value);
  namaEl.value = nama;
  if (!nama || nama.length < 3 || nama.length > 100 || !/^[A-Za-zÀ-ÖØ-öø-ÿ\s.''\-]+$/.test(nama)) {
    showErr(1, 'Nama lengkap minimal 3 huruf dan hanya boleh berisi huruf.');
    markInvalid('f-nama'); namaEl.focus(); return false;
  }

  // Nomor Telepon
  var telpEl = document.getElementById('f-telp');
  var telp = telpEl.value.trim();
  if (!telp) {
    showErr(1, 'Nomor telepon wajib diisi.');
    markInvalid('f-telp'); telpEl.focus(); return false;
  }
  if (!validatePhone(telp)) {
    showErr(1, 'Nomor telepon tidak valid. Contoh: 08123456789.');
    markInvalid('f-telp'); telpEl.focus(); return false;
  }

  // Email (opsional)
  var emailEl = document.getElementById('f-email');
  var email = emailEl.value.trim().toLowerCase();
  if (email) {
    if (email.length > 100 || !/^[a-z0-9._%+\-]+@[a-z0-9\-]+(\.[a-z0-9\-]+)*\.[a-z]{2,}$/.test(email)) {
      showErr(1, 'Format email tidak valid. Contoh: nama@gmail.com.');
      markInvalid('f-email'); emailEl.focus(); return false;
    }
  }
  // Alamat Rumah
  var alamatEl = document.getElementById('f-alamat');
  var alamat = cleanSpaces(alamatEl.value);
  alamatEl.value = alamat;
  if (alamat.length < 10 || alamat.length > 255) {
    showErr(1, 'Alamat rumah minimal 10 karakter.');
    markInvalid('f-alamat'); alamatEl.focus(); return false;
  }

  // Kategori
  var kat = document.querySelector('input[name="kategori"]:checked');
  if (!kat) { showErr(1, 'Silakan pilih kategori potensi.'); return false; }

  return true;
}

function validateStep2() {
  // Nama Potensi
  var namaPEl = document.getElementById('f-nama-potensi');
  var namaP = cleanSpaces(namaPEl.value);
  namaPEl.value = namaP;
  if (!namaP || namaP.length < 3 || namaP.length > 100 || !/[A-Za-zÀ-ÖØ-öø-ÿ0-9]/.test(namaP)) {
    showErr(2, 'Nama potensi minimal 3 karakter.');
    markInvalid('f-nama-potensi'); namaPEl.focus(); return false;
  }

  // Deskripsi
  var deskEl = document.getElementById('f-deskripsi');
  var desk = cleanSpaces(deskEl.value);
  deskEl.value = desk;
  if (desk.length < 20 || desk.length > 1000) {
    showErr(2, 'Deskripsi minimal 20 karakter.');
    markInvalid('f-deskripsi'); deskEl.focus(); return false;
  }
  var deskWords = desk.split(/\s+/).filter(function(w){ return w.length > 0; });
  if (deskWords.length < 3) {
    showErr(2, 'Deskripsi minimal 3 kata.');
    markInvalid('f-deskripsi'); deskEl.focus(); return false;
  }
  if (/([A-Za-z])\1{7,}/.test(desk)) {
    showErr(2, 'Deskripsi mengandung huruf yang diulang-ulang, mohon ditulis dengan kalimat yang jelas.');
    markInvalid('f-deskripsi'); deskEl.focus(); return false;
  }
  var hasLongWord = deskWords.some(function(w) {
    if (w.length <= 50) return false;
    var lower = w.toLowerCase();
    return !(lower.indexOf('http') === 0 || lower.indexOf('www.') === 0 || w.charAt(0) === '@');
  });
  if (hasLongWord) {
    showErr(2, 'Ada kata yang terlalu panjang dalam deskripsi.');
    markInvalid('f-deskripsi'); deskEl.focus(); return false;
  }

  // Lokasi (opsional)
  var lokasiEl = document.getElementById('f-lokasi');
  var lokasi = cleanSpaces(lokasiEl.value);
  lokasiEl.value = lokasi;
  if (lokasi && (lokasi.length < 3 || lokasi.length > 255)) {
    showErr(2, 'Lokasi minimal 3 karakter jika diisi.');
    markInvalid('f-lokasi'); lokasiEl.focus(); return false;
  }

  // Jam Operasional (opsional)
  var jamEl = document.getElementById('f-jam');
  var jam = cleanSpaces(jamEl.value);
  jamEl.value = jam;
  if (jam.length > 100) {
    showErr(2, 'Jam operasional maksimal 100 karakter.');
    markInvalid('f-jam'); jamEl.focus(); return false;
  }

  // Kontak Usaha (opsional)
  var kontakEl = document.getElementById('f-kontak-usaha');
  var kontak = kontakEl.value.trim();
  if (kontak && !validatePhone(kontak)) {
    showErr(2, 'Nomor telepon tidak valid. Contoh: 08123456789.');
    markInvalid('f-kontak-usaha'); kontakEl.focus(); return false;
  }

  return true;
}

/* ── C. Penyaringan ketikan ── */
function filterTelInput(el) {
  el.addEventListener('input', function() {
    var pos = el.selectionStart;
    var orig = el.value;
    var filtered = orig.replace(/[^0-9+\s\-()]/g, '');
    if (filtered !== orig) {
      var diff = orig.length - filtered.length;
      el.value = filtered;
      el.setSelectionRange(Math.max(0, pos - diff), Math.max(0, pos - diff));
    }
  });
}
filterTelInput(document.getElementById('f-telp'));
filterTelInput(document.getElementById('f-kontak-usaha'));

/* ── D. Saran email otomatis ── */
(function() {
  var emailEl  = document.getElementById('f-email');
  var suggestEl = document.getElementById('emailSuggest');
  if (!emailEl || !suggestEl) return;

  var TYPOS = {
    'gmial.com': 'gmail.com',
    'gmaill.com': 'gmail.com',
    'gmai.com': 'gmail.com',
    'gmal.com': 'gmail.com',
    'gmail.co': 'gmail.com',
    'gmail.con': 'gmail.com',
    'gmail.cm': 'gmail.com',
    'yaho.com': 'yahoo.com',
    'yahooo.com': 'yahoo.com',
    'hotmial.com': 'hotmail.com'
  };
  var GMAIL_PREFIX = 'gmail.com';

  function getSuggestion(val) {
    if (!val) return null;
    var atIdx = val.indexOf('@');
    if (atIdx === -1) {
      // Belum ada @, hanya karakter sah bagian depan email, min 3
      if (val.length < 3) return null;
      if (!/^[a-zA-Z0-9._%+\-]+$/.test(val)) return null;
      return val + '@gmail.com';
    }
    var local  = val.slice(0, atIdx);
    var domain = val.slice(atIdx + 1).toLowerCase();
    if (!local) return null;
    // Domain adalah awalan dari gmail.com tapi belum lengkap
    if (domain.length > 0 && domain.length < GMAIL_PREFIX.length && GMAIL_PREFIX.indexOf(domain) === 0) {
      return local + '@gmail.com';
    }
    // Domain salah ketik umum
    if (TYPOS[domain]) {
      return local + '@' + TYPOS[domain];
    }
    return null;
  }

  function isValidEmail(v) {
    return /^[a-z0-9._%+\-]+@[a-z0-9\-]+(\.[a-z0-9\-]+)*\.[a-z]{2,}$/.test(v.toLowerCase());
  }

  function updateSuggest() {
    var val = emailEl.value.trim();
    if (!val) { suggestEl.style.display = 'none'; suggestEl.textContent = ''; return; }
    // Sembunyikan jika valid dan bukan domain typo/gmail-prefix
    var sugg = getSuggestion(val);
    if (!sugg) { suggestEl.style.display = 'none'; suggestEl.textContent = ''; return; }
    if (isValidEmail(val) && !sugg) { suggestEl.style.display = 'none'; return; }

    // Bangun teks dengan textContent dan button
    suggestEl.textContent = '';
    var prefix = document.createTextNode('Maksud Anda ');
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.textContent = sugg;
    var suffix = document.createTextNode('?');
    suggestEl.appendChild(prefix);
    suggestEl.appendChild(btn);
    suggestEl.appendChild(suffix);
    suggestEl.style.display = 'block';

    btn.addEventListener('click', function() {
      emailEl.value = sugg;
      suggestEl.style.display = 'none';
      suggestEl.textContent = '';
      emailEl.focus();
    });
  }

  emailEl.addEventListener('input', updateSuggest);
  emailEl.addEventListener('blur', updateSuggest);
})();

/* ── E. Preview ── */
function fillPreview() {
  var g = function(id){ var el=document.getElementById(id); return el ? cleanSpaces(el.value) : ''; };
  var kat    = document.querySelector('input[name="kategori"]:checked');
  var katMap = { usaha: 'Usaha & Produk', jasa: 'Jasa', hunian: 'Hunian', barang: 'Barang Warga' };
  var katLbl = kat ? (katMap[kat.value] || kat.value) : '—';

  document.getElementById('prev-nama').textContent      = g('f-nama') || '—';
  document.getElementById('prev-telp').textContent      = normalizePhone(g('f-telp')) || '—';
  document.getElementById('prev-alamat').textContent    = g('f-alamat') || '—';
  document.getElementById('prev-email').textContent     = g('f-email').toLowerCase() || '(Tidak diisi)';
  document.getElementById('prev-kategori').textContent  = katLbl;
  document.getElementById('prev-potensi').textContent   = g('f-nama-potensi') || '—';
  document.getElementById('prev-jenis').textContent     = g('f-jenis') || '(Tidak diisi)';
  document.getElementById('prev-deskripsi').textContent = g('f-deskripsi') || '—';
  document.getElementById('prev-lokasi').textContent    = g('f-lokasi') || '(Tidak diisi)';
  document.getElementById('prev-jam').textContent       = g('f-jam')    || '(Tidak diisi)';
  document.getElementById('prev-kontak-usaha').textContent = g('f-kontak-usaha') || '(Tidak diisi)';

  // Jumlah foto
  var preview = document.getElementById('photoPreview');
  var fotoCount = preview ? preview.querySelectorAll('img').length : 0;
  document.getElementById('prev-foto').textContent = fotoCount > 0 ? (fotoCount + ' foto') : 'Tidak ada foto';
}

/* ── Buttons ── */
document.getElementById('btnNext1').addEventListener('click', function(){ if (validateStep1()) goToStep(2); });
document.getElementById('btnPrev2').addEventListener('click', function(){ goToStep(1); });
document.getElementById('btnNext2').addEventListener('click', function(){ if (validateStep2()) goToStep(3); });
document.getElementById('btnPrev3').addEventListener('click', function(){ goToStep(2); });

document.getElementById('btnSubmit').addEventListener('click', async function () {
  // Validasi ulang semua sebelum mengirim
  if (!validateStep1()) { goToStep(1); return; }
  if (!validateStep2()) { goToStep(2); return; }

  if (!document.getElementById('agreeCheck').checked) {
    showErr(3, 'Anda harus menyetujui pernyataan sebelum mengirim.');
    return;
  }

  const btnSubmit = this;
  btnSubmit.disabled = true;
  btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';

  try {
    const kategori = document.querySelector('input[name="kategori"]:checked');

    // Bug 1 fix: lookup category_id dari data API, bukan hard-coded map
    var katSlug   = kategori ? kategori.value : '';
    var katEntry  = _categoryApiData.find(function(c){ return c.slug === katSlug; });
    var categoryId = katEntry ? katEntry.id : null;

    // Jika kategori gagal dimuat dari API, tampilkan peringatan
    if (!categoryId && _categoryLoadError) {
      showErr(3, 'Kategori tidak dapat dimuat dari server. Coba muat ulang halaman.');
      btnSubmit.disabled = false;
      btnSubmit.innerHTML = '<i class="bi bi-send me-1"></i> Kirim Pendaftaran';
      return;
    }

    // Bersihkan nilai
    var namaVal    = cleanSpaces(document.getElementById('f-nama').value);
    var telpRaw    = document.getElementById('f-telp').value.trim();
    var telpNorm   = normalizePhone(telpRaw.replace(/[\s\-()]/g, ''));
    var alamatVal  = cleanSpaces(document.getElementById('f-alamat').value);
    var namaPVal   = cleanSpaces(document.getElementById('f-nama-potensi').value);
    var deskVal    = cleanSpaces(document.getElementById('f-deskripsi').value);
    var lokasiVal  = cleanSpaces(document.getElementById('f-lokasi').value);
    var emailVal   = document.getElementById('f-email').value.trim().toLowerCase();
    var jenisVal   = cleanSpaces(document.getElementById('f-jenis').value);
    var jamVal     = cleanSpaces(document.getElementById('f-jam').value);
    var kontakRaw  = document.getElementById('f-kontak-usaha').value.trim();
    var kontakNorm = kontakRaw ? normalizePhone(kontakRaw.replace(/[\s\-()]/g, '')) : '';

    // Bug 2 fix: gunakan FormData agar foto ikut terkirim
    var formData = new FormData();
    if (categoryId) formData.append('category_id', categoryId);
    formData.append('name',        namaPVal);
    formData.append('owner_name',  namaVal);
    formData.append('whatsapp',    kontakNorm || telpNorm);
    formData.append('address',     alamatVal);
    formData.append('description', deskVal);
    formData.append('location',    lokasiVal || alamatVal);
    formData.append('status',      'pending');

    // F. Tambah kunci baru hanya jika diisi
    if (emailVal)  formData.append('email',           emailVal);
    if (jenisVal)  formData.append('jenis',           jenisVal);
    if (jamVal)    formData.append('jam_operasional', jamVal);

    // Lampirkan foto yang terpilih
    _selectedFiles.forEach(function(file, idx){
      formData.append('images[' + idx + ']', file, file.name);
    });

    const API_BASE = (window.location.protocol === 'file:' || window.location.port === '5500') ? 'http://localhost:8000/api' : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');

    // Sertakan token warga jika sedang login
    var wargaHeaders = {};
    try {
      var _ws = JSON.parse(localStorage.getItem('ep_warga_session') || 'null');
      if (_ws && _ws.token) wargaHeaders['Authorization'] = 'Bearer ' + _ws.token;
    } catch(e) {}

    const response = await fetch(API_BASE + '/potensi', {
      method: 'POST',
      headers: wargaHeaders,
      // Tanpa Content-Type: biarkan browser set multipart boundary otomatis
      body: formData
    });

    const result = await response.json();

    if (!response.ok) {
      // F. Error 422: tampilkan pesan pertama dari errors
      var errMsg = 'Gagal mengirim pendaftaran.';
      if (response.status === 422 && result.errors) {
        var firstKey = Object.keys(result.errors)[0];
        if (firstKey && result.errors[firstKey] && result.errors[firstKey].length) {
          errMsg = result.errors[firstKey][0];
        }
      } else if (result.message) {
        errMsg = result.message;
      }
      showErr(3, errMsg);
      btnSubmit.disabled = false;
      btnSubmit.innerHTML = '<i class="bi bi-send me-1"></i> Kirim Pendaftaran';
      return;
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
    btnSubmit.disabled = false;
    btnSubmit.innerHTML = '<i class="bi bi-send me-1"></i> Kirim Pendaftaran';
  }
});

document.getElementById('btnDaftarLagi').addEventListener('click', function(){
  document.getElementById('pendaftaranForm').reset();
  document.querySelectorAll('.kat-pill').forEach(function(p){ p.classList.remove('selected'); });
  var prev = document.getElementById('photoPreview');
  prev.style.display = 'none'; prev.innerHTML = '';
  _selectedFiles = [];
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

/* ── Bug 1: Muat kategori dari API (bukan hard-coded) ── */
var _categoryApiData = []; // [{id, name, slug}]
var _categoryLoadError = false;

function slugKategori(name) {
  if (!name) return '';
  var n = name.toLowerCase();
  if (n.indexOf('usaha') !== -1 || n.indexOf('produk') !== -1) return 'usaha';
  if (n.indexOf('jasa') !== -1) return 'jasa';
  if (n.indexOf('hunian') !== -1 || n.indexOf('kost') !== -1) return 'hunian';
  if (n.indexOf('barang') !== -1) return 'barang';
  return '';
}

(function loadCategories() {
  var API_BASE_CAT = (window.location.protocol === 'file:' || window.location.port === '5500')
    ? 'http://localhost:8000/api'
    : (window.location.port === '3000' ? 'http://localhost:3000/api' : '/api');
  fetch(API_BASE_CAT + '/categories')
    .then(function(r){ return r.json(); })
    .then(function(json){
      if (json && Array.isArray(json.data) && json.data.length) {
        _categoryApiData = json.data.map(function(c){
          return { id: c.id, name: c.name, slug: slugKategori(c.name) };
        });
      } else {
        _categoryLoadError = true;
        var errEl = document.getElementById('kategoriLoadErr');
        if (errEl) errEl.style.display = 'flex';
      }
    })
    .catch(function(){
      _categoryLoadError = true;
      var errEl = document.getElementById('kategoriLoadErr');
      if (errEl) errEl.style.display = 'flex';
    });
})();

/* ── Bug 2: File Upload (Dropzone) — simpan file ke array ── */
var uploadArea   = document.getElementById('uploadArea');
var fileInput    = document.getElementById('fileInput');
var dzBtn        = document.getElementById('dzBtnTelusuri');
var PHOTO_ALLOWED = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
var PHOTO_MAX     = 2 * 1024 * 1024; // 2 MB (sesuai batas server)
var _selectedFiles = []; // array File yang akan dikirim ke server

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
  _selectedFiles = [];
  var valid = Array.from(files).filter(function(file){
    if (PHOTO_ALLOWED.indexOf(file.type) === -1){
      showErr(2, 'Format foto tidak didukung. Gunakan JPG, PNG, atau WebP.');
      return false;
    }
    if (file.size > PHOTO_MAX){
      showErr(2, 'Ukuran foto melebihi 2 MB: ' + file.name);
      return false;
    }
    return true;
  }).slice(0, 3);
  if (!valid.length){ preview.style.display = 'none'; return; }
  _selectedFiles = valid;
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

<script src="assets/js/utils.js"></script>
<script src="assets/js/warga-auth.js"></script>
<script src="assets/js/auth.js"></script>
<script src="assets/js/site-logo.js"></script>
</body>
</html>
