<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Payroll - Portal Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-white">

    <!-- Navbar Visitor -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fa-solid fa-building-user me-2"></i>E-Payroll System</a>
            <div class="ms-auto">
                <a href="login/login.php" class="btn btn-light text-primary fw-bold px-4">Login Karyawan</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section / Halaman Visitor -->
    <div class="container my-5">
        <div class="row align-items-center py-5">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold text-dark mb-3">Portal Layanan Mandiri Karyawan</h1>
                <p class="lead text-muted mb-4">Kelola presensi harian, ajukan izin atau cuti, serta unduh slip gaji bulanan Anda dengan mudah, cepat, dan transparan dalam satu genggaman.</p>
                <a href="login/login.php" class="btn btn-primary btn-lg px-4 me-2 shadow-sm"><i class="fa-solid fa-right-to-bracket me-2"></i>Masuk ke Akun</a>
            </div>
            <div class="col-lg-6 text-center">
                <div class="p-5 bg-light rounded-4 shadow-sm border">
                    <i class="fa-solid fa-users-gear text-primary display-1 mb-3"></i>
                    <h3 class="fw-semibold">E-Payroll Karyawan</h3>
                    <p class="text-muted small">Silakan login menggunakan akun yang telah didaftarkan oleh bagian HRD perusahaan.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Sederhana -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <small>&copy; 2026 E-Payroll Project. All rights reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>