<?php
/**
 * --------------------------------------------------------------------------
 * DASHBOARD KARYAWAN ELEGAN & ULTRA ESTETIK - E-PAYROLL SYSTEM
 * --------------------------------------------------------------------------
 */
?>

<!-- Custom Style UI Estetik Modern -->
<style>
  /* Root Theme Variables */
  :root {
    --primary-grad: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
    --success-grad: linear-gradient(135deg, #10b981 0%, #047857 100%);
    --warning-grad: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
    --info-grad: linear-gradient(135deg, #06b6d4 0%, #0e7490 100%);
    --purple-grad: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
  }

  /* Glassmorphism & Micro-Interactions */
  .hero-gradient {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
    border-radius: 28px;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }

  .hero-gradient::before {
    content: '';
    position: absolute;
    top: -40%;
    right: -10%;
    width: 380px;
    height: 380px;
    background: radial-gradient(circle, rgba(13, 110, 253, 0.25) 0%, rgba(255, 255, 255, 0) 70%);
    border-radius: 50%;
    animation: floatGlow 6s infinite alternate ease-in-out;
  }

  @keyframes floatGlow {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(-30px, 30px) scale(1.15); }
  }

  /* Estetik Feature Cards */
  .estetik-card {
    border: none;
    border-radius: 20px;
    background: #ffffff;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    position: relative;
    overflow: hidden;
  }

  .estetik-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: transparent;
    transition: all 0.3s ease;
  }

  .estetik-card.card-primary::after { background: var(--primary-grad); }
  .estetik-card.card-success::after { background: var(--success-grad); }
  .estetik-card.card-warning::after { background: var(--warning-grad); }

  .estetik-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
  }

  /* Icon Glow & Bounce */
  .icon-shape {
    width: 68px;
    height: 68px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    transition: all 0.3s ease;
  }

  .estetik-card:hover .icon-shape {
    transform: scale(1.1) rotate(6deg);
  }

  /* Status Dot Pulse */
  .pulse-indicator {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    background: rgba(34, 197, 94, 0.1);
    color: #15803d;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 600;
  }

  .pulse-dot {
    width: 8px;
    height: 8px;
    background-color: #22c55e;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
    animation: pulse 1.8s infinite;
  }

  @keyframes pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
  }

  /* Action Link Buttons */
  .card-action-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px;
    border-radius: 12px;
    background: #f8fafc;
    color: #334155;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
  }

  .card-action-btn:hover {
    background: #0d6efd;
    color: #ffffff !important;
    padding-right: 22px;
  }

  .card-action-btn:hover .arrow-icon {
    transform: translateX(4px);
  }

  .arrow-icon {
    transition: transform 0.3s ease;
  }

  /* Typography Contrast */
  .text-dark-title {
    color: #0f172a !important;
    font-weight: 700;
  }

  .text-subtle {
    color: #64748b !important;
  }

  /* Step Guide Box */
  .guide-box {
    background: #ffffff;
    border-radius: 18px;
    padding: 28px;
    border: 1px solid #f1f5f9;
    transition: all 0.3s ease;
  }

  .guide-box:hover {
    border-color: #cbd5e1;
    background: #fafafa;
    transform: translateY(-4px);
  }
</style>

<div class="container-fluid px-4 py-4">

  <!-- HERO BANNER START -->
  <div class="hero-gradient p-4 p-md-5 mb-5 shadow-lg text-white wow fadeIn" data-wow-delay="0.1s">
    <div class="row align-items-center position-relative" style="z-index: 2;">
      <div class="col-lg-8">
        <div class="pulse-indicator bg-white text-dark mb-3 shadow-sm">
          <span class="pulse-dot"></span> System Operational • Portal Karyawan
        </div>
        <h1 class="display-4 fw-bold text-white mb-2">
          Selamat Datang, <?php echo htmlspecialchars($user_nama); ?>! 👋
        </h1>
        <p class="fs-5 text-light opacity-90 mb-4" style="max-width: 650px; line-height: 1.6;">
          Pantau catatan kehadiran harian, ajukan izin kerja, dan unduh slip gaji bulanan Anda dengan mudah, transparan, dan real-time.
        </p>
        <div class="d-flex flex-wrap gap-3 pt-2">
          <a href="index.php?page=presensi" class="btn btn-primary btn-lg rounded-3 fw-bold px-4 py-3 shadow-sm">
            <i class="fa fa-fingerprint me-2"></i> Input Presensi
          </a>
          <a href="index.php?page=slip-gaji" class="btn btn-outline-light btn-lg rounded-3 fw-bold px-4 py-3">
            <i class="fa fa-file-invoice-dollar me-2"></i> Lihat Slip Gaji
          </a>
        </div>
      </div>
    </div>
  </div>
  <!-- HERO BANNER END -->

  <!-- ESTETIK CARDS BROWSER START -->
  <div class="row g-4 mb-5">
    
    <!-- Card 1: Profil Karyawan -->
    <div class="col-12 col-md-6 col-xl-4 wow fadeInUp" data-wow-delay="0.2s">
      <div class="estetik-card card-primary p-4 h-100 d-flex flex-column justify-content-between">
        <div>
          <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="icon-shape bg-primary-subtle text-primary">
              <i class="fa fa-user-gear"></i>
            </div>
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">
              ID: #EMP-<?php echo str_pad($employee_id, 4, '0', STR_PAD_LEFT); ?>
            </span>
          </div>
          <p class="text-subtle text-uppercase fw-semibold mb-1 small">Informasi Pegawai</p>
          <h4 class="text-dark-title mb-2"><?php echo htmlspecialchars($user_nama); ?></h4>
          <p class="text-subtle mb-4"><i class="fa fa-envelope me-2 text-primary"></i><?php echo htmlspecialchars($user_email); ?></p>
        </div>
        
        <div class="pt-3 border-top d-flex justify-content-between align-items-center">
          <span class="text-subtle small fw-medium">Role Akses:</span>
          <span class="badge bg-dark text-white text-uppercase px-3 py-2 rounded-2 fw-semibold">
            <?php echo htmlspecialchars($user_role); ?>
          </span>
        </div>
      </div>
    </div>

    <!-- Card 2: Status Presensi Hari Ini -->
    <div class="col-12 col-md-6 col-xl-4 wow fadeInUp" data-wow-delay="0.3s">
      <div class="estetik-card card-success p-4 h-100 d-flex flex-column justify-content-between">
        <div>
          <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="icon-shape bg-success-subtle text-success">
              <i class="fa fa-clock-rotate-left"></i>
            </div>
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">
              <i class="fa fa-calendar-day me-1"></i> <?php echo date('d M Y'); ?>
            </span>
          </div>
          <p class="text-subtle text-uppercase fw-semibold mb-1 small">Kehadiran Harian</p>
          <h4 class="text-dark-title mb-2">Presensi Hari Ini</h4>
          <p class="text-subtle mb-4">Pastikan Anda melakukan presensi masuk dan keluar jam kerja secara tepat waktu.</p>
        </div>

        <div>
          <a href="index.php?page=presensi" class="card-action-btn">
            <span>Input Presensi Sekarang</span>
            <i class="fa fa-arrow-right arrow-icon"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Card 3: Slip Gaji Bulanan -->
    <div class="col-12 col-md-12 col-xl-4 wow fadeInUp" data-wow-delay="0.4s">
      <div class="estetik-card card-warning p-4 h-100 d-flex flex-column justify-content-between">
        <div>
          <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="icon-shape bg-warning-subtle text-warning">
              <i class="fa fa-wallet"></i>
            </div>
            <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill fw-bold">
              Periode Aktif
            </span>
          </div>
          <p class="text-subtle text-uppercase fw-semibold mb-1 small">Informasi Penggajian</p>
          <h4 class="text-dark-title mb-2">Slip Gaji Bulanan</h4>
          <p class="text-subtle mb-4">Periksa rincian gaji pokok, tunjangan jabatan, dan rekap potongan penggajian Anda.</p>
        </div>

        <div>
          <a href="index.php?page=slip-gaji" class="card-action-btn">
            <span>Buka & Unduh Slip</span>
            <i class="fa fa-download arrow-icon"></i>
          </a>
        </div>
      </div>
    </div>

  </div>
  <!-- ESTETIK CARDS END -->

  <!-- FITUR PANDUAN KARYAWAN START -->
  <div class="bg-white rounded-4 p-4 p-md-5 shadow-sm border mb-4 wow fadeInUp" data-wow-delay="0.5s">
    <div class="text-center mx-auto mb-5" style="max-width: 600px;">
      <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold mb-2">Layanan Mandiri</span>
      <h3 class="fw-bold text-dark-title">Panduan Penggunaan Portal</h3>
      <p class="text-subtle">Langkah mudah mengelola aktivitas administratif kerja Anda di E-Payroll</p>
    </div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="guide-box h-100 text-center">
          <div class="icon-shape bg-primary-subtle text-primary mx-auto mb-3">
            <i class="fa fa-fingerprint"></i>
          </div>
          <h5 class="fw-bold text-dark-title mb-2">1. Record Presensi</h5>
          <p class="text-subtle small mb-0">Lakukan absensi sesuai lokasi dan jadwal jam kerja untuk menjaga akurasi rekapan kehadiran.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="guide-box h-100 text-center">
          <div class="icon-shape bg-info-subtle text-info mx-auto mb-3">
            <i class="fa fa-file-signature"></i>
          </div>
          <h5 class="fw-bold text-dark-title mb-2">2. Pengajuan Cuti/Izin</h5>
          <p class="text-subtle small mb-0">Ajukan permohonan ketidakhadiran kerja dengan bukti pendukung langsung ke HRD.</p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="guide-box h-100 text-center">
          <div class="icon-shape bg-success-subtle text-success mx-auto mb-3">
            <i class="fa fa-receipt"></i>
          </div>
          <h5 class="fw-bold text-dark-title mb-2">3. Transparansi Gaji</h5>
          <p class="text-subtle small mb-0">Unduh dokumen resmi slip gaji bulanan yang telah diproses dan diterbitkan oleh tim Finance.</p>
        </div>
      </div>
    </div>
  </div>
  <!-- FITUR PANDUAN KARYAWAN END -->

</div>