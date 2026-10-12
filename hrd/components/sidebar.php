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
    <div class="sidebar-profile">
      <img src="template/assets/images/avatar.png" alt="Administrator" class="sidebar-profile-img"
        onerror="this.src='https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=256&auto=format&fit=crop'">
      <div class="sidebar-profile-info">
        <div class="sidebar-profile-name">Administrator</div>
        <div class="sidebar-profile-email">admin@email.com</div>
      </div>
    </div>
</div>