<?php
// Pastikan variabel user aman dari XSS
$user_nama = htmlspecialchars($_SESSION['nama'] ?? 'Karyawan', ENT_QUOTES, 'UTF-8');
$user_email = htmlspecialchars($_SESSION['email'] ?? 'karyawan@email.com', ENT_QUOTES, 'UTF-8');
$user_role = htmlspecialchars($_SESSION['role'] ?? 'karyawan', ENT_QUOTES, 'UTF-8');
?>

<!-- Topbar Start -->
<div class="container-fluid px-0 d-none d-lg-block">
    <div class="row gx-0">
        <div class="col-lg-4 text-center bg-light py-3">
            <div class="d-inline-flex align-items-center justify-content-center">
                <i class="bi bi-shield-check fs-1 text-primary me-3"></i>
                <div class="text-start">
                    <h6 class="mb-1">Keamanan Sistem</h6>
                    <span class="small text-muted">Sesi Terenkripsi & CSRF Active</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 text-center bg-white border-inner py-3">
            <div class="d-inline-flex align-items-center justify-content-center">
                <a href="index.php?page=dashboard" class="navbar-brand">
                    <h1 class="m-0 text-primary"><i class="bi bi-wallet2 fs-1 me-3"></i>E-Payroll</h1>
                </a>
            </div>
        </div>
        <div class="col-lg-4 text-center bg-light py-3">
            <div class="d-inline-flex align-items-center justify-content-center">
                <i class="bi bi-person-circle fs-1 text-primary me-3"></i>
                <div class="text-start">
                    <h6 class="mb-1"><?= $user_nama; ?></h6>
                    <span class="small text-muted text-uppercase fw-bold"><?= $user_role; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Topbar End -->