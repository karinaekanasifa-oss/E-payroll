<?php
require_once 'database/connection.php';
$db = new Database();
$conn = $db->conn;

// Ambil data karyawan
$query_karyawan = $conn->query("SELECT employees.id as emp_id, employees.nip, users.nama FROM employees JOIN users ON employees.user_id = users.id ORDER BY users.nama ASC");

// Ambil data shift untuk pilihan dropdown
$query_shift = $conn->query("SELECT * FROM shifts ORDER BY id ASC");

if (isset($_POST['submit'])) {
    $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);
    $shift_id    = mysqli_real_escape_string($conn, $_POST['shift_id']);
    $tanggal     = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam_masuk   = mysqli_real_escape_string($conn, $_POST['jam_masuk']);
    $jam_keluar  = mysqli_real_escape_string($conn, $_POST['jam_keluar']);
    $status      = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "INSERT INTO attedances (employee_id, shift_id, tanggal, jam_masuk, jam_keluar, status) 
              VALUES ('$employee_id', '$shift_id', '$tanggal', '$jam_masuk', '$jam_keluar', '$status')";

    if ($conn->query($query)) {
        echo "<script>alert('Data absensi berhasil ditambahkan!'); window.location='index.php?page=absensi';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data absensi: " . $conn->error . "');</script>";
    }
}
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Tambah Absensi</h1>
    <p class="page-subtitle">Formulir input data rekapitulasi kehadiran dan shift harian karyawan.</p>
  </div>
</div>

<div class="table-card-custom p-4 bg-white rounded-3 shadow-sm">
  <form action="" method="POST">
    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="employee_id" class="form-label fw-bold">Pilih Karyawan</label>
        <select class="form-select" id="employee_id" name="employee_id" required>
          <option value="" disabled selected>-- Pilih Karyawan --</option>
          <?php while ($emp = $query_karyawan->fetch_assoc()): ?>
            <option value="<?= $emp['emp_id']; ?>"><?= htmlspecialchars($emp['nip'] . ' - ' . $emp['nama']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label for="shift_id" class="form-label fw-bold">Pilih Shift Kerja</label>
        <select class="form-select" id="shift_id" name="shift_id" required>
          <option value="" disabled selected>-- Pilih Shift --</option>
          <?php while ($shf = $query_shift->fetch_assoc()): ?>
            <option value="<?= $shf['id']; ?>"><?= strtoupper(htmlspecialchars($shf['nama_shift'])) . ' (' . $shf['jam_masuk'] . ' - ' . $shf['jam_keluar'] . ')'; ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label for="tanggal" class="form-label fw-bold">Tanggal</label>
        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d'); ?>" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="status" class="form-label fw-bold">Status Kehadiran</label>
        <select class="form-select" id="status" name="status" required>
          <option value="hadir" selected>Hadir</option>
          <option value="izin">Izin</option>
          <option value="sakit">Sakit</option>
          <option value="alfa">Alfa</option>
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label for="jam_masuk" class="form-label fw-bold">Jam Masuk</label>
        <input type="time" class="form-control" id="jam_masuk" name="jam_masuk" value="08:00" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="jam_keluar" class="form-label fw-bold">Jam Keluar</label>
        <input type="time" class="form-control" name="jam_keluar" id="jam_keluar" value="17:00" required>
      </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
      <a href="index.php?page=absensi" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
      <button type="submit" name="submit" class="btn btn-primary" style="background-color: #143423; border: none;"><i class="bi bi-save me-1"></i> Simpan Absensi</button>
    </div>
  </form>
</div>