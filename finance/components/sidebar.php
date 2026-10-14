<div class="sidebar-wrapper" id="sidebar" style="background-color: #2C1A22; border-right: 1px solid rgba(128, 0, 32, 0.2);">
    <!-- Brand Logo / Identity -->
    <a href="index.php?page=dashboard-finance" class="sidebar-brand text-white text-decoration-none d-flex align-items-center px-4 py-3">
      <i class="bi bi-wallet2 me-2" style="color: #D4AF37;"></i>
      <span class="fw-bold">E-Payroll Finance</span>
    </a>

    <!-- Navigation Menu -->
    <div class="flex-grow-1 overflow-y-auto px-2">
      <!-- Group: Menu -->
      <div class="sidebar-menu-section mb-3">
        <div class="sidebar-menu-title text-uppercase small text-muted px-3 mb-2" style="font-size: 11px; letter-spacing: 0.5px;">Menu Utama</div>
        <ul class="sidebar-menu-list list-unstyled mb-0">
          <li class="sidebar-menu-item mb-1">
            <a href="index.php?page=dashboard" class="sidebar-menu-link d-flex align-items-center px-3 py-2 rounded-3 text-decoration-none text-white transition-all <?= (isset($_GET['page']) && $_GET['page'] == 'dashboard-finance') ? 'active bg-burgundy' : ''; ?>" id="menu-overview" title="Dashboard">
              <i class="bi bi-grid-fill me-3 fs-5" style="color: #D4AF37;"></i>
              <span>Dashboard</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Group: Components / Modul Keuangan -->
      <div class="sidebar-menu-section mb-3">
        <div class="sidebar-menu-title text-uppercase small text-muted px-3 mb-2" style="font-size: 11px; letter-spacing: 0.5px;">Komponen Keuangan</div>
        <ul class="sidebar-menu-list list-unstyled mb-0">
           <li class="sidebar-menu-item mb-1">
            <a href="index.php?page=laporan-gaji" class="sidebar-menu-link d-flex align-items-center px-3 py-2 rounded-3 text-decoration-none text-white transition-all <?= (isset($_GET['page']) && $_GET['page'] == 'laporan-gaji') ? 'active bg-burgundy' : ''; ?>" id="menu-laporan" title="Laporan & Slip">
              <i class="bi bi-file-earmark-text-fill me-3 fs-5" style="color: #D4AF37;"></i>
              <span>Laporan & Slip Gaji</span>
            </a>
          </li>
          <li class="sidebar-menu-item mb-1">
            <a href="index.php?page=hitung" class="sidebar-menu-link d-flex align-items-center px-3 py-2 rounded-3 text-decoration-none text-white transition-all <?= (isset($_GET['page']) && $_GET['page'] == 'hitung-gaji') ? 'active bg-burgundy' : ''; ?>" id="menu-hitung" title="Hitung Gaji">
              <i class="bi bi-calculator me-3 fs-5" style="color: #D4AF37;"></i>
              <span>Proses Hitung Gaji</span>
            </a>
          </li>
         
          <li class="sidebar-menu-item mb-1">
            <a href="index.php?page=terbit" class="sidebar-menu-link d-flex align-items-center px-3 py-2 rounded-3 text-decoration-none text-white transition-all <?= (isset($_GET['page']) && $_GET['page'] == 'terbit') ? 'active bg-burgundy' : ''; ?>" id="menu-terbit" title="Terbit Slip">
              <i class="bi bi-printer-fill me-3 fs-5" style="color: #D4AF37;"></i>
              <span>Terbit & Cetak Slip</span>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Sidebar Profile Card (Dynamic Footer) -->
    <div class="sidebar-profile p-3 border-top d-flex align-items-center" style="border-color: rgba(255, 255, 255, 0.1) !important;">
      <img src="assets/images/avatar.png" alt="Finance Officer" class="sidebar-profile-img rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;"
        onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=256&auto=format&fit=crop'">
      <div class="sidebar-profile-info overflow-hidden">
        <div class="sidebar-profile-name fw-bold text-white text-truncate" style="font-size: 14px;">Finance Officer</div>
        <div class="sidebar-profile-email text-muted text-truncate" style="font-size: 12px;">finance@email.com</div>
      </div>
    </div>
  </div>

  <!-- Style Tambahan untuk Active Menu Sidebar Burgundy -->
  <style>
  .sidebar-menu-link:hover {
    background-color: rgba(128, 0, 32, 0.4);
    color: #fff !important;
  }
  .sidebar-menu-link.active, .bg-burgundy {
    background-color: #800020 !important;
    color: #fff !important;
    box-shadow: 0 4px 10px rgba(128, 0, 32, 0.3);
  }
  </style>