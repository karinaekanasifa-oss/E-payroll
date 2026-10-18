<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN KELOLA JABATAN - OOP VERSION (WARNA SENADA TEMPLATE & ANIMASI)
 * --------------------------------------------------------------------------
 */

require_once 'database/connection.php';

class PositionOOP {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function tampilDataJabatan() {
        return $this->conn->query("SELECT * FROM positions ORDER BY id DESC");
    }

    public function getTotalJabatan() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM positions")->fetch_assoc();
        return (int) ($result['total'] ?? 0);
    }

    public function getRataRataGaji() {
        $result = $this->conn->query("SELECT AVG(gaji_pokok) as rata FROM positions")->fetch_assoc();
        return (float) ($result['rata'] ?? 0);
    }
}

$positionObj = new PositionOOP();

$total_jabatan = $positionObj->getTotalJabatan();
$rata_gaji     = $positionObj->getRataRataGaji();
$query_jabatan = $positionObj->tampilDataJabatan();

// Generate CSRF Token jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
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
    <h1 class="page-title fw-bold text-dark">Manajemen Jabatan</h1>
    <p class="page-subtitle text-muted">Kelola data posisi jabatan, rincian gaji pokok, dan tunjangan karyawan secara terstruktur.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard" class="text-decoration-none text-success">Home</a></li>
      <li class="breadcrumb-item text-muted">HRD</li>
      <li class="breadcrumb-item active text-dark" aria-current="page">Jabatan</li>
    </ol>
  </nav>
</div>

<!-- START: Kartu Statistik Ringkasan -->
<div class="row mb-4">
  <div class="col-xl-6 col-md-6 mb-3 mb-xl-0">
    <div class="card border-0 shadow-sm p-3 border-start border-success border-4 card-hover-pop rounded-3 bg-white">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Total Posisi Jabatan</span>
          <h3 class="mb-0 fw-bold text-success mt-1"><?= htmlspecialchars($total_jabatan); ?> <span class="fs-6 fw-normal text-muted">Posisi</span></h3>
        </div>
        <div class="fs-2 text-success opacity-75"><i class="bi bi-briefcase-fill"></i></div>
      </div>
    </div>
  </div>

  <div class="col-xl-6 col-md-6">
    <div class="card border-0 shadow-sm p-3 border-start border-info border-4 card-hover-pop rounded-3 bg-white">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Rata-Rata Gaji Pokok</span>
          <h3 class="mb-0 fw-bold text-info mt-1">Rp <?= number_format($rata_gaji, 0, ',', '.'); ?></h3>
        </div>
        <div class="fs-2 text-info opacity-75"><i class="bi bi-cash-stack"></i></div>
      </div>
    </div>
  </div>
</div>

<div class="mb-4">
  <a href="index.php?page=tambah-jabatan" class="btn btn-template-theme px-4 py-2 rounded-2 shadow-sm">
    <i class="bi bi-plus-lg me-1"></i> Add Jabatan
  </a>
</div>

<!-- START: Basic Table Card Container -->
<div class="table-card-custom rounded-3 shadow-sm border-0 bg-white p-4">
  <div class="table-header-control d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div class="table-search-box flex-grow-1" style="max-width: 350px;">
      <div class="input-group input-group-sm">
        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="text" id="searchJabatanInput" class="form-control bg-light border-start-0 shadow-none" placeholder="Cari nama jabatan...">
      </div>
    </div>
    <div class="table-filter-group">
      <button class="btn btn-danger btn-sm px-3 rounded-2 shadow-sm" id="btnExportPDF" type="button">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
      </button>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table align-middle table-hover mb-0" id="myTableJabatan">
      <thead class="table-light text-uppercase fs-7 text-muted">
        <tr>
          <th class="py-3">No</th>
          <th class="py-3">Nama Jabatan</th>
          <th class="py-3">Gaji Pokok</th>
          <th class="py-3">Tunjangan</th>
          <th class="py-3 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($query_jabatan && $query_jabatan->num_rows > 0): ?>
          <?php $no = 1; while ($row = $query_jabatan->fetch_assoc()): ?>
            <tr>
              <td class="table-order-id fw-bold text-muted">#JAB-00<?php echo $no++; ?></td>
              <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['nama_jabatan'] ?? '-'); ?></td>
              <td class="text-muted">Rp <?php echo number_format((int)($row['gaji_pokok'] ?? 0), 0, ',', '.'); ?></td>
              <td class="text-muted">Rp <?php echo number_format((int)($row['tunjangan'] ?? 0), 0, ',', '.'); ?></td>
              <td>
                <div class="d-flex justify-content-center gap-2">
                  <a href="index.php?page=edit-jabatan&id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-light text-primary border rounded-1 p-2" title="Edit row"><i class="bi bi-pencil"></i></a>
                  <a href="index.php?page=hapus-jabatan&id=<?php echo (int)$row['id']; ?>&csrf_token=<?php echo $csrf_token; ?>" class="btn btn-sm btn-light text-danger border rounded-1 p-2" title="Delete row" onclick="return confirm('Yakin ingin menghapus jabatan ini?')"><i class="bi bi-trash"></i></a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center text-muted py-4">Belum ada data jabatan di database.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="table-footer-control mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
    <span class="badge bg-light text-success border px-3 py-2 rounded-2">Status: Sinkron Aktif</span>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('searchJabatanInput');
    const table = document.getElementById('myTableJabatan');

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

    const exportPdfBtn = document.getElementById('btnExportPDF');
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.setFontSize(16);
            doc.setTextColor(40, 40, 40);
            doc.text("Laporan Data Jabatan - E-Payroll", 14, 20);
            
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text("Dicetak pada: " + new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }), 14, 26);

            doc.autoTable({
                html: '#myTableJabatan',
                startY: 32,
                columns: [
                    { header: 'No', dataKey: 0 },
                    { header: 'Nama Jabatan', dataKey: 1 },
                    { header: 'Gaji Pokok', dataKey: 2 },
                    { header: 'Tunjangan', dataKey: 3 }
                ],
                theme: 'grid',
                headStyles: { fillColor: [20, 52, 35] },
                styles: { fontSize: 8, cellPadding: 4 }
            });

            doc.save("data_jabatan.pdf");
        });
    }
});
</script>