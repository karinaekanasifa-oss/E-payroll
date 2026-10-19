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

if (isset($_POST['submit_izin'])) {
    $judul = trim($_POST['judul']);
    $pesan = trim($_POST['pesan']);

    if (!empty($judul) && !empty($pesan)) {
        $query = "INSERT INTO notifications (employee_id, judul, pesan, status_baca) 
                  VALUES (:employee_id, :judul, :pesan, 'belum')";
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(":employee_id", $employee_id);
        $stmt->bindParam(":judul", $judul);
        $stmt->bindParam(":pesan", $pesan);

        if ($stmt->execute()) {
            echo "<script>alert('Pengajuan izin berhasil dikirim!'); window.location='izin.php';</script>";
        } else {
            echo "<script>alert('Gagal mengirim pengajuan izin.');</script>";
        }
    } else {
        echo "<script>alert('Semua kolom wajib diisi!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan Izin / Cuti - E-Payroll System</title>
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
            --bg-color: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
        }

        /* Navbar Clean Modern */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        .btn-orange-custom {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            border: none;
            font-weight: 700;
            border-radius: 50rem;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.25);
        }

        .btn-orange-custom:hover {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.35);
        }

        .form-control:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 0.25rem rgba(249, 115, 22, 0.15);
        }
    </style>
</head>
<body>

    <!-- Navbar Karyawan Clean -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="izin.php">
                <div class="text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; background: linear-gradient(135deg, #f97316, #ea580c);">
                    <i class="fa-solid fa-paper-plane fs-5"></i>
                </div>
                E-Payroll <span class="text-muted fw-normal small ms-2">| Form Pengajuan Izin</span>
            </a>
            <a href="izin.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </nav>

    <!-- Form Container Card -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card card-custom p-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle p-3 me-3" style="background-color: #fff7ed; color: #ea580c;">
                                <i class="fa-solid fa-pen-to-square fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Formulir Pengajuan Izin / Cuti</h4>
                                <p class="text-muted small mb-0">Isi permohonan kamu di bawah ini secara lengkap.</p>
                            </div>
                        </div>
                        <hr class="my-4 text-muted opacity-25">

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="judul" class="form-label fw-bold text-dark">Judul / Perihal Izin</label>
                                <input type="text" name="judul" id="judul" class="form-control py-2 rounded-3" placeholder="Contoh: Permohonan Izin Sakit / Cuti Tahunan" required>
                            </div>

                            <div class="mb-4">
                                <label for="pesan" class="form-label fw-bold text-dark">Keterangan / Alasan</label>
                                <textarea name="pesan" id="pesan" rows="4" class="form-control rounded-3" placeholder="Tuliskan alasan lengkap permohonan izin kamu di sini..." required></textarea>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2">
                                <a href="izin.php" class="btn btn-light rounded-pill px-4 fw-semibold text-muted">Batal</a>
                                <button type="submit" name="submit_izin" class="btn btn-orange-custom">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Kirim Pengajuan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>