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

    <!-- START: Main Content Dynamic Router -->
    <?php
    switch ($page) {
        case 'dashboard':
        case 'dashboard-finance':
            include 'pages/dashboard.php'; // atau file dashboard finance kamu
            break;
        case 'gaji':
        case 'laporan-gaji':
            include 'pages/penggajian/gaji.php';
            break;
        case 'hitung':
        case 'hitung-gaji':
            include 'pages/penggajian/hitung.php';
            break;
       case 'terbit':
        case 'terbit-gaji':
            include 'pages/penggajian/terbit.php'; // Tambahkan rute ini agar terbit.php terpanggil
            break;   
    }
    ?> 
    <!-- END: Main Content Dynamic Router -->

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