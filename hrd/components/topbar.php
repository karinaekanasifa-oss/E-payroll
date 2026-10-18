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
    
    <!-- Lonceng Notifikasi Sistem -->
    <div class="dropdown me-2">
      <button class="navbar-action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
        aria-expanded="false" id="btn-notifications" data-bs-auto-close="outside">
        <i class="bi bi-bell"></i>
        <span class="navbar-action-badge bg-danger"></span>
      </button>
      <div class="dropdown-menu dropdown-menu-end dropdown-menu-notification p-0 shadow border-0"
        aria-labelledby="btn-notifications">
        <div class="notification-header bg-light d-flex justify-content-between align-items-center p-3 border-bottom">
          <h6 class="notification-title mb-0 fw-bold">Notifikasi Sistem</h6>
          <span class="badge bg-success bg-opacity-10 text-success rounded-pill small">3 Baru</span>
        </div>
        <div class="notification-list p-2" style="max-height: 280px; overflow-y: auto;">
          <a href="#" class="notification-item d-flex align-items-center p-2 text-decoration-none rounded hover-bg-light">
            <div class="notification-icon bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
              <i class="bi bi-person-check-fill"></i>
            </div>
            <div class="notification-content">
              <p class="notification-text mb-0 small text-dark">Data pegawai berhasil diperbarui secara sinkron.</p>
              <span class="notification-time text-muted" style="font-size: 0.75rem;">Baru saja</span>
            </div>
          </a>
          <a href="#" class="notification-item d-flex align-items-center p-2 text-decoration-none rounded hover-bg-light">
            <div class="notification-icon bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
              <i class="bi bi-shield-check"></i>
            </div>
            <div class="notification-content">
              <p class="notification-text mb-0 small text-dark">Sesi terenkripsi CSRF & rate limit aktif.</p>
              <span class="notification-time text-muted" style="font-size: 0.75rem;">10 menit yang lalu</span>
            </div>
          </a>
        </div>
      </div>
    </div>

    <!-- Profil User Akun Aktif -->
    <div class="dropdown">
      <button class="navbar-profile-btn dropdown-toggle d-flex align-items-center bg-transparent border-0" type="button" data-bs-toggle="dropdown"
        aria-expanded="false" id="profile-dropdown">
        <div class="avatar-initial rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 36px; height: 36px; font-size: 14px;">
            <?= htmlspecialchars($user_inisial ?? 'A'); ?>
        </div>
        <div class="text-start d-none d-md-block">
          <span class="navbar-profile-name fw-bold d-block lh-1 text-dark" style="font-size: 0.88rem;"><?= htmlspecialchars($user_nama ?? 'Administrator'); ?></span>
          <span class="text-uppercase text-muted" style="font-size: 0.68rem; font-weight: 700;"><?= htmlspecialchars($user_role ?? 'HRD'); ?></span>
        </div>
        <i class="bi bi-chevron-down navbar-profile-caret ms-2 text-muted"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-profile shadow-sm border-0 rounded-3 mt-2" aria-labelledby="profile-dropdown">
        <li class="dropdown-header border-bottom pb-2 mb-1">
          <div class="fw-bold text-dark"><?= htmlspecialchars($user_nama ?? 'Administrator'); ?></div>
          <div class="text-muted small"><?= htmlspecialchars($user_email ?? 'admin@email.com'); ?></div>
        </li>
        <li><a class="dropdown-item text-danger py-2 fw-semibold" href="../login/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
      </ul>
    </div>

  </div>
</header>