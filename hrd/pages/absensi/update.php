<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN EDIT ABSENSI - OOP VERSION
 * --------------------------------------------------------------------------
 */

require_once 'database/connection.php';

class EditAttendanceOOP {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Mengambil data absensi berdasarkan ID
    public function getAttendanceById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM attedances WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Mengambil daftar karyawan untuk dropdown
    public function getAllEmployees() {
        return $this->conn->query("SELECT employees.id, employees.nip, users.nama FROM employees JOIN users ON employees.user_id = users.id");
    }

    // Proses Update Data Absensi
    public function updateAttendance($id, $employee_id, $tanggal, $jam_masuk, $jam_keluar, $status) {
        $stmt = $this->conn->prepare("UPDATE attedances SET employee_id = ?, tanggal = ?, jam_masuk = ?, jam_keluar = ?, status = ? WHERE id = ?");
        $stmt->bind_param("issssi", $employee_id, $tanggal, $jam_masuk, $jam_keluar, $status, $id);
        return $stmt->execute();
    }
}

$editObj = new EditAttendanceOOP();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$attendance = $editObj->getAttendanceById($id);

if (!$attendance) {
    echo "<script>alert('Data absensi tidak ditemukan!'); window.location='index.php?page=absensi';</script>";
    exit;
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $tanggal     = $_POST['tanggal'];
    $jam_masuk   = !empty($_POST['jam_masuk']) ? $_POST['jam_masuk'] : NULL;
    $jam_keluar  = !empty($_POST['jam_keluar']) ? $_POST['jam_keluar'] : NULL;
    $status      = $_POST['status'];

    if ($editObj->updateAttendance($id, $employee_id, $tanggal, $jam_masuk, $jam_keluar, $status)) {
        echo "<script>alert('Data absensi berhasil diperbarui!'); window.location='index.php?page=absensi';</script>";
        exit;
    } else {
        $error = "Gagal memperbarui data absensi.";
    }
}

$employees = $editObj->getAllEmployees();
?>

<!-- START: Page Header -->
<div class="page-header mb-4">
  <div>
    <h1 class="page-title fw-bold text-dark">Edit Absensi</h1>
    <p class="page-subtitle text-muted">Perbarui data presensi kehadiran karyawan.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard" class="text-decoration-none text-success">Home</a></li>
      <li class="breadcrumb-item text-muted">HRD</li>
      <li class="breadcrumb-item"><a href="index.php?page=absensi" class="text-decoration-none text-success">Absensi</a></li>
      <li class="breadcrumb-item active text-dark" aria-current="page">Edit</li>
    </ol>
  </nav>
</div>
<!-- END: Page Header -->

<!-- START: Form Edit Card -->
<div class="card border-0 shadow-sm rounded-3 p-4 bg-white" style="max-width: 700px;">
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2"><?= $error; ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="mb-3">
      <label class="form-label fw-bold small text-uppercase">Nama Karyawan</label>
      <select name="employee_id" class="form-select" required>
        <option value="">-- Pilih Karyawan --</option>
        <?php while ($emp = $employees->fetch_assoc()): ?>
          <option value="<?= $emp['id']; ?>" <?= ($emp['id'] == $attendance['employee_id']) ? 'selected' : ''; ?>>
            <?= htmlspecialchars($emp['nama'] . ' (' . $emp['nip'] . ')'); ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label fw-bold small text-uppercase">Tanggal Presensi</label>
      <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($attendance['tanggal']); ?>" required>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label fw-bold small text-uppercase">Jam Masuk</label>
        <input type="time" name="jam_masuk" class="form-control" value="<?= htmlspecialchars($attendance['jam_masuk']); ?>">
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label fw-bold small text-uppercase">Jam Keluar</label>
        <input type="time" name="jam_keluar" class="form-control" value="<?= htmlspecialchars($attendance['jam_keluar']); ?>">
      </div>
    </div>

    <div class="mb-4">
      <label class="form-label fw-bold small text-uppercase">Status Kehadiran</label>
      <select name="status" class="form-select" required>
        <option value="hadir" <?= ($attendance['status'] == 'hadir') ? 'selected' : ''; ?>>Hadir</option>
        <option value="izin" <?= ($attendance['status'] == 'izin') ? 'selected' : ''; ?>>Izin</option>
        <option value="sakit" <?= ($attendance['status'] == 'sakit') ? 'selected' : ''; ?>>Sakit</option>
        <option value="alfa" <?= ($attendance['status'] == 'alfa') ? 'selected' : ''; ?>>Alfa</option>
      </select>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn text-white px-4 py-2 rounded-2 shadow-sm" style="background-color: #143423;">Simpan Perubahan</button>
      <a href="index.php?page=absensi" class="btn btn-light border px-4 py-2 rounded-2">Batal</a>
    </div>
  </form>
</div>
<!-- END: Form Edit Card -->