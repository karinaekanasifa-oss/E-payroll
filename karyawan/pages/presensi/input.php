<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN INPUT / FORM PRESENSI HARI INI - OOP VERSION
 * --------------------------------------------------------------------------
 */

// Memasukkan file koneksi database
if (file_exists('database/connection.php')) {
    require_once 'database/connection.php';
} elseif (file_exists('../database/connection.php')) {
    require_once '../database/connection.php';
} elseif (file_exists('../../database/connection.php')) {
    require_once '../../database/connection.php';
}

class InputPresensiPage {
    private $conn;
    private $employee_id;

    public function __construct($employee_id) {
        $db = new Database();
        $this->conn = $db->conn;
        $this->employee_id = (int) $employee_id;
    }

    // Mengambil data presensi hari ini
    public function getPresensiHariIni() {
        $tanggal = date('Y-m-d');
        $stmt = $this->conn->prepare("SELECT * FROM attendances WHERE employee_id = ? AND tanggal = ? LIMIT 1");
        $stmt->bind_param("is", $this->employee_id, $tanggal);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Proses pencatatan presensi masuk atau keluar
    public function catatPresensi($action) {
        $tanggal = date('Y-m-d');
        $waktu = date('H:i:s');
        $statusEnum = 'hadir';
        $defaultShiftId = 1;

        if ($action === 'masuk') {
            $stmt = $this->conn->prepare("INSERT INTO attendances (employee_id, shifts_id, tanggal, jam_masuk, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $this->employee_id, $defaultShiftId, $tanggal, $waktu, $statusEnum);
            return $stmt->execute();
        } elseif ($action === 'keluar') {
            $stmt = $this->conn->prepare("UPDATE attendances SET jam_keluar = ? WHERE employee_id = ? AND tanggal = ?");
            $stmt->bind_param("sis", $waktu, $this->employee_id, $tanggal);
            return $stmt->execute();
        }
        return false;
    }
}

$employee_id = $_SESSION['employee_id'] ?? 0;
$inputObj = new InputPresensiPage($employee_id);

// Handling aksi form submit (masuk / keluar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($inputObj->catatPresensi($action)) {
        echo "<script>alert('Berhasil mencatat presensi $action!'); window.location='index.php?page=presensi';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal mencatat presensi. Silakan coba lagi.'); window.location='index.php?page=input-presensi';</script>";
        exit;
    }
}

$hariIni = $inputObj->getPresensiHariIni();
?>

<style>
.btn-template-theme { background-color: #143423; color: #fff; border: none; }
.btn-template-theme:hover { background-color: #1b4731; color: #fff; }
</style>

<div class="page-header mb-4">
  <div>
    <h1 class="page-title fw-bold text-dark">Form Absensi Harian</h1>
    <p class="page-subtitle text-muted">Lakukan pencatatan jam masuk dan jam keluar kantor Anda secara tepat waktu.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard" class="text-decoration-none text-success">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=presensi" class="text-decoration-none text-success">Presensi</a></li>
      <li class="breadcrumb-item active text-dark" aria-current="page">Form Input</li>
    </ol>
  </nav>
</div>

<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white text-center">
      
      <div class="mb-4">
        <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-semibold border">
          <i class="bi bi-calendar-day text-success me-2"></i> Tanggal Hari Ini: <?= date('d F Y'); ?>
        </span>
      </div>

      <!-- Kotak Tampilan Jam Masuk & Keluar -->
      <div class="row g-3 mb-4">
        <div class="col-6">
          <div class="p-3 bg-light rounded-3 border-start border-4 border-success text-start">
            <small class="text-muted d-block fw-semibold mb-1">Jam Masuk</small>
            <span class="fw-bold text-success fs-4">
              <?= !empty($hariIni['jam_masuk']) ? htmlspecialchars($hariIni['jam_masuk']) : '-- : --'; ?>
            </span>
          </div>
        </div>
        <div class="col-6">
          <div class="p-3 bg-light rounded-3 border-start border-4 border-danger text-start">
            <small class="text-muted d-block fw-semibold mb-1">Jam Keluar</small>
            <span class="fw-bold text-danger fs-4">
              <?= !empty($hariIni['jam_keluar']) ? htmlspecialchars($hariIni['jam_keluar']) : '-- : --'; ?>
            </span>
          </div>
        </div>
      </div>

      <!-- Form Eksekusi Tombol Absen -->
      <form action="" method="POST">
        <?php if (empty($hariIni['jam_masuk'])): ?>
          <button type="submit" name="action" value="masuk" class="btn btn-success w-100 py-3 fw-bold rounded-pill shadow-sm fs-5">
            <i class="bi bi-box-arrow-in-right me-2"></i> Klik Untuk Absen Masuk
          </button>
        <?php elseif (empty($hariIni['jam_keluar'])): ?>
          <button type="submit" name="action" value="keluar" class="btn btn-danger w-100 py-3 fw-bold rounded-pill shadow-sm fs-5">
            <i class="bi bi-box-arrow-left me-2"></i> Klik Untuk Absen Keluar
          </button>
        <?php else: ?>
          <div class="alert alert-success py-3 mb-3 rounded-pill fw-semibold shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i> Presensi masuk & keluar hari ini sudah lengkap tercatat!
          </div>
        <?php endif; ?>
      </form>

      <div class="mt-4">
        <a href="index.php?page=presensi" class="text-secondary text-decoration-none fw-semibold">
          <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat Presensi
        </a>
      </div>

    </div>
  </div>
</div>