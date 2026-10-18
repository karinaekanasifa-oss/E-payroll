<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN KELOLA SHIFT - OOP VERSION (WARNA SENADA TEMPLATE & ANIMASI)
 * --------------------------------------------------------------------------
 */

// Menyesuaikan path koneksi database dari folder pages keluar ke root
require_once 'database/connection.php';

/**
 * Class ShiftOOP
 * Membungkus seluruh logika query untuk mengelola data shift kerja menggunakan OOP.
 */
class ShiftOOP {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Method untuk mengambil seluruh data shift
    public function tampilDataShift() {
        return $this->conn->query("SELECT * FROM shifts ORDER BY id DESC");
    }

    // Method untuk menghitung total data shift
    public function getTotalShift() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM shifts")->fetch_assoc();
        return $result['total'] ?? 0;
    }
}

// Instansiasi Objek Shift
$shiftObj = new ShiftOOP();

// Mengambil data statistik untuk kartu ringkasan
$total_shift = $shiftObj->getTotalShift();

// Mengambil data tabel shift
$query_shift = $shiftObj->tampilDataShift();
?>

<!-- START: CSS Custom Animasi & Warna Senada Template Admin -->
<style>
.card-hover-pop {
  transition: all 0.3s ease-in-out;
}
.card-hover-pop:hover {
  transform: translateY(-4px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
}

@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.page-header, .card-hover-pop, .table-card-custom {
  animation: slideUpFade 0.3s ease-in-out;
}

/* Tombol utama mengikuti warna tema template admin */
.btn-template-theme {
  background-color: #143423;
  color: #fff;
  border: none;
}
.btn-template-theme:hover {
  background-color: #1b4731;
  color: #fff;
}
</style>
<!-- END: CSS Custom -->

<!-- START: Page Header Banner -->
<div class="page-header mb-4">
  <div>
    <h1 class="page-title fw-bold text-dark">Manajemen Shift Kerja</h1>
    <p class="page-subtitle text-muted">Kelola data kategori shift, jam masuk, dan jam keluar karyawan secara terstruktur.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard" class="text-decoration-none text-success">Home</a></li>
      <li class="breadcrumb-item text-muted">HRD</li>
      <li class="breadcrumb-item active text-dark" aria-current="page">Shift</li>
    </ol>
  </nav>
</div>
<!-- END: Page Header Banner -->

<!-- START: Kartu Statistik Ringkasan dengan Animasi Hover -->
<div class="row mb-4">
  <div class="col-xl-12 col-md-12">
    <div class="card border-0 shadow-sm p-3 border-start border-success border-4 card-hover-pop rounded-3 bg-white">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Total Kategori Shift</span>
          <h3 class="mb-0 fw-bold text-success mt-1"><?= $total_shift; ?> <span class="fs-6 fw-normal text-muted">Shift Terdaftar</span></h3>
        </div>
        <div class="fs-2 text-success opacity-75"><i class="bi bi-clock-history"></i></div>
      </div>
    </div>
  </div>
</div>
<!-- END: Kartu Statistik Ringkasan -->

<!-- Tombol Tambah Shift -->
<div class="mb-4">
  <a href="index.php?page=tambah-shift" class="btn btn-template-theme px-4 py-2 rounded-2 shadow-sm">
    <i class="bi bi-plus-lg me-1"></i> Add Shift
  </a>
</div>

<!-- START: Basic Table Card Container -->
<div class="table-card-custom rounded-3 shadow-sm border-0 bg-white p-4">
  <!-- Header Controls -->
  <div class="table-header-control d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <!-- Search bar -->
    <div class="table-search-box flex-grow-1" style="max-width: 350px;">
      <div class="input-group input-group-sm">
        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="text" id="searchShiftInput" class="form-control bg-light border-start-0 shadow-none" placeholder="Cari nama shift...">
      </div>
    </div>
    <!-- Tombol Export PDF -->
    <div class="table-filter-group">
      <button class="btn btn-danger btn-sm px-3 rounded-2 shadow-sm" id="btnExportPDF" type="button">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
      </button>
    </div>
  </div>

  <!-- Responsive Table Wrapper -->
  <div class="table-responsive">
    <table class="table align-middle table-hover mb-0" id="myTableShift">
      <thead class="table-light text-uppercase fs-7 text-muted">
        <tr>
          <th class="py-3">No</th>
          <th class="py-3">Nama Shift</th>
          <th class="py-3">Jam Masuk</th>
          <th class="py-3">Jam Keluar</th>
          <th class="py-3 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($query_shift && $query_shift->num_rows > 0): ?>
          <?php $no = 1; while ($row = $query_shift->fetch_assoc()): ?>
            <tr>
              <td class="table-order-id fw-bold text-muted">#SFT-00<?php echo $no++; ?></td>
              <td class="fw-bold text-dark text-uppercase"><?php echo htmlspecialchars($row['nama_shift'] ?? '-'); ?></td>
              <td><span class="badge bg-success-subtle text-success px-2 py-1"><?php echo htmlspecialchars($row['jam_masuk'] ?? '-'); ?></span></td>
              <td><span class="badge bg-danger-subtle text-danger px-2 py-1"><?php echo htmlspecialchars($row['jam_keluar'] ?? '-'); ?></span></td>
              <td>
                <div class="d-flex justify-content-center gap-2">
                  <a href="index.php?page=edit-shift&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light text-primary border rounded-1 p-2" title="Edit row"><i class="bi bi-pencil"></i></a>
                  <a href="index.php?page=hapus-shift&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light text-danger border rounded-1 p-2" title="Delete row" onclick="return confirm('Yakin ingin menghapus shift ini?')"><i class="bi bi-trash"></i></a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center text-muted py-4">Belum ada data shift kerja di database.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Footer Controls -->
  <div class="table-footer-control mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
    <span class="badge bg-light text-success border px-3 py-2 rounded-2">Status: Sinkron Aktif</span>
  </div>
</div>
<!-- END: Basic Table Card Container -->

<!-- LOAD CDN JSPDF & JSPDF-AUTOTABLE UNTUK EKSPOR PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<!-- SCRIPT JAVASCRIPT UNTUK SEARCH DAN EXPORT PDF -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. FUNGSI PENCARIAN (SEARCH) REAL-TIME
    const searchInput = document.getElementById('searchShiftInput');
    const table = document.getElementById('myTableShift');

    if (searchInput && table) {
        searchInput.addEventListener('keyup', function () {
            let filter = searchInput.value.toLowerCase();
            let trs = table.getElementsByTagName('tr');

            for (let i = 1; i < trs.length; i++) {
                let tdNama = trs[i].getElementsByTagName('td')[1];
                if (tdNama) {
                    let textValue = tdNama.textContent || tdNama.innerText;
                    if (textValue.toLowerCase().indexOf(filter) > -1) {
                        trs[i].style.display = "";
                    } else {
                        trs[i].style.display = "none";
                    }
                }
            }
        });
    }

    // 2. FUNGSI EXPORT KE PDF MENGGUNAKAN JSPDF
    const exportPdfBtn = document.getElementById('btnExportPDF');
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Judul Dokumen PDF
            doc.setFontSize(16);
            doc.setTextColor(40, 40, 40);
            doc.text("Laporan Data Shift Kerja - E-Payroll", 14, 20);
            
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text("Dicetak pada: " + new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }), 14, 26);

            // Mengambil data dari tabel HTML untuk dimasukkan ke PDF
            doc.autoTable({
                html: '#myTableShift',
                startY: 32,
                columns: [
                    { header: 'No', dataKey: 0 },
                    { header: 'Nama Shift', dataKey: 1 },
                    { header: 'Jam Masuk', dataKey: 2 },
                    { header: 'Jam Keluar', dataKey: 3 }
                ],
                theme: 'grid',
                headStyles: { fillColor: [20, 52, 35] },
                styles: { fontSize: 8, cellPadding: 4 }
            });

            // Simpan/Download file PDF otomatis
            doc.save("data_shift_kerja.pdf");
        });
    }
});
</script>