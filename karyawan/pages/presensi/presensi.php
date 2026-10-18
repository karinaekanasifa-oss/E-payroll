<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN PRESENSI KARYAWAN - OOP VERSION (KHUSUS KARYAWAN)
 * --------------------------------------------------------------------------
 */

// Panggil file koneksi database
if (file_exists('database/connection.php')) {
    require_once 'database/connection.php';
} elseif (file_exists('../database/connection.php')) {
    require_once '../database/connection.php';
} elseif (file_exists('../../database/connection.php')) {
    require_once '../../database/connection.php';
}

class KaryawanPresensiPage {
    private $conn;
    private $employee_id;

    public function __construct($employee_id) {
        $db = new Database();
        $this->conn = $db->conn;
        $this->employee_id = (int) $employee_id;
    }

    // Menghitung total presensi karyawan berdasarkan status
    public function getAttendanceCountByStatus($status) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM attendances WHERE employee_id = ? AND status = ?");
        $stmt->bind_param("is", $this->employee_id, $status);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    // Mengambil riwayat presensi karyawan dengan JOIN ke shifts
    public function getAttendancesData($filter_periode = '') {
        $query = "SELECT attendances.id as att_id, attendances.tanggal, attendances.jam_masuk, 
                         attendances.jam_keluar, attendances.status,
                         shifts.nama_shift 
                  FROM attendances 
                  LEFT JOIN shifts ON attendances.shifts_id = shifts.id 
                  WHERE attendances.employee_id = " . $this->employee_id;

        if ($filter_periode == 'triwulan_1') {
            $query .= " AND MONTH(attendances.tanggal) BETWEEN 1 AND 3";
        } elseif ($filter_periode == 'triwulan_2') {
            $query .= " AND MONTH(attendances.tanggal) BETWEEN 4 AND 6";
        } elseif ($filter_periode == 'triwulan_3') {
            $query .= " AND MONTH(attendances.tanggal) BETWEEN 7 AND 9";
        } elseif ($filter_periode == 'triwulan_4') {
            $query .= " AND MONTH(attendances.tanggal) BETWEEN 10 AND 12";
        }

        $query .= " ORDER BY attendances.tanggal DESC, attendances.id DESC";
        return $this->conn->query($query);
    }
}

$employee_id = $_SESSION['employee_id'] ?? 0;
$user_nama   = $_SESSION['nama'] ?? 'Karyawan';

$presensiObj = new KaryawanPresensiPage($employee_id);
$filter_periode = isset($_GET['periode']) ? $_GET['periode'] : '';

$total_hadir = $presensiObj->getAttendanceCountByStatus('hadir');
$total_izin  = $presensiObj->getAttendanceCountByStatus('izin');
$total_sakit = $presensiObj->getAttendanceCountByStatus('sakit');
$total_alfa  = $presensiObj->getAttendanceCountByStatus('alfa');

$query_absensi = $presensiObj->getAttendancesData($filter_periode);
?>

<style>
.card-hover-pop { transition: all 0.3s ease-in-out; }
.card-hover-pop:hover { transform: translateY(-4px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important; }
@keyframes slideUpFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.page-header, .card-hover-pop, .table-card-custom { animation: slideUpFade 0.3s ease-in-out; }
.btn-template-theme { background-color: #143423; color: #fff; border: none; }
.btn-template-theme:hover { background-color: #1b4731; color: #fff; }
</style>

<div class="page-header mb-4">
  <div>
    <h1 class="page-title fw-bold text-dark">Presensi Kehadiran</h1>
    <p class="page-subtitle text-muted">Riwayat pencatatan jam masuk, jam keluar, shift, dan status kehadiran Anda.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard" class="text-decoration-none text-success">Home</a></li>
      <li class="breadcrumb-item text-muted">Karyawan</li>
      <li class="breadcrumb-item active text-dark" aria-current="page">Presensi</li>
    </ol>
  </nav>
</div>

<!-- Kartu Statistik -->
<div class="row mb-4">
  <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
    <div class="card border-0 shadow-sm p-3 border-start border-success border-4 card-hover-pop rounded-3 bg-white">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Total Hadir</span>
          <h3 class="mb-0 fw-bold text-success mt-1"><?= $total_hadir; ?> <span class="fs-6 fw-normal text-muted">Hari</span></h3>
        </div>
        <div class="fs-2 text-success opacity-75"><i class="bi bi-check-circle-fill"></i></div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3 mb-xl-0">
    <div class="card border-0 shadow-sm p-3 border-start border-info border-4 card-hover-pop rounded-3 bg-white">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Total Izin</span>
          <h3 class="mb-0 fw-bold text-info mt-1"><?= $total_izin; ?> <span class="fs-6 fw-normal text-muted">Hari</span></h3>
        </div>
        <div class="fs-2 text-info opacity-75"><i class="bi bi-info-circle-fill"></i></div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-3 mb-sm-0">
    <div class="card border-0 shadow-sm p-3 border-start border-warning border-4 card-hover-pop rounded-3 bg-white">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Total Sakit</span>
          <h3 class="mb-0 fw-bold text-warning mt-1"><?= $total_sakit; ?> <span class="fs-6 fw-normal text-muted">Hari</span></h3>
        </div>
        <div class="fs-2 text-warning opacity-75"><i class="bi bi-heart-pulse-fill"></i></div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card border-0 shadow-sm p-3 border-start border-danger border-4 card-hover-pop rounded-3 bg-white">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Total Alfa</span>
          <h3 class="mb-0 fw-bold text-danger mt-1"><?= $total_alfa; ?> <span class="fs-6 fw-normal text-muted">Hari</span></h3>
        </div>
        <div class="fs-2 text-danger opacity-75"><i class="bi bi-x-circle-fill"></i></div>
      </div>
    </div>
  </div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
  <a href="index.php?page=input-presensi" class="btn btn-template-theme px-4 py-2 rounded-2 shadow-sm">
    <i class="bi bi-fingerprint me-1"></i> Form Absensi Harian
  </a>
  <form method="GET" action="index.php" class="d-flex align-items-center gap-2">
    <input type="hidden" name="page" value="presensi">
    <select name="periode" class="form-select form-select-sm rounded-2 px-3 shadow-sm" onchange="this.form.submit()">
      <option value="">-- Semua Periode --</option>
      <option value="triwulan_1" <?= ($filter_periode == 'triwulan_1') ? 'selected' : ''; ?>>Triwulan I (Jan - Mar)</option>
      <option value="triwulan_2" <?= ($filter_periode == 'triwulan_2') ? 'selected' : ''; ?>>Triwulan II (Apr - Jun)</option>
      <option value="triwulan_3" <?= ($filter_periode == 'triwulan_3') ? 'selected' : ''; ?>>Triwulan III (Jul - Sep)</option>
      <option value="triwulan_4" <?= ($filter_periode == 'triwulan_4') ? 'selected' : ''; ?>>Triwulan IV (Okt - Des)</option>
    </select>
  </form>
</div>

<div class="table-card-custom rounded-3 shadow-sm border-0 bg-white p-4">
  <div class="table-header-control d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div class="table-search-box flex-grow-1" style="max-width: 350px;">
      <div class="input-group input-group-sm">
        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="text" id="searchPresensiInput" class="form-control bg-light border-start-0 shadow-none" placeholder="Cari tanggal, shift, atau status...">
      </div>
    </div>
    <div class="table-filter-group">
      <button class="btn btn-danger btn-sm px-3 rounded-2 shadow-sm" id="btnExportPDF" type="button">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
      </button>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table align-middle table-hover mb-0" id="myTablePresensi">
      <thead class="table-light text-uppercase fs-7 text-muted">
        <tr>
          <th class="py-3">No</th>
          <th class="py-3">Tanggal</th>
          <th class="py-3">Shift</th>
          <th class="py-3">Nama Karyawan</th>
          <th class="py-3">Jam Masuk</th>
          <th class="py-3">Jam Keluar</th>
          <th class="py-3">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($query_absensi && $query_absensi->num_rows > 0): ?>
          <?php $no = 1; while ($row = $query_absensi->fetch_assoc()): ?>
            <?php 
              $status = strtolower($row['status']);
              $badgeClass = 'bg-secondary';
              if ($status == 'hadir') $badgeClass = 'bg-success';
              elseif ($status == 'izin') $badgeClass = 'bg-info text-dark';
              elseif ($status == 'sakit') $badgeClass = 'bg-warning text-dark';
              elseif ($status == 'alfa') $badgeClass = 'bg-danger';
            ?>
            <tr>
              <td class="table-order-id fw-bold text-muted">#PRES-00<?php echo $no++; ?></td>
              <td class="text-muted"><?php echo htmlspecialchars($row['tanggal'] ?? '-'); ?></td>
              <td><span class="badge bg-success-subtle text-success px-2 py-1 text-uppercase"><?php echo htmlspecialchars($row['nama_shift'] ?? 'Normal'); ?></span></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar-initial rounded-circle bg-success-subtle text-success fw-bold d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 13px;">
                    <?= strtoupper(substr($user_nama, 0, 1)); ?>
                  </div>
                  <div><span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($user_nama); ?></span></div>
                </div>
              </td>
              <td class="text-muted"><?php echo ($status == 'hadir') ? htmlspecialchars($row['jam_masuk'] ?? '-') : '-'; ?></td>
              <td class="text-muted"><?php echo ($status == 'hadir') ? htmlspecialchars($row['jam_keluar'] ?? '-') : '-'; ?></td>
              <td><span class="badge <?php echo $badgeClass; ?> text-uppercase px-2 py-1"><?php echo htmlspecialchars($row['status']); ?></span></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center text-muted py-4">Belum ada riwayat presensi untuk periode ini.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('searchPresensiInput');
    const table = document.getElementById('myTablePresensi');

    if (searchInput && table) {
        searchInput.addEventListener('keyup', function () {
            let filter = searchInput.value.toLowerCase();
            let trs = table.getElementsByTagName('tr');
            for (let i = 1; i < trs.length; i++) {
                let rowText = trs[i].textContent || trs[i].innerText;
                trs[i].style.display = (rowText.toLowerCase().indexOf(filter) > -1) ? "" : "none";
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
            doc.text("Laporan Riwayat Presensi Karyawan - E-Payroll", 14, 20);
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text("Dicetak pada: " + new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }), 14, 26);

            doc.autoTable({
                html: '#myTablePresensi',
                startY: 32,
                columns: [
                    { header: 'No', dataKey: 0 },
                    { header: 'Tanggal', dataKey: 1 },
                    { header: 'Shift', dataKey: 2 },
                    { header: 'Nama Karyawan', dataKey: 3 },
                    { header: 'Jam Masuk', dataKey: 4 },
                    { header: 'Jam Keluar', dataKey: 5 },
                    { header: 'Status', dataKey: 6 }
                ],
                theme: 'grid',
                headStyles: { fillColor: [20, 52, 35] },
                styles: { fontSize: 8, cellPadding: 4 }
            });
            doc.save("laporan_presensi_karyawan.pdf");
        });
    }
});
</script>