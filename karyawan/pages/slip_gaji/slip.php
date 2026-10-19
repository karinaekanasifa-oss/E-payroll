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
    $query = "SELECT p.*, e.nip, u.nama FROM payrolls p 
              JOIN employees e ON p.employee_id = e.id 
              JOIN users u ON e.user_id = u.id 
              WHERE p.employee_id = :employee_id ORDER BY p.id DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":employee_id", $employee_id);
    $stmt->execute();
    $data_gaji = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $data_gaji = [];
}

// Hitung total gaji dan slip cair
$total_gaji_diterima = 0;
$total_slip = count($data_gaji);
foreach ($data_gaji as $g) {
    if (strtolower($g['status_cair'] ?? '') == 'dibayar') {
        $total_gaji_diterima += ($g['total_gaji'] ?? 0);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Slip Gaji - E-Payroll System</title>
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

        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .navbar-brand { font-weight: 800; color: var(--text-main) !important; letter-spacing: -0.5px; }

        .header-card {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border: 1px solid #fed7aa;
            border-radius: 1.5rem;
            padding: 2rem 2.25rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(249, 115, 22, 0.08);
        }

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

        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .table thead { background-color: #f8fafc; border-bottom: 2px solid #f1f5f9; }
        .table thead th {
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            padding: 1rem;
        }

        .table tbody tr:hover { background-color: #fff7ed; }

        .btn-orange-custom {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            border: none;
            font-weight: 700;
            border-radius: 50rem;
            padding: 0.4rem 1.2rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.25);
            font-size: 0.85rem;
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

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../dashboard.php">
                <div class="text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; background: linear-gradient(135deg, #f97316, #ea580c);">
                    <i class="fa-solid fa-file-invoice-dollar fs-5"></i>
                </div>
                E-Payroll <span class="text-muted fw-normal small ms-2 d-none d-sm-inline">| Slip Gaji Karyawan</span>
            </a>
            <a href="../dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                <i class="fa-solid fa-house me-1"></i> Dashboard
            </a>
        </div>
    </nav>

    <!-- Content -->
    <div class="container my-5">

        <!-- Header Hero -->
        <div class="header-card d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-3 mb-md-0">
                <span class="badge px-3 py-2 rounded-pill fw-bold mb-2 shadow-sm" style="background-color: #ffedd5; color: #ea580c; border: 1px solid #fed7aa;">
                    <i class="fa-solid fa-wallet me-1"></i> Transparansi Gaji
                </span>
                <h2 class="fw-extrabold text-dark mb-1">Riwayat Slip Gaji Bulanan 💵</h2>
                <p class="text-muted mb-0">Pantau rincian gaji, potongan, dan cetak slip gaji kamu kapan saja.</p>
            </div>
            <div>
                <div class="bg-white p-3 rounded-4 border shadow-sm text-center">
                    <span class="text-muted small d-block">Status Pembayaran</span>
                    <span class="badge bg-success px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-circle-check me-1"></i> Terverifikasi</span>
                </div>
            </div>
        </div>

        <!-- 3 Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="stat-card d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3 shadow-sm" style="background-color: #f0fdf4; color: #16a34a; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-hand-holding-dollar fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Gaji Diterima (Pencairan)</div>
                        <div class="fw-bold text-dark fs-5">Rp <?= number_format($total_gaji_diterima, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card d-flex align-items-center">
                    <div class="rounded-circle p-3 me-3 shadow-sm" style="background-color: #fff7ed; color: #ea580c; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-receipt fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Jumlah Terbitan Slip Gaji</div>
                        <div class="fw-bold text-dark fs-5"><?= $total_slip; ?> Dokumen</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card card-custom p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Bulan & Tahun</th>
                            <th>Total Potongan</th>
                            <th>Total Gaji Diterima</th>
                            <th class="text-center">Status Pencairan</th>
                            <th class="text-center">Aksi Slip</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data_gaji) > 0): ?>
                            <?php $no = 1; foreach ($data_gaji as $row): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                <td class="fw-bold text-dark">
                                    <i class="fa-regular fa-calendar-check me-2 text-warning" style="color: #f97316 !important;"></i>
                                    <?= htmlspecialchars($row['bulan_tahun']); ?>
                                </td>
                                <td class="text-danger fw-semibold">
                                    - Rp <?= number_format($row['potongan'], 0, ',', '.'); ?>
                                </td>
                                <td class="text-success fw-bold fs-6">
                                    Rp <?= number_format($row['total_gaji'], 0, ',', '.'); ?>
                                </td>
                                <td class="text-center">
                                    <?php if (strtolower($row['status_cair']) == 'dibayar'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-2 rounded-pill fw-semibold">
                                            <i class="fa-solid fa-check me-1"></i> Dibayar
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-3 py-2 rounded-pill fw-semibold" style="color: #ea580c !important;">
                                            <i class="fa-solid fa-clock me-1"></i> Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="cetak.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-orange-custom">
                                        <i class="fa-solid fa-print me-1"></i> Cetak Slip
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Empty" style="width: 80px;" class="mb-3 opacity-50"><br>
                                    <span class="text-muted fw-semibold">Belum ada dokumen slip gaji yang diterbitkan.</span>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>