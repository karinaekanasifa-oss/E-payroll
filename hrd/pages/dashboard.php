<?php
/**
 * --------------------------------------------------------------------------
 * DASHBOARD HRD - E-PAYROLL (OOP VERSION + MODERN CHART & CARDS)
 * --------------------------------------------------------------------------
 * Halaman utama dashboard khusus peran HRD untuk memantau data karyawan,
 * jabatan, serta grafik rekapitulasi absensi secara real-time dan estetik.
 */

// Panggil file koneksi database
require_once 'database/connection.php';

/**
 * Kelas DashboardHRD
 * Kelas berbasis OOP untuk membungkus semua proses pengambilan data statistik 
 * dari database MySQL ke halaman dashboard.
 */
class DashboardHRD {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Method buat ngambil total data dari tabel tertentu secara dinamis
    public function hitungTotalData($nama_tabel) {
        $hasil = $this->conn->query("SELECT COUNT(*) as total FROM $nama_tabel")->fetch_assoc();
        return $hasil['total'] ?? 0;
    }

    // Method buat ngambil jumlah kehadiran berdasarkan statusnya (hadir, izin, sakit, alfa)
    public function hitungAbsensiByStatus($status_kehadiran) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM attedances WHERE status = ?");
        $stmt->bind_param("s", $status_kehadiran);
        $stmt->execute();
        $hasil = $stmt->get_result()->fetch_assoc();
        return $hasil['total'] ?? 0;
    }

    // Method buat ngambil daftar karyawan yang baru banget ditambahkan
    public function ambilKaryawanTerbaru($batas = 5) {
        $query = "SELECT employees.nip, users.nama, users.email 
                  FROM employees 
                  JOIN users ON employees.user_id = users.id 
                  ORDER BY employees.id DESC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $batas);
        $stmt->execute();
        return $stmt->get_result();
    }
}

// Bikin objek (instansiasi) dari class DashboardHRD
$dashboardObj = new DashboardHRD();

// Ambil data-data buat ditampilkan di kartu statistik
$jumlah_karyawan = $dashboardObj->hitungTotalData('employees');
$jumlah_jabatan  = $dashboardObj->hitungTotalData('positions');
$jumlah_hadir    = $dashboardObj->hitungAbsensiByStatus('hadir');
$jumlah_izin     = $dashboardObj->hitungAbsensiByStatus('izin');
$jumlah_sakit    = $dashboardObj->hitungAbsensiByStatus('sakit');
$jumlah_alfa     = $dashboardObj->hitungAbsensiByStatus('alfa');

// Ambil data tabel karyawan terbaru
$data_karyawan_baru = $dashboardObj->ambilKaryawanTerbaru(5);
?>

<!-- KODE CSS TAMBAHAN: Biar ada animasi lembut & tampilan gak kaku -->
<style>
.card-hover-effect {
  transition: all 0.3s ease-in-out;
}
.card-hover-effect:hover {
  transform: translateY(-6px);
  box-shadow: 0 0.7rem 1.5rem rgba(0, 0, 0, 0.12) !important;
}
@keyframes munculPelan {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.page-header, .card-hover-effect {
  animation: munculPelan 0.5s ease-in-out;
}
</style>

<!-- HEADER HALAMAN DASHBOARD -->
<div class="page-header mb-4">
  <div>
    <h1 class="page-title fw-bold">Dashboard HRD</h1>
    <p class="page-subtitle text-muted">Selamat datang kembali! Yuk pantau data karyawan, posisi jabatan, dan rekap absensi harian di sini.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard" class="text-decoration-none text-success">Home</a></li>
      <li class="breadcrumb-item active text-dark" aria-current="page">Dashboard HRD</li>
    </ol>
  </nav>
</div>

<!-- KARTU STATISTIK UTAMA (Dibuat warna-warni biar estetik dan gak monoton) -->
<div class="row mb-4">
  <!-- Kotak Total Karyawan -->
  <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
    <div class="card border-0 shadow-sm p-3 border-start border-success border-4 card-hover-effect rounded-4">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Total Karyawan</span>
          <h3 class="mb-0 fw-bold text-success mt-1"><?= $jumlah_karyawan; ?> <span class="fs-6 fw-normal text-muted">Orang</span></h3>
        </div>
        <div class="fs-1 text-success bg-success-subtle p-3 rounded-4 opacity-75"><i class="bi bi-people-fill"></i></div>
      </div>
    </div>
  </div>

  <!-- Kotak Total Jabatan -->
  <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
    <div class="card border-0 shadow-sm p-3 border-start border-info border-4 card-hover-effect rounded-4">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Total Jabatan</span>
          <h3 class="mb-0 fw-bold text-info mt-1"><?= $jumlah_jabatan; ?> <span class="fs-6 fw-normal text-muted">Posisi</span></h3>
        </div>
        <div class="fs-1 text-info bg-info-subtle p-3 rounded-4 opacity-75"><i class="bi bi-briefcase-fill"></i></div>
      </div>
    </div>
  </div>

  <!-- Kotak Total Hadir -->
  <div class="col-xl-3 col-md-6 mb-3 mb-sm-0">
    <div class="card border-0 shadow-sm p-3 border-start border-primary border-4 card-hover-effect rounded-4">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Kehadiran (Hadir)</span>
          <h3 class="mb-0 fw-bold text-primary mt-1"><?= $jumlah_hadir; ?> <span class="fs-6 fw-normal text-muted">Hari</span></h3>
        </div>
        <div class="fs-1 text-primary bg-primary-subtle p-3 rounded-4 opacity-75"><i class="bi bi-check-circle-fill"></i></div>
      </div>
    </div>
  </div>

  <!-- Kotak Total Izin/Sakit/Alfa -->
  <div class="col-xl-3 col-md-6">
    <div class="card border-0 shadow-sm p-3 border-start border-warning border-4 card-hover-effect rounded-4">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Izin / Sakit / Alfa</span>
          <h3 class="mb-0 fw-bold text-warning mt-1"><?= ($jumlah_izin + $jumlah_sakit + $jumlah_alfa); ?> <span class="fs-6 fw-normal text-muted">Total</span></h3>
        </div>
        <div class="fs-1 text-warning bg-warning-subtle p-3 rounded-4 opacity-75"><i class="bi bi-exclamation-triangle-fill"></i></div>
      </div>
    </div>
  </div>
</div>

<!-- BAGIAN BAWAH: DIAGRAM BATANG & TABEL KARYAWAN TERBARU -->
<div class="row">
  <!-- Kolom Kiri: Diagram Batang Interaktif pakai Chart.js -->
  <div class="col-xl-7 mb-4">
    <div class="card border-0 shadow-sm p-4 card-hover-effect rounded-4 h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-bar-chart-fill text-success me-2"></i>Grafik Rekap Kepegawaian</h5>
        <span class="badge bg-success text-white px-3 py-2 rounded-pill">Real-time Data</span>
      </div>
      <p class="text-muted small mb-4">Visualisasi perbandingan jumlah karyawan, posisi jabatan, dan status kehadiran.</p>
      
      <!-- Tempat Canvas Grafik Batang -->
      <div style="position: relative; height: 290px; width: 100%;">
        <canvas id="grafikBatangHrd"></canvas>
      </div>
    </div>
  </div>

  <!-- Kolom Kanan: Tabel Karyawan Terbaru -->
  <div class="col-xl-5 mb-4">
    <div class="card border-0 shadow-sm p-4 card-hover-effect rounded-4 h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge-fill text-success me-2"></i>Karyawan Terbaru</h5>
        <a href="index.php?page=karyawan" class="btn btn-sm btn-outline-success rounded-pill px-3">Lihat Semua</a>
      </div>
      <p class="text-muted small mb-3">Daftar pegawai yang baru saja terdaftar di sistem.</p>

      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="rounded-start">Nama</th>
              <th>Email</th>
              <th class="text-end rounded-end">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($data_karyawan_baru && $data_karyawan_baru->num_rows > 0): ?>
              <?php while ($row = $data_karyawan_baru->fetch_assoc()): ?>
                <tr>
                  <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama']); ?></td>
                  <td class="text-muted small"><?= htmlspecialchars($row['email']); ?></td>
                  <td class="text-end"><span class="badge bg-success-subtle text-success px-2 py-1">Aktif</span></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="text-center text-muted py-4">Belum ada data karyawan yang tersimpan.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- LOAD SCRIPT CHART.JS BUAT BIKIN DIAGRAM BATANG -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- SCRIPT JAVASCRIPT: Mengatur Tampilan Diagram Batang -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const konteksGrafik = document.getElementById('grafikBatangHrd').getContext('2d');
    
    // Data dari PHP dipindahkan ke dalam variabel JavaScript secara aman
    const dataKaryawan = <?= $jumlah_karyawan; ?>;
    const dataJabatan  = <?= $jumlah_jabatan; ?>;
    const dataHadir    = <?= $jumlah_hadir; ?>;
    const dataIzin     = <?= $jumlah_izin; ?>;
    const dataSakit    = <?= $jumlah_sakit; ?>;
    const dataAlfa     = <?= $jumlah_alfa; ?>;

    new Chart(konteksGrafik, {
        type: 'bar',
        data: {
            labels: ['Karyawan', 'Jabatan', 'Hadir', 'Izin', 'Sakit', 'Alfa'],
            datasets: [{
                label: 'Jumlah',
                data: [dataKaryawan, dataJabatan, dataHadir, dataIzin, dataSakit, dataAlfa],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.75)',   // Hijau
                    'rgba(13, 202, 240, 0.75)',  // Biru Muda
                    'rgba(13, 110, 253, 0.75)',  // Biru Tua
                    'rgba(255, 193, 7, 0.75)',   // Kuning
                    'rgba(253, 126, 20, 0.75)',  // Oranye
                    'rgba(220, 53, 69, 0.75)'    // Merah
                ],
                borderColor: [
                    'rgb(25, 135, 84)',
                    'rgb(13, 202, 240)',
                    'rgb(13, 110, 253)',
                    'rgb(255, 193, 7)',
                    'rgb(253, 126, 20)',
                    'rgb(220, 53, 69)'
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