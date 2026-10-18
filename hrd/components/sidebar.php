<?php
// Pastikan variabel $page terbaca dari URL jika belum ada
$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<div class="sidebar-wrapper" id="sidebar">
    <!-- Brand Logo / Identity -->
    <a href="index.html" class="sidebar-brand">
      <i class="bi-building-fill"></i>
      <span>E-Payroll HRD</span>
    </a>

    <!-- Navigation Menu -->
    <div class="flex-grow-1 overflow-y-auto">
      <!-- Group: Menu -->
      <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Menu</div>
        <ul class="sidebar-menu-list">
          <li class="sidebar-menu-item">
            <a href="index.php?page=dashboard" class="sidebar-menu-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>" id="menu-overview" title="Overview">
              <i class="bi bi-grid-fill"></i>
              <span>Dashboard</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Group: Components -->
      <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Components</div>
        <ul class="sidebar-menu-list">
          <li class="sidebar-menu-item">
            <a href="index.php?page=jabatan" class="sidebar-menu-link <?php echo ($current_page == 'jabatan' || $current_page == 'tambah-jabatan' || $current_page == 'edit-jabatan') ? 'active' : ''; ?>" id="menu-basictables" title="Basic Tables">
              <i class="bi bi-briefcase-fill"></i>
              <span>Jabatan</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="index.php?page=karyawan" class="sidebar-menu-link <?php echo ($current_page == 'karyawan') ? 'active' : ''; ?>" id="menu-uiforms" title="Forms and Input">
              <i class="bi bi-person-lines-fill"></i>
              <span>Karyawan</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="index.php?page=shift" class="sidebar-menu-link <?php echo ($current_page == 'shift') ? 'active' : ''; ?>" id="menu-uiforms" title="Forms and Input">
              <i class="bi bi-clock-fill"></i>
              <span>Shift</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="index.php?page=absensi" class="sidebar-menu-link <?php echo ($current_page == 'absensi') ? 'active' : ''; ?>" id="menu-uibuttons" title="Buttons">
              <i class="bi bi-calendar-check-fill"></i>
              <span>Rekap Absensi</span>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Sidebar Profile Card (Dynamic Footer) -->
  <!-- Bagian Profil User di Sidebar Bawah -->
<div class="sidebar-profile-box p-3 m-3 rounded-3 border border-secondary border-opacity-25 bg-dark bg-opacity-50">
    <div class="d-flex align-items-center">
        <div class="avatar-initial rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center me-2 shadow-sm flex-shrink-0" style="width: 40px; height: 40px; font-size: 15px;">
            <?= htmlspecialchars($user_inisial ?? 'A'); ?>
        </div>
        <div class="overflow-hidden">
            <h6 class="mb-0 text-white fw-bold text-truncate" style="font-size: 0.9rem;"><?= htmlspecialchars($user_nama ?? 'Administrator'); ?></h6>
            <span class="text-muted text-truncate d-block" style="font-size: 0.75rem;"><?= htmlspecialchars($user_email ?? 'admin@email.com'); ?></span>
        </div>
    </div>
</div>
</div>