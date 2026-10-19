<?php
/**
 * --------------------------------------------------------------------------
 * SECURE & MODERN SPLIT-SCREEN LOGIN PAGE - E-PAYROLL SYSTEM
 * --------------------------------------------------------------------------
 */

require_once 'classes/auth.php';

$auth = new Auth();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email      = $_POST['email'] ?? '';
    $password   = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    $error = $auth->login($email, $password, $csrf_token);
}

// Ambil token CSRF untuk form
$csrf_token = $auth->generateCsrfToken();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Payroll System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
        }
        html, body {
            height: 100vh;
            width: 100vw;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ffffff;
        }

        .login-wrapper {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        /* SISI KIRI: Animasi Gradasi Bergerak & Estetik */
        .login-left {
            flex: 1.25;
            background: linear-gradient(-45deg, #700018, #990033, #4a0012, #240006);
            background-size: 400% 400%;
            animation: gradientAnimation 14s ease infinite;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 4rem 5rem;
            position: relative;
            overflow: hidden;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Efek Cahaya Orb Mengalir */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.35;
            animation: orbFloat 10s infinite alternate ease-in-out;
        }
        .orb-1 {
            width: 350px;
            height: 350px;
            background: #ff3366;
            top: -100px;
            left: -100px;
        }
        .orb-2 {
            width: 450px;
            height: 450px;
            background: #ff9900;
            bottom: -150px;
            right: -100px;
            animation-delay: 5s;
        }

        @keyframes orbFloat {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(30px) scale(1.08); }
        }

        /* Pilar Info Card di Sisi Kiri */
        .pilar-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 2rem;
            z-index: 5;
        }
        .pilar-box {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 1rem;
            border-radius: 14px;
            transition: transform 0.3s ease;
        }
        .pilar-box:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.15);
        }

        /* SISI KANAN: Form Login Full Clean */
        .login-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            background: #ffffff;
            z-index: 2;
        }

        .login-box {
            width: 100%;
            max-width: 410px;
            animation: fadeInSmooth 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeInSmooth {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-icon {
            width: 68px;
            height: 68px;
            background: rgba(128, 0, 32, 0.08);
            color: #800020;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            margin-bottom: 1.25rem;
            box-shadow: 0 10px 25px rgba(128, 0, 32, 0.08);
            animation: pulseGlow 3s infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(128, 0, 32, 0.2); }
            50% { transform: scale(1.03); box-shadow: 0 0 0 12px rgba(128, 0, 32, 0); }
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .form-control {
            border-radius: 12px;
            padding: 0.8rem 1rem;
            border: 1.5px solid #cbd5e1;
            font-size: 0.95rem;
            background-color: #f8fafc;
            transition: all 0.25s ease;
        }

        .form-control:focus {
            border-color: #800020;
            box-shadow: 0 0 0 4px rgba(128, 0, 32, 0.1);
            background-color: #ffffff;
        }

        .input-group-text {
            border-radius: 12px 0 0 12px;
            border: 1.5px solid #cbd5e1;
            border-right: none;
            background-color: #f8fafc;
            color: #64748b;
        }

        .input-group .form-control {
            border-radius: 0 12px 12px 0;
        }

        .btn-burgundy {
            background-color: #800020;
            color: #ffffff;
            font-weight: 700;
            padding: 0.85rem;
            border-radius: 12px;
            border: none;
            transition: all 0.25s ease;
            box-shadow: 0 6px 15px rgba(128, 0, 32, 0.25);
        }

        .btn-burgundy:hover {
            background-color: #600018;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(128, 0, 32, 0.35);
        }

        @media (max-width: 992px) {
            .login-left { display: none; }
            .login-right { flex: 1; height: 100vh; }
            body, html { overflow: auto; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <!-- Sisi Kiri: Branding Dinamis & Indikator Pilar Sistem -->
    <div class="login-left">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>

        <div style="z-index: 5;">
            <h1 class="display-3 fw-extrabold mb-3" style="letter-spacing: -1px;">E-Payroll System</h1>
            <p class="fs-5 text-white-50 lh-base" style="max-width: 520px;">
                Platform manajemen terpadu untuk efisiensi operasional pegawai, kalkulasi gaji, dan transparansi finansial perusahaan.
            </p>

            <!-- Card Indikator Modul -->
            <div class="pilar-container">
                <div class="pilar-box">
                    <i class="bi bi-people-fill text-info fs-5 mb-1 d-block"></i>
                    <div class="fw-bold text-white small">HRD</div>
                    <div class="text-white-50" style="font-size: 0.72rem;">Manajemen Pegawai</div>
                </div>
                <div class="pilar-box">
                    <i class="bi bi-wallet2 text-warning fs-5 mb-1 d-block"></i>
                    <div class="fw-bold text-white small">Finance</div>
                    <div class="text-white-50" style="font-size: 0.72rem;">Kalkulasi & Gaji</div>
                </div>
                <div class="pilar-box">
                    <i class="bi bi-person-badge-fill text-success fs-5 mb-1 d-block"></i>
                    <div class="fw-bold text-white small">Karyawan</div>
                    <div class="text-white-50" style="font-size: 0.72rem;">Portal & Slip Gaji</div>
                </div>
            </div>
        </div>

        <div style="z-index: 5;" class="text-white-50 small">
            &copy; 2026 E-Payroll Indonesia. Seluruh hak cipta dilindungi.
        </div>
    </div>

    <!-- Sisi Kanan: Form Login -->
    <div class="login-right">
        <div class="login-box">
            <div class="text-center mb-4">
                <div class="brand-icon">
                    <i class="bi bi-shield-lock-fill fs-3"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1">Selamat Datang</h3>
                <p class="text-muted small">Silakan masuk menggunakan kredensial akun Anda</p>
            </div>

            <!-- Notifikasi Error -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 small text-center rounded-3 shadow-sm mb-3 border-0 bg-danger bg-opacity-10 text-danger fw-semibold" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form action="" method="POST" autocomplete="off">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control shadow-none" id="email" name="email" placeholder="nama@email.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control shadow-none" id="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-burgundy w-100 shadow-sm">
                    Masuk Sistem <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>