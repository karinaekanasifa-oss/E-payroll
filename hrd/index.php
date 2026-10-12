<?php
// Tangkap parameter halaman di paling atas agar terbaca oleh sidebar
$page = isset($_GET['page']) ? $_GET['page'] : "dashboard";
?>
<!DOCTYPE html>
<html lang="en">

<!-- head --> 
 <?php include 'partials/head.php'; ?>
<!-- head -->

<body>

  <!-- ==========================================
         START: Sidebar Component
         Highly polished, dark-green sticky navigation
         ========================================== -->
    <?php include 'components/sidebar.php'; ?>
  <!-- ==========================================
         END: Sidebar Component
         ========================================== -->


  <!-- ==========================================
         START: Main Content Area
         ========================================== -->
  <div class="main-wrapper">

    <!-- START: Top Navbar Component -->
     <?php include 'components/topbar.php'; ?>
    <!-- END: Top Navbar Component -->

    <!-- START: Dashboard Header Banner -->
      <!-- MAIN CONTENT -->
    <!-- Fungsinya untuk menampilkan halaman hanya dibagian main content saja -->
    <?php
    switch ($page) {
        // Untuk memberi nama halamannya
        // Dashboard
        case 'dashboard':
            // isinya apa / mau diisi dengan bagian pages apa
            include 'pages/dashboard.php';
            // Fungsinya untuk menahan halaman agar tidak 
            // otomatis berpindah ke halaman setelahnya
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
        //case 'tambah-absensi':
          //  include 'pages/absensi/tambah.php';
           // break;
        case 'edit-absensi':
            include 'pages/absensi/update.php';
            break;
        case 'hapus-absensi':
            include 'pages/absensi/hapus.php';
            break;

    }
    ?>
    <!-- END: Main Layout Grid -->

    <!-- START: Footer Component -->
    <?php include 'components/footer.php'; ?>
    <!-- END: Footer Component -->

  </div>
  <!-- ==========================================
         END: Main Content Area
         ========================================== -->

  <!-- Local Third-Party Libraries Script dependencies -->
  <?php include 'partials/script.php'; ?>
</body>

</html>