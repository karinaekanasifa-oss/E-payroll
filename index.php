<?php
/**
 * --------------------------------------------------------------------------
 * MAIN INDEX KARYAWAN MODULE - E-PAYROLL SYSTEM
 * --------------------------------------------------------------------------
 */

// Panggil Auth dari dalam folder login/classes/
require_once 'login/classes/auth.php';
$auth = new Auth();

// Wajibkan user login & batasi hanya role 'karyawan'
$auth->requireLogin();
$auth->restrictRole('karyawan');

// Ambil data session
$user_id      = $_SESSION['user_id'] ?? 0;
$employee_id  = $_SESSION['employee_id'] ?? 0;
$user_nama    = htmlspecialchars($_SESSION['nama'] ?? 'Karyawan', ENT_QUOTES, 'UTF-8');
$user_email   = htmlspecialchars($_SESSION['email'] ?? 'karyawan@email.com', ENT_QUOTES, 'UTF-8');
$user_role    = htmlspecialchars($_SESSION['role'] ?? 'karyawan', ENT_QUOTES, 'UTF-8');

// Tangkap parameter halaman dari URL (default ke 'dashboard')
$page = isset($_GET['page']) ? $_GET['page'] : "dashboard";
?>
<!DOCTYPE html>
<html lang="id">

<?php include 'karyawan/partials/head.php'; ?>

<body>
    <!-- Spinner Start -->
    <?php include 'karyawan/components/spinner.php'; ?>
    <!-- Spinner End -->

    <!-- Topbar Start -->
    <?php include 'karyawan/components/topbar.php'; ?>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <?php include 'karyawan/components/navbar.php'; ?>
    <!-- Navbar End -->

    <!-- MAIN CONTENT SWITCHER -->
    <?php
    switch ($page) {
        case 'dashboard':
            include 'karyawan/pages/dashboard.php';
            break;
        case 'presensi':
            include 'karyawan/pages/presensi/presensi.php';
            break;
        case 'izin':
            include 'karyawan/pages/presensi/izin.php';
            break;
    }
    ?>

    <!-- Footer Start -->
    <?php include 'karyawan/components/footer.php'; ?>
    <!-- Footer End -->

    <?php include 'karyawan/partials/script.php'; ?>
</body>
</html>