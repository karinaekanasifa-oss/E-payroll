<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN TAMBAH ABSENSI - OOP VERSION
 * --------------------------------------------------------------------------
 */

// Menyesuaikan path koneksi database dari folder pages keluar ke root
require_once 'database/connection.php';

// Instansiasi objek database
$db = new Database();
$conn = $db->conn;

// Mengambil data karyawan untuk pilihan dropdown (JOIN employees & users)
$query_karyawan = $conn->query("SELECT employees.id as emp_id, employees.nip, users.nama 
                                FROM employees 
                                JOIN users ON employees.user_id = users.id 
                                ORDER BY users.nama ASC");

// Jika tombol submit ditekan
if (isset($_POST['submit'])) {
    $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);
    $tanggal     = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam_masuk   = mysqli_real_escape_string($conn, $_POST['jam_masuk']);
    $jam_keluar  = mysqli_real_escape_string($conn, $_POST['jam_keluar']);
    $status      = mysqli_real_escape_string($conn, $_POST['status']);

    // Query insert ke tabel attedances (sesuai nama tabel di database kamu)
    $query = "INSERT INTO attedances (employee_id, tanggal, jam_masuk, jam_keluar, status) 
              VALUES ('$employee_id', '$tanggal', '$jam_masuk', '$jam_keluar', '$status')";

    if ($conn->query($query)) {
        echo "<script>alert('Data absensi berhasil ditambahkan!'); window.location='index.php?page=absensi';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data absensi: " . $conn->error . "');</script>";
    }
}
?>

<!-- START: Page Header Banner -->
<div class="page-header">
  <div>
    <h1 class="page-title">Tambah Absensi</h1>
    <p class="page-subtitle">Formulir input data rekapitulasi kehadiran harian karyawan.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard" class="text-decoration-none text-muted-green">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=absensi" class="text-decoration-none text-muted-green">Absensi</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Tambah</li>
    </ol>
  </nav>
</div>
<!-- END: Page Header Banner -->

<!-- START: Form Card Container -->
<div class="table-card-custom p-4">
  <form action="" method="POST">
    <div class="row">
      <!-- Pilihan Karyawan -->
      <div class="col-md-12 mb-3">
        <label for="employee_id" class="form-label fw-bold">Pilih Karyawan</label>
        <select class="form-select" id="employee_id" name="employee_id" required>
          <option value="" disabled selected>-- Pilih Karyawan --</option>
          <?php while ($emp = $query_karyawan->fetch_assoc()): ?>
            <option value="<?= $emp['emp_id']; ?>">
              <?= htmlspecialchars($emp['nip']); ?> - <?= htmlspecialchars($emp['nama']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Tanggal Kehadiran -->
      <div class="col-md-6 mb-3">
        <label for="tanggal" class="form-label fw-bold">Tanggal</label>
        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d'); ?>" required>
      </div>

      <!-- Status Kehadiran -->
      <div class="col-md-6 mb-3">
        <label for="status" class="form-label fw-bold">Status Kehadiran</label>
        <select class="form-select" id="status" name="status" required>
          <option value="" disabled selected>-- Pilih Status --</option>
          <option value="hadir">Hadir</option>
          <option value="izin">Izin</option>
          <option value="sakit">Sakit</option>
          <option value="alfa">Alfa</option>
        </select>
      </div>

      <!-- Jam Masuk -->
      <div class="col-md-6 mb-3">
        <label for="jam_masuk" class="form-label fw-bold">Jam Masuk</label>
        <input type="time" class="form-control" id="jam_masuk" name="jam_masuk" value="08:00" required>
      </div>

      <!-- Jam Keluar -->
      <div class="col-md-6 mb-3">
        <label for="jam_keluar" class="form-label fw-bold">Jam Keluar</label>
        <input type="time" class="form-control" id="jam_keluar" name="jam_keluar" value="17:00" required>
      </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
      <a href="index.php?page=absensi" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
      </a>
      <button type="submit" name="submit" class="btn btn-primary">
        <i class="bi bi-save me-1"></i> Simpan Absensi
      </button>
    </div>
  </form>
</div>
<!-- END: Form Card Container -->