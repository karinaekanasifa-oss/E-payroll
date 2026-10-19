<?php
session_start();
require_once '../database/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: '../../login/login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'] ?? null;
$employee_id = $_SESSION['employee_id'] ?? 1;

// Mengambil data detail karyawan & saldo gaji terakhir
$nama_karyawan = $_SESSION['nama'] ?? 'Karyawan';
$jabatan = 'Karyawan Tetap';
$saldo_gaji = 0;

try {
    // Ambil gaji bersih dari slip gaji terbaru
    $query = "SELECT total_gaji FROM payrolls WHERE employee_id = :employee_id AND status_cair = 'dibayar' ORDER BY id DESC LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":employee_id", $employee_id);
    $stmt->execute();
    $data_gaji = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data_gaji) {
        $saldo_gaji = $data_gaji['total_gaji'];
    }
} catch (PDOException $e) {
    $saldo_gaji = 0;
}

// Logika sapaan waktu
date_default_timezone_set('Asia/Jakarta');
$jam = date('G');
if ($jam >= 4 && $jam < 11) {
    $sapaan = "Selamat Pagi";
} elseif ($jam >= 11 && $jam < 15) {
    $sapaan = "Selamat Siang";
} elseif ($jam >= 15 && $jam < 18) {
    $sapaan = "Selamat Sore";
} else {
    $sapaan = "Selamat Malam";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan - E-Payroll System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-orange: #f97316;
            --dark-orange: #ea580c;
            --light-orange: #fff7ed;
            --accent-orange: #fdba74;
            --bg-color: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Navbar Clean Modern */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .navbar-brand {
            font-weight: 800;
            color: var(--text-main) !important;
            letter-spacing: -0.5px;
        }

        .navbar-nav .nav-link {
            color: var(--text-muted) !important;
            font-weight: 600;
            transition: all 0.25s ease;
            padding: 0.5rem 1rem !important;
            border-radius: 10px;
        }

        .navbar-nav .nav-link:hover, 
        .navbar-nav .nav-link.active {
            color: var(--dark-orange) !important;
            background-color: var(--light-orange);
        }

        /* Hero Banner Sunset Orange */
        .hero-section {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border: 1px solid #fed7aa;
            color: var(--text-main);
            border-radius: 1.5rem;
            padding: 2.25rem 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(249, 115, 22, 0.08);
            position: relative;
            overflow: hidden;
        }

        /* Profile Card Top */
        .user-profile-badge {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* Saldo Box Hero */
        .saldo-box {
            background: #ffffff;
            border: 1px solid #fed7aa;
            border-radius: 1.25rem;
            padding: 1rem 1.5rem;
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.06);
        }

        /* Stat Card Ringkasan */
        .stat-card {
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            background: #ffffff;
            padding: 1.25rem 1.5rem;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(249, 115, 22, 0.1);
            border-color: var(--accent-orange);
        }

        /* Card Menu Utama */
        .card-menu {
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            background: #ffffff;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .card-menu:hover {
            transform: translateY(-8px);
            border-color: var(--accent-orange);
            box-shadow: 0 20px 40px rgba(249, 115, 22, 0.12);
        }

        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.25rem;
            transition: transform 0.3s ease;
        }

        .card-menu:hover .icon-box {
            transform: scale(1.1) rotate(6deg);
        }

        /* Tombol Orange Gradient */
        .btn-orange-custom {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            border: none;
            font-weight: 700;
            border-radius: 50rem;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.25);
        }

        .btn-orange-custom:hover {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.35);
        }
    </style>
</head>
<body>

    <!-- Navbar Karyawan Lengkap dengan Bar Profil -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom sticky-top py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <div class="text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; background: linear-gradient(135deg, #f97316, #ea580c);">
                    <i class="fa-solid fa-user-tie fs-5"></i>
                </div>
                E-Payroll <span class="text-muted fw-normal small ms-2 d-none d-sm-inline">| Portal Karyawan</span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php"><i class="fa-solid fa-house me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="presensi/presensi.php"><i class="fa-solid fa-clock-rotate-left me-1"></i> Presensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="izin/izin.php"><i class="fa-solid fa-envelope-open-text me-1"></i> Izin / Cuti</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="slip_gaji/slip.php"><i class="fa-solid fa-file-invoice-dollar me-1"></i> Slip Gaji</a>
                    </li>

                    <!-- Profil User di Topbar -->
                    <li class="nav-item ms-lg-3 my-2 my-lg-0">
                        <div class="user-profile-badge">
                            <div class="user-avatar">
                                <?= strtoupper(substr($nama_karyawan, 0, 1)); ?>
                            </div>
                            <div class="lh-1">
                                <span class="d-block fw-bold text-dark small mb-1"><?= htmlspecialchars($nama_karyawan); ?></span>
                                <span class="badge bg-success bg-opacity-10 text-success p-1 fw-semibold" style="font-size: 0.68rem;">Online</span>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-semibold border-2" href="../../login/logout.php">
                            <i class="fa-solid fa-right-from-bracket me-1"></i> Keluar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content Wrapper -->
    <div class="container my-4">
        
        <!-- Hero Banner dengan Profil & Saldo Gaji (Emoticon #1: 👋) -->
        <div class="hero-section d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div class="mb-3 mb-lg-0">
                <span class="badge px-3 py-2 rounded-pill fw-bold mb-3 shadow-sm" style="background-color: #ffedd5; color: #ea580c; border: 1px solid #fed7aa;">
                    <i class="fa-solid fa-sun me-1"></i> <?= $sapaan; ?>
                </span>
                <h1 class="fw-extrabold mb-2 display-6 text-dark">Halo, <?= htmlspecialchars($nama_karyawan); ?>! 👋</h1>
                <p class="text-muted mb-0 lead" style="max-width: 580px; font-size: 0.98rem;">
                    Selamat datang di portal mandiri. Kelola kehadiran harian, perizinan, dan pantau rincian gaji kamu secara terpusat.
                </p>
            </div>

            <!-- Card Tampilan Saldo Gaji Karyawan -->
            <div class="d-flex flex-column flex-sm-row gap-3">
                <div class="saldo-box text-start text-sm-end">
                    <div class="text-muted small fw-semibold mb-1">
                        <i class="fa-solid fa-wallet text-warning me-1"></i> Saldo Gaji Diterima
                    </div>
                    <div class="fw-extrabold text-success fs-4">
                        Rp <?= number_format($saldo_gaji, 0, ',', '.'); ?>
                    </div>
                    <div class="text-muted" style="font-size: 0.72rem;">Periode Gaji Terakhir</div>
                </div>

                <div class="bg-white p-3 rounded-4 border shadow-sm text-center px-4 d-flex flex-column justify-content-center">
                    <i class="fa-solid fa-calendar-check fs-4 mb-1 text-warning"></i>
                    <span class="fw-bold text-dark small"><?= date('d M Y'); ?></span>
                    <div class="text-muted" style="font-size: 0.72rem;">Sistem Normal</div>
                </div>
            </div>
        </div>

        <!-- Section Ringkasan Informasi Cepat -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3 shadow-sm" style="background-color: #fff7ed; color: #ea580c; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-fingerprint fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Absensi Kehadiran</div>
                        <div class="fw-bold text-dark">Siap Rekam Shift</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3 shadow-sm" style="background-color: #fff1f2; color: #e11d48; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-envelope-circle-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Perizinan Kerja</div>
                        <div class="fw-bold text-dark">Pantau Status HRD</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3 shadow-sm" style="background-color: #f0fdf4; color: #16a34a; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-money-check-dollar fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Slip Gaji Bulanan</div>
                        <div class="fw-bold text-dark">Rincian & Unduh</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid Kartu Menu Utama Interaktif (Emoticon #2: 💵) -->
        <div class="row g-4">
            
            <!-- Menu 1: Presensi Harian -->
            <div class="col-md-4">
                <div class="card card-menu h-100 p-4">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-box shadow-sm" style="background-color: #fff7ed; color: #ea580c;">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Presensi Harian</h4>
                            <p class="text-muted small mb-4">Catat jam masuk dan jam keluar shift kerja kamu dengan praktis dan cepat langsung dari sini.</p>
                        </div>
                        <a href="presensi/presensi.php" class="btn btn-orange-custom w-100 text-center">
                            Buka Presensi <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Menu 2: Pengajuan Izin -->
            <div class="col-md-4">
                <div class="card card-menu h-100 p-4">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-box shadow-sm" style="background-color: #fff1f2; color: #e11d48;">
                                <i class="fa-solid fa-envelope-open-text"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Pengajuan Izin</h4>
                            <p class="text-muted small mb-4">Lagi berhalangan hadir atau mau ambil cuti? Langsung ajukan permohonan ke pihak HRD.</p>
                        </div>
                        <a href="izin/izin.php" class="btn btn-orange-custom w-100 text-center">
                            Ajukan Izin <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Menu 3: Slip Gaji Bulanan -->
            <div class="col-md-4">
                <div class="card card-menu h-100 p-4">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-box shadow-sm" style="background-color: #f0fdf4; color: #16a34a;">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Slip Gaji 💵</h4>
                            <p class="text-muted small mb-4">Lihat rincian penerimaan gaji bersih, tunjangan, potongan, dan cetak slip resmi kamu.</p>
                        </div>
                        <a href="slip_gaji/slip.php" class="btn btn-orange-custom w-100 text-center">
                            Lihat Slip Gaji <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 text-muted small mt-5 border-top bg-white shadow-sm">
        <div class="container">
            &copy; 2026 E-Payroll System. Hak cipta dilindungi undang-undang.
        </div>
    </footer>

    <!-- Bootstrap JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>