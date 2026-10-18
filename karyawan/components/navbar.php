<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top py-4 px-4 px-lg-5 shadow-sm">
    <a href="index.php?page=dashboard" class="navbar-brand d-block d-lg-none">
        <h1 class="m-0 text-uppercase text-primary"><i class="bi bi-wallet2 fs-1 me-3"></i>E-Payroll</h1>
    </a>
    
    <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav mx-auto bg-light py-3 py-lg-0 px-4 rounded-pill">
            <a href="index.php?page=dashboard" class="nav-item nav-link <?= ($page == 'dashboard') ? 'active' : ''; ?>">Dashboard</a>
            <a href="index.php?page=presensi" class="nav-item nav-link <?= ($page == 'presensi') ? 'active' : ''; ?>">Presensi Kehadiran</a>
            <a href="index.php?page=izin" class="nav-item nav-link <?= ($page == 'izin') ? 'active' : ''; ?>">Pengajuan Izin/Cuti</a>
            <a href="index.php?page=slip-gaji" class="nav-item nav-link <?= ($page == 'slip-gaji') ? 'active' : ''; ?>">Slip Gaji</a>
        </div>
    </div>
    
    <div class="d-none d-lg-block text-end">
        <a href="../login/logout.php" class="btn btn-outline-danger px-4 rounded-pill" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
    </div>
</nav>
<!-- Navbar End -->