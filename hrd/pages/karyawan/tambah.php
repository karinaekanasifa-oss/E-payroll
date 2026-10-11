<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN TAMBAH KARYAWAN - OOP VERSION
 * --------------------------------------------------------------------------
 */

// Menyesuaikan path koneksi database dari folder pages keluar ke root
require_once 'database/connection.php';

// Instansiasi objek database
$db = new Database();
$conn = $db->conn;

// Mengambil data jabatan untuk pilihan (dropdown) pada form
$query_jabatan = $conn->query("SELECT * FROM positions ORDER BY nama_jabatan ASC");

// Jika tombol simpan/submit ditekan
if (isset($_POST['submit'])) {
    $nama        = mysqli_real_escape_string($conn, $_POST['nama']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $password    = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role        = 'karyawan'; // Sesuai ketentuan role di database
    $nip         = mysqli_real_escape_string($conn, $_POST['nip']);
    $position_id = mysqli_real_escape_string($conn, $_POST['position_id']);
    $tgl_masuk   = mysqli_real_escape_string($conn, $_POST['tgl_masuk']);

    // Mulai transaksi database untuk menjaga keamanan data relasi users & employees
    $conn->begin_transaction();

    try {
        // 1. Masukkan data ke tabel users terlebih dahulu
        $queryUser = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
        $conn->query($queryUser);
        
        // Ambil ID user yang baru saja dibuat
        $user_id = $conn->insert_id;

        // 2. Masukkan data ke tabel employees menggunakan user_id tersebut
        $queryEmp = "INSERT INTO employees (user_id, position_id, nip, tgl_masuk) VALUES ('$user_id', '$position_id', '$nip', '$tgl_masuk')";
        $conn->query($queryEmp);

        // Jika sukses, commit transaksi
        $conn->commit();
        echo "<script>alert('Data karyawan berhasil ditambahkan!'); window.location='index.php?page=karyawan';</script>";
    } catch (Exception $e) {
        // Jika gagal, batalkan transaksi
        $conn->rollback();
        echo "<script>alert('Gagal menambahkan karyawan: " . $e->getMessage() . "');</script>";
    }
}
?>

<!-- START: Page Header Banner -->
<div class="page-header">
  <div>
    <h1 class="page-title">Tambah Karyawan</h1>
    <p class="page-subtitle">Formulir pendaftaran pegawai baru dan pembuatan akun login sistem.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard" class="text-decoration-none text-muted-green">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=karyawan" class="text-decoration-none text-muted-green">Karyawan</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Tambah</li>
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

      <!-- Informasi Kepegawaian (Tabel Employees & Positions) -->
      <div class="col-md-6 mb-3">
        <label for="nip" class="form-label fw-bold">NIP (Nomor Induk Pegawai)</label>
        <input type="text" class="form-control" id="nip" name="nip" placeholder="Contoh: EMP-2026-001" required>
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
<!-- END: Form Card Container -->