<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN KELOLA KARYAWAN - SECURE OOP VERSION WITH PAGINATION
 * --------------------------------------------------------------------------
 */

require_once 'database/connection.php';

class EmployeeManagement {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Ambil data karyawan dengan pembatasan limit untuk pagination
    public function getEmployeesDataPaginated($limit, $offset) {
        $query = "SELECT employees.id as emp_id, employees.nip, employees.tgl_masuk, employees.nama_bank, employees.no_rekening,
                         users.nama, users.email, users.role, positions.nama_jabatan 
                  FROM employees 
                  JOIN users ON employees.user_id = users.id 
                  JOIN positions ON employees.position_id = positions.id 
                  ORDER BY employees.id DESC 
                  LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getTotalEmployees() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM employees")->fetch_assoc();
        return (int)($result['total'] ?? 0);
    }

    public function getTotalPositions() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM positions")->fetch_assoc();
        return (int)($result['total'] ?? 0);
    }
}

$employeeObj = new EmployeeManagement();

$total_karyawan = $employeeObj->getTotalEmployees();
$total_jabatan  = $employeeObj->getTotalPositions();

// Konfigurasi Pagination
$limit = 5; // Maksimal 5 data per halaman
$page_current = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($page_current < 1) $page_current = 1;
$offset = ($page_current - 1) * $limit;

$total_pages = ceil($total_karyawan / $limit);
if ($total_pages < 1) $total_pages = 1;

$query_karyawan = $employeeObj->getEmployeesDataPaginated($limit, $offset);

// Inisialisasi token CSRF untuk aksi hapus
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!-- START: CSS Custom -->
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

.badge-jabatan {
  background-color: #e8f5e9;
  color: #143423;
  font-weight: 600;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 0.85rem;
}
</style>
<!-- END: CSS Custom -->

<div class="page-header mb-4">
  <div>
    <h1 class="page-title fw-bold text-dark">Manajemen Karyawan</h1>
    <p class="page-subtitle text-muted">Kelola data pegawai aktif, NIP, relasi jabatan, rekening bank, dan role akun login secara terstruktur.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard" class="text-decoration-none text-success">Home</a></li>
      <li class="breadcrumb-item text-muted">HRD</li>
      <li class="breadcrumb-item active text-dark" aria-current="page">Karyawan</li>
    </ol>
  </nav>
</div>

<div class="row mb-4">
  <div class="col-xl-6 col-md-6 mb-3 mb-xl-0">
    <div class="card border-0 shadow-sm p-3 border-start border-success border-4 card-hover-pop rounded-3 bg-white">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Total Pegawai Aktif</span>
          <h3 class="mb-0 fw-bold text-success mt-1"><?= htmlspecialchars($total_karyawan); ?> <span class="fs-6 fw-normal text-muted">Orang</span></h3>
        </div>
        <div class="fs-2 text-success opacity-75"><i class="bi bi-people-fill"></i></div>
      </div>
    </div>
  </div>

  <div class="col-xl-6 col-md-6">
    <div class="card border-0 shadow-sm p-3 border-start border-info border-4 card-hover-pop rounded-3 bg-white">
      <div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <span class="text-muted small fw-bold text-uppercase">Kategori Jabatan</span>
          <h3 class="mb-0 fw-bold text-info mt-1"><?= htmlspecialchars($total_jabatan); ?> <span class="fs-6 fw-normal text-muted">Posisi</span></h3>
        </div>
        <div class="fs-2 text-info opacity-75"><i class="bi bi-briefcase-fill"></i></div>
      </div>
    </div>
  </div>
</div>

<div class="mb-4">
  <a href="index.php?page=tambah-karyawan" class="btn btn-template-theme px-4 py-2 rounded-2 shadow-sm">
    <i class="bi bi-plus-lg me-1"></i> Add Karyawan Baru
  </a>
</div>

<div class="table-card-custom rounded-3 shadow-sm border-0 bg-white p-4">
  <div class="table-header-control d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div class="table-search-box flex-grow-1" style="max-width: 350px;">
      <div class="input-group input-group-sm">
        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
        <input type="text" id="searchKaryawanInput" class="form-control bg-light border-start-0 shadow-none" placeholder="Cari nama, NIP, bank, atau jabatan...">
      </div>
    </div>
    
    <div class="table-filter-group">
      <button class="btn btn-danger btn-sm px-3 rounded-2 shadow-sm" id="btnExportPDF" type="button">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export PDF
      </button>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table align-middle table-hover mb-0" id="myTableKaryawan">
      <thead class="table-light text-uppercase fs-7 text-muted">
        <tr>
          <th class="py-3">No</th>
          <th class="py-3">NIP</th>
          <th class="py-3">Nama Karyawan</th>
          <th class="py-3">Email & Role</th>
          <th class="py-3">Bank & No. Rekening</th>
          <th class="py-3">Jabatan</th>
          <th class="py-3">Tanggal Masuk</th>
          <th class="py-3 text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($query_karyawan && $query_karyawan->num_rows > 0): ?>
          <?php 
            $no = $offset + 1; 
            while ($row = $query_karyawan->fetch_assoc()): 
          ?>
            <tr>
              <td class="table-order-id fw-bold text-muted">#EMP-00<?php echo $no++; ?></td>
              <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['nip'] ?? '-'); ?></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar-initial rounded-circle bg-success-subtle text-success fw-bold d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 13px;">
                    <?= strtoupper(substr($row['nama'], 0, 1)); ?>
                  </div>
                  <div>
                    <span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($row['nama'] ?? '-'); ?></span>
                  </div>
                </div>
              </td>
              <td>
                <span class="text-muted d-block"><?php echo htmlspecialchars($row['email'] ?? '-'); ?></span>
                <?php 
                  $role_user = strtolower($row['role'] ?? 'karyawan');
                  $badge_class = ($role_user === 'hrd') ? 'bg-danger-subtle text-danger' : (($role_user === 'finance') ? 'bg-warning-subtle text-warning-emphasis' : 'bg-info-subtle text-info');
                ?>
                <span class="badge <?= $badge_class; ?> text-uppercase" style="font-size: 10px;"><?= htmlspecialchars($role_user); ?></span>
              </td>
              <td>
                <span class="fw-bold text-secondary"><?php echo htmlspecialchars($row['nama_bank'] ?? 'Belum diatur'); ?></span><br>
                <small class="text-muted"><?php echo htmlspecialchars($row['no_rekening'] ?? '-'); ?></small>
              </td>
              <td><span class="badge-jabatan"><?php echo htmlspecialchars($row['nama_jabatan'] ?? '-'); ?></span></td>
              <td class="text-muted"><?php echo htmlspecialchars($row['tgl_masuk'] ?? '-'); ?></td>
              <td>
                <div class="d-flex justify-content-center gap-2">
                  <a href="index.php?page=edit-karyawan&id=<?php echo (int)$row['emp_id']; ?>" class="btn btn-sm btn-light text-primary border rounded-1 p-2" title="Edit Data"><i class="bi bi-pencil"></i></a>
                  <a href="index.php?page=hapus-karyawan&id=<?php echo (int)$row['emp_id']; ?>&csrf_token=<?php echo $csrf_token; ?>" class="btn btn-sm btn-light text-danger border rounded-1 p-2" title="Hapus Data" onclick="return confirm('Yakin ingin menghapus data karyawan ini?')"><i class="bi bi-trash"></i></a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="text-center text-muted py-4">Belum ada data karyawan tersimpan di database.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination & Status Bar -->
  <div class="table-footer-control mt-3 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div class="text-muted small">
      Menampilkan halaman <strong><?= $page_current; ?></strong> dari <strong><?= $total_pages; ?></strong> (Total <?= $total_karyawan; ?> Karyawan)
    </div>

    <!-- Navigasi Halaman (Pagination) -->
    <?php if ($total_pages > 1): ?>
      <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm mb-0">
          <!-- Tombol Previous -->
          <li class="page-item <?= ($page_current <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link text-success" href="index.php?page=karyawan&p=<?= $page_current - 1; ?>">Sebelumnya</a>
          </li>

          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($page_current == $i) ? 'active' : ''; ?>">
              <a class="page-link <?= ($page_current == $i) ? 'bg-success border-success text-white' : 'text-success'; ?>" href="index.php?page=karyawan&p=<?= $i; ?>"><?= $i; ?></a>
            </li>
          <?php endfor; ?>

          <!-- Tombol Next -->
          <li class="page-item <?= ($page_current >= $total_pages) ? 'disabled' : ''; ?>">
            <a class="page-link text-success" href="index.php?page=karyawan&p=<?= $page_current + 1; ?>">Selanjutnya</a>
          </li>
        </ul>
      </nav>
    <?php endif; ?>

    <span class="badge bg-light text-success border px-3 py-2 rounded-2">Status: Sinkron Aktif</span>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('searchKaryawanInput');
    const table = document.getElementById('myTableKaryawan');

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
            doc.text("Laporan Resmi Data Karyawan & Rekening - E-Payroll", 14, 20);
            
            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text("Dicetak pada: " + new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }), 14, 26);

            doc.autoTable({
                html: '#myTableKaryawan',
                startY: 32,
                columns: [
                    { header: 'No', dataKey: 0 },
                    { header: 'NIP', dataKey: 1 },
                    { header: 'Nama Karyawan', dataKey: 2 },
                    { header: 'Email & Role', dataKey: 3 },
                    { header: 'Bank & Rekening', dataKey: 4 },
                    { header: 'Jabatan', dataKey: 5 },
                    { header: 'Tanggal Masuk', dataKey: 6 }
                ],
                theme: 'grid',
                headStyles: { fillColor: [20, 52, 35] },
                styles: { fontSize: 7, cellPadding: 3 }
            });

            doc.save("laporan_data_karyawan_rekening.pdf");
        });
    }
});
</script>