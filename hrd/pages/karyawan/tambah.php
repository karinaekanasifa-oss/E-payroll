<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN TAMBAH KARYAWAN - OOP VERSION (DENGAN REKENING BANK)
 * --------------------------------------------------------------------------
 */

require_once 'database/connection.php';

$db = new Database();
$conn = $db->conn;

$query_jabatan = $conn->query("SELECT * FROM positions ORDER BY nama_jabatan ASC");

if (isset($_POST['submit'])) {
    $nama        = mysqli_real_escape_string($conn, $_POST['nama']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $password    = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role        = 'karyawan';
    $nip         = mysqli_real_escape_string($conn, $_POST['nip']);
    $position_id = mysqli_real_escape_string($conn, $_POST['position_id']);
    $tgl_masuk   = mysqli_real_escape_string($conn, $_POST['tgl_masuk']);
    $nama_bank   = mysqli_real_escape_string($conn, $_POST['nama_bank']);
    $no_rekening = mysqli_real_escape_string($conn, $_POST['no_rekening']);

    $conn->begin_transaction();

    try {
        $queryUser = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
        $conn->query($queryUser);
        
        $user_id = $conn->insert_id;

        // Menyimpan data karyawan beserta nama_bank dan no_rekening[cite: 10]
        $queryEmp = "INSERT INTO employees (user_id, position_id, nip, tgl_masuk, nama_bank, no_rekening) VALUES ('$user_id', '$position_id', '$nip', '$tgl_masuk', '$nama_bank', '$no_rekening')";
        $conn->query($queryEmp);

        $conn->commit();
        echo "<script>alert('Data karyawan berhasil ditambahkan!'); window.location='index.php?page=karyawan';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Gagal menambahkan karyawan: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Tambah Karyawan</h1>
    <p class="page-subtitle">Formulir pendaftaran pegawai baru, rekening bank, dan pembuatan akun login.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard" class="text-decoration-none text-muted-green">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=karyawan" class="text-decoration-none text-muted-green">Karyawan</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Tambah</li>
    </ol>
  </nav>
</div>

<div class="table-card-custom p-4">
  <form action="" method="POST">
    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="nama" class="form-label fw-bold">Nama Lengkap</label>
        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap karyawan" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="email" class="form-label fw-bold">Email (Untuk Login)</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="contoh@email.com" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="password" class="form-label fw-bold">Password Akun</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 6 karakter" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="nip" class="form-label fw-bold">NIP (Nomor Induk Pegawai)</label>
        <input type="text" class="form-control" id="nip" name="nip" placeholder="Contoh: EMP-2026-001" required>
      </div>

      <!-- Tambahan Input Nama Bank & No Rekening -->
      <div class="col-md-6 mb-3">
        <label for="nama_bank" class="form-label fw-bold">Nama Bank Tujuan</label>
        <input type="text" class="form-control" id="nama_bank" name="nama_bank" placeholder="Contoh: BCA / BSI / Mandiri" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="no_rekening" class="form-label fw-bold">Nomor Rekening</label>
        <input type="text" class="form-control" id="no_rekening" name="no_rekening" placeholder="Contoh: 1234567890" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="position_id" class="form-label fw-bold">Jabatan</label>
        <select class="form-select" id="position_id" name="position_id" required>
          <option value="" disabled selected>-- Pilih Jabatan --</option>
          <?php while ($jabatan = $query_jabatan->fetch_assoc()): ?>
            <option value="<?= $jabatan['id']; ?>">
              <?= htmlspecialchars($jabatan['nama_jabatan']); ?> (Gaji Pokok: Rp <?= number_format($jabatan['gaji_pokok'], 0, ',', '.'); ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label for="tgl_masuk" class="form-label fw-bold">Tanggal Masuk</label>
        <input type="date" class="form-control" id="tgl_masuk" name="tgl_masuk" value="<?= date('Y-m-d'); ?>" required>
      </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
      <a href="index.php?page=karyawan" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
      </a>
      <button type="submit" name="submit" class="btn btn-primary">
        <i class="bi bi-save me-1"></i> Simpan Karyawan
      </button>
    </div>
  </form>
</div>