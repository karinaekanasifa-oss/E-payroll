<?php
/**
 * --------------------------------------------------------------------------
 * SECURE LOGIN PAGE - E-PAYROLL SYSTEM
 * --------------------------------------------------------------------------
 */

require_once 'classes/Auth.php';

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
    <title>Login - E-Payroll System (Secure)</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
        }
        .btn-burgundy {
            background-color: #800020;
            color: #fff;
        }
        .btn-burgundy:hover {
            background-color: #600018;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card login-card mx-auto p-4 bg-white">
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color: #800020;">E-PAYROLL</h3>
            <p class="text-muted small">Autentikasi Aman & Terenkripsi</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small text-center" role="alert">
                <i class="bi bi-shield-exclamation me-1"></i> <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" autocomplete="off">
            <!-- CSRF Token Hidden Input -->
            <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

            <div class="mb-3">
                <label for="email" class="form-label small fw-bold text-muted">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" class="form-control bg-light border-start-0 shadow-none" id="email" name="email" placeholder="nama@email.com" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small fw-bold text-muted">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" class="form-control bg-light border-start-0 shadow-none" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-burgundy w-100 py-2 rounded-3 fw-bold shadow-sm">
                Masuk Sistem <i class="bi bi-shield-lock-fill ms-1"></i>
            </button>
        </form>

        <div class="text-center mt-4 text-muted small">
            &copy; 2026 E-Payroll Indonesia. Secure Enterprise System.
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>