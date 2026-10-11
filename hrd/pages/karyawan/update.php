<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN EDIT/UPDATE KARYAWAN - OOP VERSION (USERS & EMPLOYEES)
 * --------------------------------------------------------------------------
 */

require_once 'database/connection.php';

$db = new Database();
$conn = $db->conn;

// Ambil ID dari URL (id dari tabel employees)
$emp_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data karyawan yang ada berdasarkan emp_id, JOIN dengan users & positions
$query_cek = $conn->query("SELECT employees.*, users.nama, users.email, users.role 
                           FROM employees 
                           JOIN users ON employees.user_id = users.id 
                           WHERE employees.id = $emp_id");
$data = $query_cek->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data karyawan tidak ditemukan!'); window.location='index.php?page=karyawan';</script>";
    exit;
}

// Ambil data jabatan untuk pilihan dropdown
$query_jabatan = $conn->query("SELECT * FROM positions ORDER BY nama_jabatan ASC");

// Jika tombol update ditekan
if (isset($_POST['update'])) {
    $nama        = mysqli_real_escape_string($conn, $_POST['nama']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $nip         = mysqli_real_escape_string($conn, $_POST['nip']);
    $position_id = mysqli_real_escape_string($conn, $_POST['position_id']);
    $tgl_masuk   = mysqli_real_escape_string($conn, $_POST['tgl_masuk']);
    $user_id     = $data['user_id'];

    // Cek apakah password diisi (jika ingin diubah)
    $password_query = "";
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_query = ", password = '$password'";
    }

    // Mulai transaksi database
    $conn->begin_transaction();

    try {
        // 1. Update tabel users (nama, email, password jika diisi)
        $updateUser = "UPDATE users SET nama = '$nama', email = '$email' $password_query WHERE id = $user_id";
        $conn->query($updateUser);

        // 2. Update tabel employees (nip, position_id, tgl_masuk)
        $updateEmp = "UPDATE employees SET nip = '$nip', position_id = '$position_id', tgl_masuk = '$tgl_masuk' WHERE id = $emp_id";
        $conn->query($updateEmp);

        // Commit transaksi jika sukses
        $conn->commit();
        echo "<script>alert('Data karyawan berhasil diperbarui!'); window.location='index.php?page=karyawan';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Gagal memperbarui data: " . $e->getMessage() . "');</script>";
    }
}
?>

<!-- START: Page Header Banner -->
<div class="page-header">
  <div>
    <h1 class="page-title">Edit Karyawan</h1>
    <p class="page-subtitle">Perbarui informasi profil pegawai, NIP, jabatan, maupun kredensial akun.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard" class="text-decoration-none text-muted-green">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=karyawan" class="text-decoration-none text-muted-green">Karyawan</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Edit</li>
    </ol>
  </nav>
</div>
<!-- END: Page Header Banner -->

<!-- START: Form Card Container -->
<div class="table-card-custom p-4">
  <form action="" method="POST">
    <div class="row">
      <!-- Informasi Akun (Tabel Users) -->
      <div class="col-md-6 mb-3">
        <label for="nama" class="form-label fw-bold">Nama Lengkap</label>
        <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="email" class="form-label fw-bold">Email (Untuk Login)</label>
        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="password" class="form-label fw-bold">Password Baru <span class="text-muted fw-normal">(Opsional)</span></label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
      </div>

      <!-- Informasi Kepegawaian (Tabel Employees & Positions) -->
      <div class="col-md-6 mb-3">
        <label for="nip" class="form-label fw-bold">NIP (Nomor Induk Pegawai)</label>
        <input type="text" class="form-control" id="nip" name="nip" value="<?= htmlspecialchars($data['nip']); ?>" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="position_id" class="form-label fw-bold">Jabatan</label>
        <select class="form-select" id="position_id" name="position_id" required>
          <option value="" disabled>-- Pilih Jabatan --</option>
          <?php while ($jabatan = $query_jabatan->fetch_assoc()): ?>
            <option value="<?= $jabatan['id']; ?>" <?= ($jabatan['id'] == $data['position_id']) ? 'selected' : ''; ?>>
              <?= htmlspecialchars($jabatan['nama_jabatan']); ?> (Gaji Pokok: Rp <?= number_format($jabatan['gaji_pokok'], 0, ',', '.'); ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label for="tgl_masuk" class="form-label fw-bold">Tanggal Masuk</label>
        <input type="date" class="form-control" id="tgl_masuk" name="tgl_masuk" value="<?= htmlspecialchars($data['tgl_masuk']); ?>" required>
      </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
      <a href="index.php?page=karyawan" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
      </a>
      <button type="submit" name="update" class="btn btn-primary">
        <i class="bi bi-save me-1"></i> Perbarui Karyawan
      </button>
    </div>
  </form>
</div>
<!-- END: Form Card Container -->