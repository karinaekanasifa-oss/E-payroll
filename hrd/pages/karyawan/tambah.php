<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN TAMBAH KARYAWAN - SECURE OOP VERSION (DENGAN ROLE & REKENING)
 * --------------------------------------------------------------------------
 */

require_once 'database/connection.php';

$db = new Database();
$conn = $db->conn;

// Inisialisasi token CSRF menggunakan sesi yang sudah aktif dari index.php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$query_jabatan = $conn->query("SELECT * FROM positions ORDER BY nama_jabatan ASC");

// Proses ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    // Sanitasi & Ambil Data Inputan
    $nama        = htmlspecialchars(trim($_POST['nama']), ENT_QUOTES, 'UTF-8');
    $email       = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password    = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Menangkap Role Akses Sistem (Karyawan / HRD / Finance)
    $role_input  = strtolower(trim($_POST['role']));
    $allowed_roles = ['karyawan', 'hrd', 'finance'];
    $role        = in_array($role_input, $allowed_roles) ? $role_input : 'karyawan';

    $nip         = htmlspecialchars(trim($_POST['nip']), ENT_QUOTES, 'UTF-8');
    $position_id = (int)$_POST['position_id'];
    $tgl_masuk   = $_POST['tgl_masuk'];
    $nama_bank   = htmlspecialchars(trim($_POST['nama_bank']), ENT_QUOTES, 'UTF-8');
    $no_rekening = htmlspecialchars(trim($_POST['no_rekening']), ENT_QUOTES, 'UTF-8');

    // Mulai Transaksi Database secara Aman
    $conn->begin_transaction();

    try {
        // 1. Insert ke tabel users (Mencegah SQL Injection dengan Prepared Statements)
        $stmtUser = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
        $stmtUser->bind_param("ssss", $nama, $email, $password, $role);
        $stmtUser->execute();
        
        $user_id = $conn->insert_id;

        // 2. Insert ke tabel employees
        $stmtEmp = $conn->prepare("INSERT INTO employees (user_id, position_id, nip, tgl_masuk, nama_bank, no_rekening) VALUES (?, ?, ?, ?, ?, ?)");
        $stmtEmp->bind_param("iissss", $user_id, $position_id, $nip, $tgl_masuk, $nama_bank, $no_rekening);
        $stmtEmp->execute();

        $conn->commit();
        echo "<script>alert('Data karyawan dan akun role $role berhasil ditambahkan!'); window.location='index.php?page=karyawan';</script>";
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Gagal menambahkan karyawan.');</script>";
    }
}
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Tambah Karyawan</h1>
    <p class="page-subtitle">Formulir pendaftaran pegawai baru, hak akses role, rekening bank, dan pembuatan akun login.</p>
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
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

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

      <!-- Pilihan Dropdown Role Akses Sistem -->
      <div class="col-md-6 mb-3">
        <label for="role" class="form-label fw-bold text-success">Role Akses Sistem</label>
        <select class="form-select border-success" id="role" name="role" required>
          <option value="karyawan" selected>Karyawan (Akses Slip Gaji & Profil)</option>
          <option value="hrd">HRD (Akses Kelola Pegawai & Absensi)</option>
          <option value="finance">Finance (Akses Kelola Penggajian)</option>
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label for="nip" class="form-label fw-bold">NIP (Nomor Induk Pegawai)</label>
        <input type="text" class="form-control" id="nip" name="nip" placeholder="Contoh: EMP-2026-001" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="nama_bank" class="form-label fw-bold">Nama Bank Tujuan</label>
        <input type="text" class="form-control" id="nama_bank" name="nama_bank" placeholder="Contoh: BCA / BSI / Mandiri" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="no_rekening" class="form-label fw-bold">Nomor Rekening</label>
        <input type="text" class="form-control" id="no_rekening" name="no_rekening" placeholder="Contoh: 1234567890" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="position_id" class="form-label fw-bold">Jabatan Operasional</label>
        <select class="form-select" id="position_id" name="position_id" required>
          <option value="" disabled selected>-- Pilih Jabatan --</option>
          <?php while ($jabatan = $query_jabatan->fetch_assoc()): ?>
            <option value="<?= (int)$jabatan['id']; ?>">
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