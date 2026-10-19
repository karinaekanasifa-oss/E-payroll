<?php
/**
 * --------------------------------------------------------------------------
 * MAIN INDEX HRD MODULE - E-PAYROLL SYSTEM
 * --------------------------------------------------------------------------
 */

// Panggil Auth untuk proteksi login & session management
require_once '../login/classes/auth.php';
$auth = new Auth();

// Wajibkan user login & batasi hanya role 'hrd' yang bisa masuk
$auth->requireLogin();
$auth->restrictRole('hrd');

// Ambil data user aktif dari Session
$user_nama     = $_SESSION['nama'] ?? 'Administrator';
$user_email    = $_SESSION['email'] ?? 'admin@email.com';
$user_role     = $_SESSION['role'] ?? 'hrd';
$user_inisial  = strtoupper(substr($user_nama, 0, 1));

// Tangkap parameter halaman di paling atas agar terbaca oleh sidebar
$page = isset($_GET['page']) ? $_GET['page'] : "dashboard";
?>
<!DOCTYPE html>
<html lang="id">

<!-- head --> 
<?php include 'partials/head.php'; ?>
<!-- head -->

<body>

  <!-- START: Sidebar Component -->
  <?php include 'components/sidebar.php'; ?>
  <!-- END: Sidebar Component -->

  <div class="main-wrapper">

    <!-- START: Top Navbar Component -->
    <?php include 'components/topbar.php'; ?>
    <!-- END: Top Navbar Component -->

    <!-- MAIN CONTENT -->
    <?php
    switch ($page) {
        case 'dashboard':
            include 'pages/dashboard.php';
            break;
        case 'jabatan':
            include 'pages/jabatan/jabatan.php';
            break;
        case 'tambah-jabatan':
            include 'pages/jabatan/tambah.php';
            break;
        case 'edit-jabatan':
            include 'pages/jabatan/update.php';
            break;
        case 'hapus-jabatan':
            include 'pages/jabatan/hapus.php';
            break;
        case 'karyawan':
            include 'pages/karyawan/karyawan.php';
            break;    
        case 'tambah-karyawan':
            include 'pages/karyawan/tambah.php';
            break;  
        case 'edit-karyawan':
            include 'pages/karyawan/update.php';
            break;
        case 'hapus-karyawan':
            include 'pages/karyawan/hapus.php';
            break;  
        case 'shift':
            include 'pages/shift/shift.php';
            break;    
        case 'tambah-shift':
            include 'pages/shift/tambah.php';
            break;
        case 'edit-shift':
            include 'pages/shift/update.php';
            break;
        case 'hapus-shift':
            include 'pages/shift/hapus.php';
            break;    
        case 'absensi':
            include 'pages/absensi/absensi.php';
            break;
        case 'edit-absensi':
            include 'pages/absensi/update.php';
            break;
        case 'hapus-absensi':
            include 'pages/absensi/hapus.php';
            break;
        case 'izin':
            include 'pages/izin/izin.php';
            break;
        default:
            include 'pages/dashboard.php';
            break;
    }
    ?>

    <!-- START: Footer Component -->
    <?php include 'components/footer.php'; ?>
    <!-- END: Footer Component -->

  </div>

  <?php include 'partials/script.php'; ?>
</body>
</html>