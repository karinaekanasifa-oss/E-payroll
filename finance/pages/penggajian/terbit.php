<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN TERBIT & CETAK SLIP GAJI - OOP VERSION (BURGUNDY THEME)
 * --------------------------------------------------------------------------
 */

if (file_exists('database/connection.php')) {
    require_once 'database/connection.php';
} elseif (file_exists('../database/connection.php')) {
    require_once '../database/connection.php';
} elseif (file_exists('../../database/connection.php')) {
    require_once '../../database/connection.php';
}

class PrintSlipOOP {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Mengambil daftar seluruh slip gaji untuk dropdown
    public function getAllPayrollsList() {
        $query = "SELECT payrolls.id, payrolls.bulan_tahun, users.nama, positions.nama_jabatan 
                  FROM payrolls 
                  JOIN employees ON payrolls.employee_id = employees.id 
                  JOIN users ON employees.user_id = users.id 
                  JOIN positions ON employees.position_id = positions.id 
                  ORDER BY payrolls.id DESC";
        return $this->conn->query($query);
    }

    // Mengambil detail slip gaji spesifik beserta nama_bank & no_rekening
    public function getPayrollDetail($id = 0) {
        if ($id > 0) {
            $stmt = $this->conn->prepare("SELECT payrolls.*, employees.nip, employees.nama_bank, employees.no_rekening, users.nama, positions.nama_jabatan, positions.gaji_pokok, positions.tunjangan 
                                          FROM payrolls 
                                          JOIN employees ON payrolls.employee_id = employees.id 
                                          JOIN users ON employees.user_id = users.id 
                                          JOIN positions ON employees.position_id = positions.id 
                                          WHERE payrolls.id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } else {
            $query = "SELECT payrolls.*, employees.nip, employees.nama_bank, employees.no_rekening, users.nama, positions.nama_jabatan, positions.gaji_pokok, positions.tunjangan 
                      FROM payrolls 
                      JOIN employees ON payrolls.employee_id = employees.id 
                      JOIN users ON employees.user_id = users.id 
                      JOIN positions ON employees.position_id = positions.id 
                      ORDER BY payrolls.id DESC LIMIT 1";
            return $this->conn->query($query)->fetch_assoc();
        }
    }
}

$printObj = new PrintSlipOOP();
$list_payroll = $printObj->getAllPayrollsList();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$slip = $printObj->getPayrollDetail($id);

if (!$slip) {
    echo "<div class='card border-0 shadow-sm rounded-4 p-5 bg-white text-center' style='max-width: 600px; margin: 40px auto;'>
            <h4 class='fw-bold text-danger mb-2'>Belum Ada Data Slip Gaji</h4>
            <p class='text-muted mb-4'>Silakan lakukan proses perhitungan gaji terlebih dahulu pada menu Hitung Gaji.</p>
            <a href='index.php?page=hitung' class='btn btn-dark px-4 py-2 rounded-3'>Proses Hitung Gaji</a>
          </div>";
    return;
}
?>

<style>
@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.page-header, .slip-card {
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

/* PENGATURAN CETAK / PRINT PDF BERSIH */
@media print {
  @page {
    size: A4 portrait;
    margin: 10mm;
  }
  body {
    background-color: #fff !important;
    margin: 0 !important;
    padding: 0 !important;
  }
  body * {
    visibility: hidden;
  }
  #printableSlip, #printableSlip * {
    visibility: visible;
  }
  #printableSlip {
    position: absolute;
    left: 0;
    top: 0;
    width: 100% !important;
    max-width: 100% !important;
    box-shadow: none !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
  }
  .no-print {
    display: none !important;
  }
}
</style>

<!-- Header Banner -->
<div class="page-header mb-4 no-print">
  <div>
    <h1 class="page-title fw-bold" style="color: #800020;">Terbit & Cetak Slip Gaji</h1>
    <p class="page-subtitle text-muted">Pratinjau dokumen resmi slip gaji bulanan pegawai.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard-finance" class="text-decoration-none" style="color: #800020;">Home</a></li>
      <li class="breadcrumb-item text-muted">Finance</li>
      <li class="breadcrumb-item"><a href="index.php?page=gaji" class="text-decoration-none" style="color: #800020;">Penggajian</a></li>
      <li class="breadcrumb-item active text-dark" aria-current="page">Terbit Slip</li>
    </ol>
  </nav>
</div>

<!-- Controls & Dropdown Pilih Pegawai -->
<div class="mb-4 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
  <div class="d-flex gap-2">
    <button onclick="window.print()" class="btn btn-burgundy-theme px-4 py-2 rounded-3 shadow-sm">
      <i class="bi bi-printer-fill me-1"></i> Cetak Dokumen Slip
    </button>
    <a href="index.php?page=gaji" class="btn btn-light border px-4 py-2 rounded-3">
      <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
  </div>

  <div style="min-width: 280px;">
    <select class="form-select rounded-3 shadow-none border" onchange="if(this.value) window.location.href='index.php?page=terbit&id='+this.value">
      <option value="" disabled selected>-- Pilih Pegawai / Periode --</option>
      <?php while ($row_p = $list_payroll->fetch_assoc()): ?>
        <option value="<?= $row_p['id']; ?>" <?= ($id == $row_p['id'] || ($id == 0 && $row_p['id'] == $slip['id'])) ? 'selected' : ''; ?>>
          <?= htmlspecialchars($row_p['nama'] . ' (' . $row_p['nama_jabatan'] . ' - ' . $row_p['bulan_tahun'] . ')'); ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>
</div>

<!-- Lembar Slip Gaji printable -->
<div class="card border-0 shadow-sm rounded-4 p-5 bg-white slip-card mx-auto" id="printableSlip" style="max-width: 750px;">
  <!-- Kop Surat -->
  <div class="text-center border-bottom pb-4 mb-4">
    <h3 class="fw-bold text-uppercase mb-1" style="color: #800020;">PT E-PAYROLL INDONESIA</h3>
    <p class="text-muted small mb-0">Laporan Resmi Penerbitan Komponen Gaji & Pendapatan Karyawan</p>
    <span class="badge bg-light text-dark border mt-2 px-3 py-1">Periode: <?= htmlspecialchars($slip['bulan_tahun']); ?></span>
  </div>

  <!-- Detail Informasi Pegawai & Bank -->
  <div class="row mb-4 fs-6">
    <div class="col-6">
      <table class="table table-sm table-borderless mb-0">
        <tr>
          <td class="text-muted fw-bold" style="width: 120px;">NIP</td>
          <td>: <?= htmlspecialchars($slip['nip']); ?></td>
        </tr>
        <tr>
          <td class="text-muted fw-bold">Nama Pegawai</td>
          <td>: <?= htmlspecialchars($slip['nama']); ?></td>
        </tr>
        <tr>
          <td class="text-muted fw-bold">Rekening Bank</td>
          <td>: <?= htmlspecialchars(($slip['nama_bank'] ?? 'Belum diatur') . ' - ' . ($slip['no_rekening'] ?? 'Belum diatur')); ?></td>
        </tr>
      </table>
    </div>
    <div class="col-6">
      <table class="table table-sm table-borderless mb-0">
        <tr>
          <td class="text-muted fw-bold" style="width: 120px;">Jabatan</td>
          <td>: <?= htmlspecialchars($slip['nama_jabatan']); ?></td>
        </tr>
        <tr>
          <td class="text-muted fw-bold">Status Cair</td>
          <td>: 
            <?php if (strtolower($slip['status_cair']) == 'dibayar'): ?>
              <span class="badge bg-success text-uppercase">Dibayar</span>
            <?php else: ?>
              <span class="badge bg-warning text-dark text-uppercase">Pending</span>
            <?php endif; ?>
          </td>
        </tr>
      </table>
    </div>
  </div>

  <!-- Tabel Komponen Gaji -->
  <div class="table-responsive mb-4">
    <table class="table table-bordered align-middle mb-0">
      <thead class="table-light text-uppercase fs-7 text-muted text-center">
        <tr>
          <th class="py-3">Komponen Pendapatan & Potongan</th>
          <th class="py-3 text-end" style="width: 220px;">Jumlah (IDR)</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Gaji Pokok Utama</td>
          <td class="text-end">Rp <?= number_format($slip['gaji_pokok'], 0, ',', '.'); ?></td>
        </tr>
        <tr>
          <td>Tunjangan Jabatan</td>
          <td class="text-end">Rp <?= number_format($slip['tunjangan'], 0, ',', '.'); ?></td>
        </tr>
        <tr>
          <td class="text-danger">Potongan Keterlambatan / Absensi</td>
          <td class="text-end text-danger">- Rp <?= number_format($slip['potongan'], 0, ',', '.'); ?></td>
        </tr>
        <tr class="table-light fw-bold">
          <td class="text-uppercase" style="color: #800020;">TOTAL GAJI BERSIH (TAKE HOME PAY)</td>
          <td class="text-end fs-5" style="color: #800020;">Rp <?= number_format($slip['total_gaji'], 0, ',', '.'); ?></td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Tanda Tangan -->
  <div class="row mt-5 pt-3">
    <div class="col-6"></div>
    <div class="col-6 text-center">
      <p class="mb-1 text-muted small">Mengetahui,</p>
      <p class="fw-bold mb-5" style="color: #800020;">Finance Department</p>
      <p class="fw-bold border-top pt-1 d-inline-block px-4 mb-0">Administrator Keuangan</p>
    </div>
  </div>
</div>