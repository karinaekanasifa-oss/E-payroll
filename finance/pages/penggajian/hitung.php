<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN HITUNG & PROSES GAJI - OOP VERSION (BURGUNDY THEME)
 * --------------------------------------------------------------------------
 */

// Memanggil file koneksi database terlebih dahulu
require_once 'database/connection.php';

/**
 * Class CalculatePayrollOOP
 * Membungkus logika pengambilan data karyawan & penyimpanan perhitungan gaji ke database.
 */
class CalculatePayrollOOP {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Mengambil daftar karyawan beserta jabatan, gaji pokok, dan tunjangan
    public function getEmployeesForPayroll() {
        $query = "SELECT employees.id as emp_id, employees.nip, users.nama, positions.nama_jabatan, positions.gaji_pokok, positions.tunjangan 
                  FROM employees 
                  JOIN users ON employees.user_id = users.id 
                  JOIN positions ON employees.position_id = positions.id 
                  ORDER BY users.nama ASC";
        return $this->conn->query($query);
    }

    // Menyimpan hasil perhitungan gaji ke tabel payrolls
    public function savePayrollCalculation($employee_id, $bulan_tahun, $potongan, $total_gaji, $status_cair) {
        $stmt = $this->conn->prepare("INSERT INTO payrolls (employee_id, bulan_tahun, potongan, total_gaji, status_cair) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdds", $employee_id, $bulan_tahun, $potongan, $total_gaji, $status_cair);
        return $stmt->execute();
    }
}

// Instansiasi Objek
$payrollCalcObj = new CalculatePayrollOOP();
$karyawan_list = $payrollCalcObj->getEmployeesForPayroll();

// Proses jika tombol submit ditekan
if (isset($_POST['proses_gaji'])) {
    $employee_id = $_POST['employee_id'];
    $bulan_tahun = $_POST['bulan_tahun'];
    $gaji_pokok  = floatval($_POST['gaji_pokok']);
    $tunjangan   = floatval($_POST['tunjangan']);
    $potongan    = floatval($_POST['potongan']);
    $status_cair = $_POST['status_cair']; // Menangkap status cair (dibayar / pending) dari pilihan form

    // Rumus Kalkulasi Total Gaji Bersih
    $total_gaji  = ($gaji_pokok + $tunjangan) - $potongan;

    $result = $payrollCalcObj->savePayrollCalculation($employee_id, $bulan_tahun, $potongan, $total_gaji, $status_cair);

    if ($result) {
        echo "<script>alert('Perhitungan gaji berhasil disimpan dan diterbitkan!'); window.location='index.php?page=gaji';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal memproses perhitungan gaji.');</script>";
    }
}
?>

<!-- START: CSS Custom Estetik Burgundy -->
<style>
@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.page-header, .card {
  animation: slideUpFade 0.3s ease-in-out;
}
.btn-burgundy-theme {
  background-color: #800020;
  color: #fff;
  border: none;
  transition: all 0.3s ease;
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
    <h1 class="page-title fw-bold" style="color: #800020;">Hitung & Proses Gaji</h1>
    <p class="page-subtitle text-muted">Kalkulasi komponen gaji pokok, tunjangan jabatan, dan potongan kehadiran karyawan secara terstruktur.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard-finance" class="text-decoration-none" style="color: #800020;">Home</a></li>
      <li class="breadcrumb-item text-muted">Finance</li>
      <li class="breadcrumb-item"><a href="index.php?page=gaji" class="text-decoration-none" style="color: #800020;">Penggajian</a></li>
      <li class="breadcrumb-item active text-dark" aria-current="page">Hitung Gaji</li>
    </ol>
  </nav>
</div>
<!-- END: Page Header Banner -->

<!-- START: Form Hitung Gaji Card -->
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="max-width: 800px;">
  <form method="POST" action="">
    <div class="mb-3">
      <label class="form-label fw-bold small text-uppercase text-muted">Pilih Karyawan</label>
      <select name="employee_id" class="form-select rounded-3 shadow-none" required onchange="ambilDataGaji(this)">
        <option value="" disabled selected>-- Pilih Pegawai --</option>
        <?php while ($k = $karyawan_list->fetch_assoc()): ?>
          <option value="<?= $k['emp_id']; ?>" data-gp="<?= $k['gaji_pokok']; ?>" data-tj="<?= $k['tunjangan']; ?>">
            <?= htmlspecialchars($k['nama'] . ' (' . $k['nip'] . ' - ' . $k['nama_jabatan'] . ')'); ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label fw-bold small text-uppercase text-muted">Periode Bulan & Tahun</label>
      <input type="month" name="bulan_tahun" class="form-control rounded-3 shadow-none" value="<?= date('Y-m'); ?>" required>
    </div>

    <div class="row">
      <div class="col-md-4 mb-3">
        <label class="form-label fw-bold small text-uppercase text-muted">Gaji Pokok (Rp)</label>
        <input type="number" id="gaji_pokok" name="gaji_pokok" class="form-control rounded-3 bg-light" readonly required>
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label fw-bold small text-uppercase text-muted">Tunjangan (Rp)</label>
        <input type="number" id="tunjangan" name="tunjangan" class="form-control rounded-3 bg-light" readonly required>
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label fw-bold small text-uppercase text-muted">Potongan (Rp)</label>
        <input type="number" name="potongan" id="potongan" class="form-control rounded-3 shadow-none" value="0" required onkeyup="hitungTotal()">
      </div>
    </div>

    <!-- Pilihan Status Pembayaran (Dibayar / Pending) -->
    <div class="mb-3">
      <label class="form-label fw-bold small text-uppercase text-muted">Status Pembayaran</label>
      <select name="status_cair" class="form-select rounded-3 shadow-none" required>
        <option value="dibayar">Dibayar (Lunas)</option>
        <option value="pending">Pending (Belum Dibayar)</option>
      </select>
    </div>

    <div class="mb-4 p-3 rounded-3 border" style="background-color: rgba(128, 0, 32, 0.03);">
      <span class="text-muted small fw-bold text-uppercase d-block">Estimasi Total Gaji Bersih:</span>
      <h2 class="fw-bold mb-0" style="color: #800020;" id="label_total_gaji">Rp 0</h2>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" name="proses_gaji" class="btn btn-burgundy-theme px-4 py-2 rounded-3 shadow-sm">
        <i class="bi bi-save me-1"></i> Simpan & Terbitkan Gaji
      </button>
      <a href="index.php?page=gaji" class="btn btn-light border px-4 py-2 rounded-3">Kembali</a>
    </div>
  </form>
</div>
<!-- END: Form Hitung Gaji Card -->

<!-- SCRIPT JAVASCRIPT: Kalkulasi Otomatis Gaji Bersih -->
<script>
function ambilDataGaji(select) {
    let opt = select.options[select.selectedIndex];
    let gp = parseFloat(opt.getAttribute('data-gp')) || 0;
    let tj = parseFloat(opt.getAttribute('data-tj')) || 0;

    document.getElementById('gaji_pokok').value = gp;
    document.getElementById('tunjangan').value = tj;
    hitungTotal();
}

function hitungTotal() {
    let gp = parseFloat(document.getElementById('gaji_pokok').value) || 0;
    let tj = parseFloat(document.getElementById('tunjangan').value) || 0;
    let pot = parseFloat(document.getElementById('potongan').value) || 0;

    let total = (gp + tj) - pot;
    document.getElementById('label_total_gaji').innerText = 'Rp ' + total.toLocaleString('id-ID');
}
</script>