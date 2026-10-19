<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN KELOLA ABSENSI - OOP VERSION (DENGAN PAGINATION & BAHASA INDONESIA)
 * --------------------------------------------------------------------------
 */

require_once 'database/connection.php';

class AttendanceManagement {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function getAttendanceCountByStatus($status) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM attedances WHERE status = ?");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    // Hitung total data absensi dengan filter periode
    public function getTotalAttendances($filter_periode = '') {
        $query = "SELECT COUNT(*) as total FROM attedances JOIN employees ON attedances.employee_id = employees.id JOIN users ON employees.user_id = users.id LEFT JOIN shifts ON attedances.shift_id = shifts.id";
        
        if ($filter_periode == 'triwulan_1') {
            $query .= " WHERE MONTH(attedances.tanggal) BETWEEN 1 AND 3";
        } elseif ($filter_periode == 'triwulan_2') {
            $query .= " WHERE MONTH(attedances.tanggal) BETWEEN 4 AND 6";
        } elseif ($filter_periode == 'triwulan_3') {
            $query .= " WHERE MONTH(attedances.tanggal) BETWEEN 7 AND 9";
        } elseif ($filter_periode == 'triwulan_4') {
            $query .= " WHERE MONTH(attedances.tanggal) BETWEEN 10 AND 12";
        }

        $result = $this->conn->query($query)->fetch_assoc();
        return (int) ($result['total'] ?? 0);
    }

    // Mengambil data absensi dengan LIMIT dan OFFSET untuk pagination
    public function getAttendancesDataPaginated($limit, $offset, $filter_periode = '') {
        $query = "SELECT attedances.id as att_id, attedances.tanggal, attedances.jam_masuk, 
                         attedances.jam_keluar, attedances.status,
                         employees.nip, users.nama, shifts.nama_shift 
                  FROM attedances 
                  JOIN employees ON attedances.employee_id = employees.id 
                  JOIN users ON employees.user_id = users.id 
                  LEFT JOIN shifts ON attedances.shift_id = shifts.id";

        if ($filter_periode == 'triwulan_1') {
            $query .= " WHERE MONTH(attedances.tanggal) BETWEEN 1 AND 3";
        } elseif ($filter_periode == 'triwulan_2') {
            $query .= " WHERE MONTH(attedances.tanggal) BETWEEN 4 AND 6";
        } elseif ($filter_periode == 'triwulan_3') {
            $query .= " WHERE MONTH(attedances.tanggal) BETWEEN 7 AND 9";
        } elseif ($filter_periode == 'triwulan_4') {
            $query .= " WHERE MONTH(attedances.tanggal) BETWEEN 10 AND 12";
        }

        $query .= " ORDER BY attedances.tanggal DESC, attedances.id DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }
}

$attendanceObj = new AttendanceManagement();
$filter_periode = isset($_GET['periode']) ? $_GET['periode'] : '';

$total_hadir = $attendanceObj->getAttendanceCountByStatus('hadir');
$total_izin  = $attendanceObj->getAttendanceCountByStatus('izin');
$total_sakit = $attendanceObj->getAttendanceCountByStatus('sakit');
$total_alfa  = $attendanceObj->getAttendanceCountByStatus('alfa');

// Konfigurasi Pagination (5 data per halaman)
$limit = 5;
$page_no = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
if ($page_no < 1) $page_no = 1;
$offset = ($page_no - 1) * $limit;

$total_data = $attendanceObj->getTotalAttendances($filter_periode);
$total_pages = ceil($total_data / $limit);

$query_absensi = $attendanceObj->getAttendancesDataPaginated($limit, $offset, $filter_periode);
?>

<style>
.card-hover-pop { transition: all 0.3s ease-in-out; }
.card-hover-pop:hover { transform: translateY(-4px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important; }
@keyframes slideUpFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.page-header, .card-hover-pop, .table-card-custom { animation: slideUpFade 0.3s ease-in-out; }
.btn-template-theme { background-color: #143423; color: #fff; border: none; }
.btn-template-theme:hover { background-color: #1b4731; color: #fff; }

/* Styling Pagination */
.pagination .page-item.active .page-link {
    background-color: #143423;
    border-color: #143423;
    color: #ffffff;
}
.pagination .page-link {
    color: #143423;
    border-radius: 0.5rem;
    margin: 0 2px;
    font-weight: 600;
}
.pagination .page-link:hover {
    background-color: #e8f5e9;
    color: #0b1d13;
}
</style>

<div class="page-header mb-4">
  <div>
    <h1 class="page-title fw-bold text-dark">Rekapitulasi Kehadiran</h1>
    <p class="page-subtitle text-muted">Kelola data presensi masuk, jam keluar, shift, dan status kehadiran karyawan.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard" class="text-decoration-none text-success">Home</a></li>
      <li class="breadcrumb-item text-muted">HRD</li>
      <li class="breadcrumb-item active text-dark" aria-current="page">Absensi</li>
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
  <a href="index.php?page=tambah-absensi" class="btn btn-template-theme px-4 py-2 rounded-2 shadow-sm">
    <i class="bi bi-plus-lg me-1"></i> Add Absensi
  </a>
  <form method="GET" action="index.php" class="d-flex align-items-center gap-2">
    <input type="hidden" name="page" value="absensi">
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
        <input type="text" id="searchAbsensiInput" class="form-control bg-light border-start-0 shadow-none" placeholder="Cari nama, NIP, shift, atau status...">
      </div>
    </div>
    <div class="table-filter-group">
      <button class="btn btn-danger btn-sm px-3 rounded-2 shadow-sm" id="btnExportPDF" type="button">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
      </button>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table align-middle table-hover mb-0" id="myTableAbsensi">
      <thead class="table-light text-uppercase fs-7 text-muted">
        <tr>
          <th class="py-3">No</th>
          <th class="py-3">Tanggal</th>
          <th class="py-3">Shift</th>
          <th class="py-3">NIP</th>
          <th class="py-3">Nama Karyawan</th>
          <th class="py-3">Jam Masuk</th>
          <th class="py-3">Jam Keluar</th>
          <th class="py-3">Status</th>
          <th class="py-3 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($query_absensi && $query_absensi->num_rows > 0): ?>
          <?php $no = $offset + 1; while ($row = $query_absensi->fetch_assoc()): ?>
            <?php 
              $status = strtolower($row['status']);
              $badgeClass = 'bg-secondary';
              if ($status == 'hadir') $badgeClass = 'bg-success';
              elseif ($status == 'izin') $badgeClass = 'bg-info text-dark';
              elseif ($status == 'sakit') $badgeClass = 'bg-warning text-dark';
              elseif ($status == 'alfa') $badgeClass = 'bg-danger';
            ?>
            <tr>
              <td class="table-order-id fw-bold text-muted">#ATT-00<?php echo $no++; ?></td>
              <td class="text-muted"><?php echo htmlspecialchars($row['tanggal'] ?? '-'); ?></td>
              <td><span class="badge bg-success-subtle text-success px-2 py-1 text-uppercase"><?php echo htmlspecialchars($row['nama_shift'] ?? '-'); ?></span></td>
              <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['nip'] ?? '-'); ?></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar-initial rounded-circle bg-success-subtle text-success fw-bold d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 13px;">
                    <?= strtoupper(substr($row['nama'], 0, 1)); ?>
                  </div>
                  <div><span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($row['nama'] ?? '-'); ?></span></div>
                </div>
              </td>
              <td class="text-muted"><?php echo ($status == 'hadir') ? htmlspecialchars($row['jam_masuk']) : '-'; ?></td>
              <td class="text-muted"><?php echo ($status == 'hadir') ? htmlspecialchars($row['jam_keluar']) : '-'; ?></td>
              <td><span class="badge <?php echo $badgeClass; ?> text-uppercase px-2 py-1"><?php echo htmlspecialchars($row['status']); ?></span></td>
              <td>
                <div class="d-flex justify-content-center gap-2">
                  <a href="index.php?page=edit-absensi&id=<?php echo $row['att_id']; ?>" class="btn btn-sm btn-light text-primary border rounded-1 p-2" title="Edit Data"><i class="bi bi-pencil"></i></a>
                  <a href="index.php?page=hapus-absensi&id=<?php echo $row['att_id']; ?>" class="btn btn-sm btn-light text-danger border rounded-1 p-2" title="Hapus Data" onclick="return confirm('Yakin ingin menghapus data absensi ini?')"><i class="bi bi-trash"></i></a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" class="text-center text-muted py-4">Belum ada data absensi untuk periode ini.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- START: Pagination Footer Control (Bahasa Indonesia) -->
  <?php if ($total_pages > 1): ?>
  <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
    <div class="text-muted small">
      Menampilkan halaman <strong><?= $page_no; ?></strong> dari <strong><?= $total_pages; ?></strong> (Total <?= $total_data; ?> Data Absensi)
    </div>
    <nav>
      <ul class="pagination pagination-sm mb-0">
        <!-- Tombol Sebelumnya -->
        <li class="page-item <?= ($page_no <= 1) ? 'disabled' : ''; ?>">
          <a class="page-link" href="index.php?page=absensi&periode=<?= $filter_periode; ?>&p=<?= $page_no - 1; ?>">
            Sebelumnya
          </a>
        </li>

        <!-- Nomor Halaman -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= ($page_no == $i) ? 'active' : ''; ?>">
            <a class="page-link" href="index.php?page=absensi&periode=<?= $filter_periode; ?>&p=<?= $i; ?>"><?= $i; ?></a>
          </li>
        <?php endfor; ?>

        <!-- Tombol Selanjutnya -->
        <li class="page-item <?= ($page_no >= $total_pages) ? 'disabled' : ''; ?>">
          <a class="page-link" href="index.php?page=absensi&periode=<?= $filter_periode; ?>&p=<?= $page_no + 1; ?>">
            Selanjutnya
          </a>
        </li>
      </ul>
    </nav>
  </div>
  <?php else: ?>
  <div class="table-footer-control mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
    <span class="badge bg-light text-success border px-3 py-2 rounded-2">Status: Sinkron Aktif</span>
  </div>
  <?php endif; ?>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('searchAbsensiInput');
    const table = document.getElementById('myTableAbsensi');

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
            doc.text("Laporan Rekapitulasi Kehadiran & Shift - E-Payroll", 14, 20);
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text("Dicetak pada: " + new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }), 14, 26);

            doc.autoTable({
                html: '#myTableAbsensi',
                startY: 32,
                columns: [
                    { header: 'No', dataKey: 0 },
                    { header: 'Tanggal', dataKey: 1 },
                    { header: 'Shift', dataKey: 2 },
                    { header: 'NIP', dataKey: 3 },
                    { header: 'Nama Karyawan', dataKey: 4 },
                    { header: 'Jam Masuk', dataKey: 5 },
                    { header: 'Jam Keluar', dataKey: 6 },
                    { header: 'Status', dataKey: 7 }
                ],
                theme: 'grid',
                headStyles: { fillColor: [20, 52, 35] },
                styles: { fontSize: 8, cellPadding: 4 }
            });
            doc.save("laporan_rekapitulasi_absensi.pdf");
        });
    }
});
</script>