<?php
/**
 * --------------------------------------------------------------------------
 * DASHBOARD FINANCE - E-PAYROLL (OOP VERSION + BURGUNDY THEME)
 * --------------------------------------------------------------------------
 * Halaman utama dashboard khusus peran Finance dengan tema warna burgundy
 * estetik, animasi lembut, dan grafik finansial real-time.
 */

// Panggil file koneksi database terlebih dahulu
require_once 'database/connection.php';

/**
 * Kelas DashboardFinance
 * Kelas berbasis OOP untuk membungkus semua proses pengambilan data statistik 
 * keuangan dan penggajian dari database MySQL ke halaman dashboard.
 */
class DashboardFinance {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Method buat ngambil total pengeluaran gaji bulanan dari tabel payrolls
    public function getTotalPengeluaran() {
        $hasil = $this->conn->query("SELECT SUM(total_gaji) as total FROM payrolls")->fetch_assoc();
        return $hasil['total'] ?? 0;
    }

    // Method buat ngambil total slip gaji yang diterbitkan
    public function getTotalSlipGaji() {
        $hasil = $this->conn->query("SELECT COUNT(*) as total FROM payrolls")->fetch_assoc();
        return $hasil['total'] ?? 0;
    }

    // Method buat ngambil total jumlah karyawan aktif
    public function getTotalKaryawan() {
        $hasil = $this->conn->query("SELECT COUNT(*) as total FROM employees")->fetch_assoc();
        return $hasil['total'] ?? 0;
    }

    // Method buat ngambil daftar riwayat penggajian terbaru
    public function ambilGajiTerbaru($batas = 5) {
        $query = "SELECT payrolls.*, employees.nip, users.nama, positions.nama_jabatan 
                  FROM payrolls 
                  JOIN employees ON payrolls.employee_id = employees.id 
                  JOIN users ON employees.user_id = users.id 
                  JOIN positions ON employees.position_id = positions.id 
                  ORDER BY payrolls.id DESC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $batas);
        $stmt->execute();
        return $stmt->get_result();
    }
}

// Bikin objek (instansiasi) dari class DashboardFinance
$financeDashboardObj = new DashboardFinance();

// Ambil data-data buat ditampilkan di kartu statistik
$total_pengeluaran = $financeDashboardObj->getTotalPengeluaran();
$total_slip        = $financeDashboardObj->getTotalSlipGaji();
$total_karyawan    = $financeDashboardObj->getTotalKaryawan();

// Ambil data tabel riwayat gaji terbaru
$data_gaji_baru = $financeDashboardObj->ambilGajiTerbaru(5);
?>

<!-- KODE CSS TEMA BURGUNDY ESTETIK & ANIMASI LEMBUT -->
<style>
.finance-card-hover {
  transition: all 0.3s ease-in-out;
}
.finance-card-hover:hover {
  transform: translateY(-6px);
  box-shadow: 0 0.7rem 1.5rem rgba(128, 0, 32, 0.15) !important;
}
@keyframes munculPelan {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.page-header, .finance-card-hover {
  animation: munculPelan 0.5s ease-in-out;
}
.badge-burgundy-subtle {
  background-color: rgba(128, 0, 32, 0.08);
  color: #800020;
}
.btn-burgundy-custom {
  background-color: #800020;
  color: #fff;
  border: none;
  transition: all 0.3s ease;
}
.btn-burgundy-custom:hover {
  background-color: #600018;
  color: #fff;
  transform: scale(1.02);
}
</style>

<!-- HEADER HALAMAN DASHBOARD FINANCE -->
<div class="page-header mb-4">
  <div>
    <h1 class="page-title fw-bold" style="color: #800020;">Dashboard Finance</h1>
    <p class="page-subtitle text-muted">Kelola rekapitulasi pengeluaran gaji, komponen tunjangan, potongan, dan penerbitan slip finansial.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard-finance" class="text-decoration-none" style="color: #800020;">Home</a></li>
      <li class="breadcrumb-item active text-dark" aria-current="page">Dashboard Finance</li>
    </ol>
  </nav>
</div>

<!-- KARTU STATISTIK UTAMA (Tema Burgundy & Putih Bersih) -->
<div class="row mb-4">
  <!-- Kotak Total Pengeluaran Gaji -->
  <div class="col-xl-4 col-md-6 mb-3 mb-xl-0">
    <div class="card border-0 shadow-sm p-3 border-start border-4 finance-card-hover rounded-4" style="border-left-color: #800020 !important;">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Total Pengeluaran Gaji</span>
          <h3 class="mb-0 fw-bold mt-1" style="color: #800020;">Rp <?= number_format($total_pengeluaran, 0, ',', '.'); ?></h3>
        </div>
        <div class="fs-1 p-3 rounded-4 opacity-75" style="color: #800020; background-color: rgba(128, 0, 32, 0.1);"><i class="bi bi-wallet2"></i></div>
      </div>
    </div>
  </div>

  <!-- Kotak Total Slip Diterbitkan -->
  <div class="col-xl-4 col-md-6 mb-3 mb-xl-0">
    <div class="card border-0 shadow-sm p-3 border-start border-4 finance-card-hover rounded-4" style="border-left-color: #A0522D !important;">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Slip Gaji Diterbitkan</span>
          <h3 class="mb-0 fw-bold mt-1" style="color: #A0522D;"><?= $total_slip; ?> <span class="fs-6 fw-normal text-muted">Dokumen</span></h3>
        </div>
        <div class="fs-1 p-3 rounded-4 opacity-75" style="color: #A0522D; background-color: rgba(160, 82, 45, 0.1);"><i class="bi bi-file-earmark-text-fill"></i></div>
      </div>
    </div>
  </div>

  <!-- Kotak Total Pegawai Terdaftar -->
  <div class="col-xl-4 col-md-6">
    <div class="card border-0 shadow-sm p-3 border-start border-4 finance-card-hover rounded-4" style="border-left-color: #5C4033 !important;">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Total Pegawai</span>
          <h3 class="mb-0 fw-bold mt-1" style="color: #5C4033;"><?= $total_karyawan; ?> <span class="fs-6 fw-normal text-muted">Orang</span></h3>
        </div>
        <div class="fs-1 p-3 rounded-4 opacity-75" style="color: #5C4033; background-color: rgba(92, 64, 51, 0.1);"><i class="bi bi-people-fill"></i></div>
      </div>
    </div>
  </div>
</div>

<!-- BAGIAN BAWAH: DIAGRAM BATANG & TABEL GAJI TERBARU -->
<div class="row">
  <!-- Kolom Kiri: Diagram Batang Interaktif Chart.js -->
  <div class="col-xl-7 mb-4">
    <div class="card border-0 shadow-sm p-4 finance-card-hover rounded-4 h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-bar-chart-fill me-2" style="color: #800020;"></i>Grafik Ringkasan Finansial</h5>
        <span class="badge text-white px-3 py-2 rounded-pill" style="background-color: #800020;">Real-time Data</span>
      </div>
      <p class="text-muted small mb-4">Visualisasi perbandingan total pengeluaran finansial dan volume penerbitan slip.</p>
      
      <!-- Tempat Canvas Grafik Batang -->
      <div style="position: relative; height: 290px; width: 100%;">
        <canvas id="grafikBatangFinance"></canvas>
      </div>
    </div>
  </div>

  <!-- Kolom Kanan: Tabel Riwayat Gaji Terbaru -->
  <div class="col-xl-5 mb-4">
    <div class="card border-0 shadow-sm p-4 finance-card-hover rounded-4 h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-stack me-2" style="color: #800020;"></i>Penggajian Terbaru</h5>
        <a href="index.php?page=laporan-gaji" class="btn btn-sm rounded-pill px-3" style="border: 1px solid #800020; color: #800020;">Lihat Semua</a>
      </div>
      <p class="text-muted small mb-3">Daftar transaksi gaji yang baru saja diproses.</p>

      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="rounded-start">Nama</th>
              <th>Periode</th>
              <th class="text-end rounded-end">Total Gaji</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($data_gaji_baru && $data_gaji_baru->num_rows > 0): ?>
              <?php while ($row = $data_gaji_baru->fetch_assoc()): ?>
                <tr>
                  <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama']); ?></td>
                  <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['bulan_tahun']); ?></span></td>
                  <td class="text-end fw-bold" style="color: #800020;">Rp <?= number_format($row['total_gaji'], 0, ',', '.'); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="text-center text-muted py-4">Belum ada data transaksi penggajian.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- LOAD SCRIPT CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- SCRIPT JAVASCRIPT: Grafik Batang Tema Burgundy -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const konteksGrafik = document.getElementById('grafikBatangFinance').getContext('2d');
    
    const totalPengeluaran = <?= $total_pengeluaran; ?>;
    const totalSlip        = <?= $total_slip; ?>;
    const totalKaryawan    = <?= $total_karyawan; ?>;

    new Chart(konteksGrafik, {
        type: 'bar',
        data: {
            labels: ['Total Karyawan', 'Slip Diterbitkan', 'Volume Finansial (Juta Rp)'],
            datasets: [{
                label: 'Statistik',
                data: [totalKaryawan, totalSlip, totalPengeluaran / 1000000],
                backgroundColor: [
                    'rgba(92, 64, 51, 0.75)',  
                    'rgba(160, 82, 45, 0.75)',    
                    'rgba(128, 0, 32, 0.75)'   
                ],
                borderColor: [
                    'rgb(92, 64, 51)',
                    'rgb(160, 82, 45)',
                    'rgb(128, 0, 32)'
                ],
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.04)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>