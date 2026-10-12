<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN KELOLA GAJI / PENGGAJIAN - OOP VERSION (BURGUNDY THEME)
 * --------------------------------------------------------------------------
 */

// Memanggil file koneksi database terlebih dahulu
require_once 'database/connection.php';

/**
 * Class PayrollManagement
 * Membungkus seluruh logika query database untuk modul penggajian menggunakan OOP.
 */
class PayrollManagement {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Method untuk mengambil total pengeluaran gaji
    public function getTotalPengeluaran() {
        $hasil = $this->conn->query("SELECT SUM(total_gaji) as total FROM payrolls")->fetch_assoc();
        return $hasil['total'] ?? 0;
    }

    // Method untuk mengambil total slip gaji yang diterbitkan
    public function getTotalSlipCount() {
        $hasil = $this->conn->query("SELECT COUNT(*) as total FROM payrolls")->fetch_assoc();
        return $hasil['total'] ?? 0;
    }

    // Method untuk menampilkan seluruh data rekapitulasi gaji (JOIN dengan employees, users, positions)
    public function tampilDataGaji() {
        $query = "SELECT payrolls.*, employees.nip, users.nama, positions.nama_jabatan, positions.gaji_pokok, positions.tunjangan 
                  FROM payrolls 
                  JOIN employees ON payrolls.employee_id = employees.id 
                  JOIN users ON employees.user_id = users.id 
                  JOIN positions ON employees.position_id = positions.id 
                  ORDER BY payrolls.id DESC";
        return $this->conn->query($query);
    }
}

// Instansiasi Objek
$payrollObj = new PayrollManagement();

$total_pengeluaran = $payrollObj->getTotalPengeluaran();
$total_slip        = $payrollObj->getTotalSlipCount();
$data_gaji         = $payrollObj->tampilDataGaji();
?>

<!-- START: CSS Custom Estetik Burgundy -->
<style>
.finance-card-hover {
  transition: all 0.3s ease-in-out;
}
.finance-card-hover:hover {
  transform: translateY(-4px);
  box-shadow: 0 0.5rem 1rem rgba(128, 0, 32, 0.1) !important;
}
@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.page-header, .finance-card-hover, .table-card-custom {
  animation: slideUpFade 0.3s ease-in-out;
}
.btn-burgundy-theme {
  background-color: #800020;
  color: #fff;
  border: none;
}
.btn-burgundy-theme:hover {
  background-color: #600018;
  color: #fff;
}
</style>
<!-- END: CSS Custom -->

<!-- START: Page Header Banner -->
<div class="page-header mb-4">
  <div>
    <h1 class="page-title fw-bold" style="color: #800020;">Rekapitulasi Penggajian</h1>
    <p class="page-subtitle text-muted">Kelola data perhitungan gaji pokok, tunjangan, potongan, dan penerbitan slip finansial pegawai.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard-finance" class="text-decoration-none" style="color: #800020;">Home</a></li>
      <li class="breadcrumb-item text-muted">Finance</li>
      <li class="breadcrumb-item active text-dark" aria-current="page">Penggajian</li>
    </ol>
  </nav>
</div>
<!-- END: Page Header Banner -->

<!-- START: Kartu Statistik Finansial -->
<div class="row mb-4">
  <div class="col-xl-6 col-md-6 mb-3 mb-xl-0">
    <div class="card border-0 shadow-sm p-3 border-start border-4 finance-card-hover rounded-4 bg-white" style="border-left-color: #800020 !important;">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Total Pengeluaran Gaji</span>
          <h3 class="mb-0 fw-bold mt-1" style="color: #800020;">Rp <?= number_format($total_pengeluaran, 0, ',', '.'); ?></h3>
        </div>
        <div class="fs-2 p-3 rounded-4 opacity-75" style="color: #800020; background-color: rgba(128, 0, 32, 0.1);"><i class="bi bi-wallet2"></i></div>
      </div>
    </div>
  </div>

  <div class="col-xl-6 col-md-6">
    <div class="card border-0 shadow-sm p-3 border-start border-4 finance-card-hover rounded-4 bg-white" style="border-left-color: #A0522D !important;">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Total Slip Diterbitkan</span>
          <h3 class="mb-0 fw-bold mt-1" style="color: #A0522D;"><?= $total_slip; ?> <span class="fs-6 fw-normal text-muted">Dokumen</span></h3>
        </div>
        <div class="fs-2 p-3 rounded-4 opacity-75" style="color: #A0522D; background-color: rgba(160, 82, 45, 0.1);"><i class="bi bi-file-earmark-text-fill"></i></div>
      </div>
    </div>
  </div>
</div>
<!-- END: Kartu Statistik -->

<!-- Action Controls -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
  <a href="index.php?page=hitung-gaji" class="btn btn-burgundy-theme px-4 py-2 rounded-3 shadow-sm">
    <i class="bi bi-plus-lg me-1"></i> Hitung Gaji Baru
  </a>
</div>

<!-- START: Tabel Kontainer Utama -->
<div class="table-card-custom rounded-4 shadow-sm border-0 bg-white p-4">
  <div class="table-header-control d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div class="table-search-box flex-grow-1" style="max-width: 350px;">
      <div class="input-group input-group-sm">
        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="text" id="searchGajiInput" class="form-control bg-light border-start-0 shadow-none" placeholder="Cari nama, NIP, atau jabatan...">
      </div>
    </div>
    
    <div class="table-filter-group">
      <button class="btn btn-danger btn-sm px-3 rounded-3 shadow-sm" id="btnExportPDF" type="button">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF Laporan
      </button>
    </div>
  </div>

  <!-- Responsive Table Wrapper -->
  <div class="table-responsive">
    <table class="table align-middle table-hover mb-0" id="myTableGaji">
      <thead class="table-light text-uppercase fs-7 text-muted">
        <tr>
          <th class="py-3">No</th>
          <th class="py-3">Periode</th>
          <th class="py-3">NIP</th>
          <th class="py-3">Nama Karyawan</th>
          <th class="py-3">Jabatan</th>
          <th class="py-3">Gaji Pokok</th>
          <th class="py-3">Tunjangan</th>
          <th class="py-3">Potongan</th>
          <th class="py-3">Total Gaji</th>
          <th class="py-3">Status</th>
          <th class="py-3 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($data_gaji && $data_gaji->num_rows > 0): ?>
          <?php $no = 1; while ($row = $data_gaji->fetch_assoc()): ?>
            <tr>
              <td class="table-order-id fw-bold text-muted">#PAY-00<?php echo $no++; ?></td>
              <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['bulan_tahun'] ?? '-'); ?></span></td>
              <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['nip'] ?? '-'); ?></td>
              <td><span class="fw-bold text-dark"><?php echo htmlspecialchars($row['nama'] ?? '-'); ?></span></td>
              <td><span class="badge" style="background-color: rgba(128, 0, 32, 0.08); color: #800020;"><?php echo htmlspecialchars($row['nama_jabatan'] ?? '-'); ?></span></td>
              <td>Rp <?= number_format($row['gaji_pokok'] ?? 0, 0, ',', '.'); ?></td>
              <td>Rp <?= number_format($row['tunjangan'] ?? 0, 0, ',', '.'); ?></td>
              <td class="text-danger">Rp <?= number_format($row['potongan'] ?? 0, 0, ',', '.'); ?></td>
              <td class="fw-bold" style="color: #800020;">Rp <?= number_format($row['total_gaji'] ?? 0, 0, ',', '.'); ?></td>
              <td><span class="badge bg-success text-uppercase px-2 py-1"><?php echo htmlspecialchars($row['status_cair']); ?></span></td>
              <td>
                <div class="d-flex justify-content-center gap-2">
                  <a href="index.php?page=hapus-gaji&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light text-danger border rounded-2 p-2" title="Hapus Data" onclick="return confirm('Yakin ingin menghapus data penggajian ini?')"><i class="bi bi-trash"></i></a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="11" class="text-center text-muted py-4">Belum ada data rekapitulasi gaji yang diterbitkan.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<!-- END: Tabel Kontainer Utama -->

<!-- LOAD PLUGIN JSPDF & AUTOTABLE UNTUK CETAK PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<!-- SCRIPT JAVASCRIPT: Real-Time Search & Ekspor PDF -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('searchGajiInput');
    const table = document.getElementById('myTableGaji');

    if (searchInput && table) {
        searchInput.addEventListener('keyup', function () {
            let filter = searchInput.value.toLowerCase();
            let trs = table.getElementsByTagName('tr');

            for (let i = 1; i < trs.length; i++) {
                let rowText = trs[i].textContent || trs[i].innerText;
                if (rowText.toLowerCase().indexOf(filter) > -1) {
                    trs[i].style.display = "";
                } else {
                    trs[i].style.display = "none";
                }
            }
        });
    }

    const exportPdfBtn = document.getElementById('btnExportPDF');
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.setFontSize(16);
            doc.setTextColor(40, 40, 40);
            doc.text("Laporan Rekapitulasi Penggajian Karyawan - E-Payroll", 14, 20);
            
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text("Dicetak pada: " + new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }), 14, 26);

            doc.autoTable({
                html: '#myTableGaji',
                startY: 32,
                theme: 'grid',
                headStyles: { fillColor: [128, 0, 32] },
                styles: { fontSize: 7, cellPadding: 3 }
            });

            doc.save("laporan_rekapitulasi_gaji.pdf");
        });
    }
});
</script>