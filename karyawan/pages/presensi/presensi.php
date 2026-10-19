<?php
session_start();
require_once '../../database/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../login/login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$employee_id = $_SESSION['employee_id'] ?? 1;

try {
    // Kueri mengambil data presensi karyawan
    $query = "SELECT * FROM attedances WHERE employee_id = :employee_id ORDER BY tanggal DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":employee_id", $employee_id);
    $stmt->execute();
    $data_presensi = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $data_presensi = [];
}

// Menghitung statistik sederhana untuk ringkasan card
$total_hadir = 0;
$total_izin  = 0;
$total_sakit = 0;

foreach ($data_presensi as $row) {
    $st = strtolower($row['status'] ?? '');
    if ($st == 'hadir') $total_hadir++;
    elseif ($st == 'izin') $total_izin++;
    elseif ($st == 'sakit') $total_sakit++;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Presensi - E-Payroll System</title>
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

        /* Header Card Hero */
        .header-card {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border: 1px solid #fed7aa;
            border-radius: 1.5rem;
            padding: 2rem 2.25rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(249, 115, 22, 0.08);
        }

        /* Stat Card Ringkasan Presensi */
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

        /* Table Card Container */
        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .table thead {
            background-color: #f8fafc;
            border-bottom: 2px solid #f1f5f9;
        }

        .table thead th {
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #fff7ed;
        }

        /* Tombol Orange Custom */
        .btn-orange-custom {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            border: none;
            font-weight: 700;
            border-radius: 50rem;
            padding: 0.65rem 1.5rem;
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

    <!-- Navbar Karyawan -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../dashboard.php">
                <div class="text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; background: linear-gradient(135deg, #f97316, #ea580c);">
                    <i class="fa-solid fa-clock-rotate-left fs-5"></i>
                </div>
                E-Payroll <span class="text-muted fw-normal small ms-2 d-none d-sm-inline">| Rekam Presensi</span>
            </a>
            <a href="../dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                <i class="fa-solid fa-house me-1"></i> Dashboard
            </a>
        </div>
    </nav>

    <!-- Content Wrapper -->
    <div class="container my-5">

        <!-- Header Section -->
        <div class="header-card d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-3 mb-md-0">
                <span class="badge px-3 py-2 rounded-pill fw-bold mb-2 shadow-sm" style="background-color: #ffedd5; color: #ea580c; border: 1px solid #fed7aa;">
                    <i class="fa-solid fa-fingerprint me-1"></i> Kehadiran Real-time
                </span>
                <h2 class="fw-extrabold text-dark mb-1">Riwayat Presensi Harian 🕒</h2>
                <p class="text-muted mb-0">Pantau catatan jam masuk, jam keluar, dan status kehadiran kamu di sini.</p>
            </div>
            <div>
                <a href="input.php" class="btn btn-orange-custom shadow">
                    <i class="fa-solid fa-plus me-1"></i> Input Presensi Baru
                </a>
            </div>
        </div>

        <!-- 3 Stat Card Ringkasan Presensi -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3 text-success shadow-sm" style="background-color: #f0fdf4; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-user-check fs-4" style="color: #16a34a;"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Hadir</div>
                        <div class="fw-bold text-dark fs-5"><?= $total_hadir; ?> Hari</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3 text-warning shadow-sm" style="background-color: #fff7ed; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-user-clock fs-4" style="color: #ea580c;"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Izin & Cuti</div>
                        <div class="fw-bold text-dark fs-5"><?= $total_izin; ?> Hari</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3 text-danger shadow-sm" style="background-color: #fff1f2; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-briefcase-medical fs-4" style="color: #e11d48;"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Sakit</div>
                        <div class="fw-bold text-dark fs-5"><?= $total_sakit; ?> Hari</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card Data Presensi -->
        <div class="card card-custom p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Tanggal Hari</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th class="text-center">Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data_presensi) > 0): ?>
                            <?php $no = 1; foreach ($data_presensi as $row): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                <td class="fw-semibold text-dark">
                                    <i class="fa-regular fa-calendar me-2 text-warning" style="color: #f97316 !important;"></i>
                                    <?= htmlspecialchars($row['tanggal']); ?>
                                </td>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.85rem;">
                                        <i class="fa-solid fa-right-to-bracket me-1"></i> <?= htmlspecialchars($row['jam_masuk']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($row['jam_keluar']) && $row['jam_keluar'] != '00:00:00'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.85rem;">
                                            <i class="fa-solid fa-right-from-bracket me-1"></i> <?= htmlspecialchars($row['jam_keluar']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small italic">Belum Absen Keluar</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $status = strtolower($row['status']);
                                        $badge_class = 'bg-secondary';
                                        if ($status == 'hadir') $badge_class = 'bg-success';
                                        elseif ($status == 'izin') $badge_class = 'bg-warning text-dark';
                                        elseif ($status == 'sakit') $badge_class = 'bg-danger';
                                        elseif ($status == 'alfa') $badge_class = 'bg-dark';
                                    ?>
                                    <span class="badge <?= $badge_class; ?> px-3 py-2 rounded-pill fw-semibold text-capitalize">
                                        <?= htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Empty" style="width: 80px;" class="mb-3 opacity-50"><br>
                                    <span class="text-muted fw-semibold">Belum ada riwayat presensi yang tercatat.</span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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