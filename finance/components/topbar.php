<?php
// Pastikan sesi aktif untuk membaca data user yang sedang login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil data nama dan role dari sesi login, berikan nilai default jika kosong
$nama_user = $_SESSION['nama'] ?? 'Pengguna';
$role_user = ucfirst($_SESSION['role'] ?? 'User');
$email_user = $_SESSION['email'] ?? 'user@email.com';
?>

<header class="navbar-custom">
      <div class="navbar-left">
        <!-- Desktop sidebar toggle -->
        <button class="btn-desktop-toggle d-none d-xl-flex align-items-center justify-content-center me-3"
          id="desktop-sidebar-toggle" aria-label="Minimize Sidebar">
          <i class="bi bi-chevron-bar-left"></i>
        </button>
        <!-- Mobile sidebar toggle -->
        <button class="sidebar-toggle-btn me-2" id="sidebar-toggle" aria-label="Toggle Navigation">
          <i class="bi bi-list"></i>
        </button>
      </div>

      <!-- Right actions -->
      <div class="navbar-actions">
        <!-- Notifications Dropdown -->
        <div class="dropdown">
          <button class="navbar-action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
            aria-expanded="false" id="btn-notifications" data-bs-auto-close="outside">
            <i class="bi bi-bell"></i>
            <span class="navbar-action-badge">1</span>
          </button>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-notification p-0"
            aria-labelledby="btn-notifications">
            <div class="notification-header">
              <h6 class="notification-title">Notifikasi Sistem</h6>
              <button class="btn-clear-all" type="button">Aktif</button>
            </div>
            <div class="notification-list">
              <!-- Notifikasi Dinamis Sesi Login -->
              <a href="#" class="notification-item">
                <div class="notification-icon bg-success text-white">
                  <i class="bi bi-shield-check"></i>
                </div>
                <div class="notification-content">
                  <p class="notification-text">Login sebagai: <strong><?= htmlspecialchars($nama_user); ?></strong></p>
                  <span class="notification-time">Akses Level: <?= htmlspecialchars($role_user); ?></span>
                </div>
                <span class="notification-unread-dot"></span>
              </a>
            </div>
            <a href="#" class="notification-footer">E-Payroll Security Active</a>
          </div>
        </div>

        <!-- Profile Dropdown (Dinamis Sesuai Akun Login) -->
        <div class="dropdown ms-2">
          <button class="navbar-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
            aria-expanded="false" id="profile-dropdown">
            <img src="template/assets/images/avatar.png" alt="Profile Image" class="navbar-profile-img">
            <span class="navbar-profile-name d-none d-md-inline"><?= htmlspecialchars($nama_user); ?></span>
            <i class="bi bi-chevron-down navbar-profile-caret"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-profile" aria-labelledby="profile-dropdown">
            <li class="dropdown-header">Welcome, <?= htmlspecialchars($role_user); ?> !</li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> <?= htmlspecialchars($email_user); ?></a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Settings</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <!-- Tombol Logout Mengarah ke File Logout yang Berfungsi Menghapus Sesi -->
           <li><a class="dropdown-item text-danger" href="../login/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
          </ul>
        </div>
      </div>
    </header>