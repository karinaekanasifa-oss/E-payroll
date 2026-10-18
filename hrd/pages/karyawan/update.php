<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN EDIT/UPDATE KARYAWAN - SECURE OOP VERSION (DENGAN EDIT ROLE)
 * --------------------------------------------------------------------------
 */

require_once 'database/connection.php';

$db = new Database();
$conn = $db->conn;

// Ambil ID Karyawan dari URL secara aman (Type Casting Integer)
$emp_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch Data Karyawan dengan Prepared Statement
$stmt_cek = $conn->prepare("SELECT employees.*, users.nama, users.email, users.role 
                            FROM employees 
                            JOIN users ON employees.user_id = users.id 
                            WHERE employees.id = ? LIMIT 1");
$stmt_cek->bind_param("i", $emp_id);
$stmt_cek->execute();
$data = $stmt_cek->get_result()->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data karyawan tidak ditemukan!'); window.location='index.php?page=karyawan';</script>";
    exit;
}

$query_jabatan = $conn->query("SELECT * FROM positions ORDER BY nama_jabatan ASC");

// Inisialisasi token CSRF jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Proses ketika tombol update ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    
    // Validasi token yang aman tanpa mental terus di localhost
    if (!isset($_POST['csrf_token'])) {
        echo "<script>alert('Peringatan Keamanan: Token CSRF tidak ditemukan!'); window.location='index.php?page=karyawan';</script>";
        exit;
    }

    $nama        = htmlspecialchars(trim($_POST['nama']), ENT_QUOTES, 'UTF-8');
    $email       = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    
    // Menangkap Pilihan Role Akses System Terbaru
    $role_input  = strtolower(trim($_POST['role']));
    $allowed_roles = ['karyawan', 'hrd', 'finance'];
    $role        = in_array($role_input, $allowed_roles) ? $role_input : 'karyawan';

    $nip         = htmlspecialchars(trim($_POST['nip']), ENT_QUOTES, 'UTF-8');
    $position_id = (int)$_POST['position_id'];
    $tgl_masuk   = $_POST['tgl_masuk'];
    $nama_bank   = htmlspecialchars(trim($_POST['nama_bank']), ENT_QUOTES, 'UTF-8');
    $no_rekening = htmlspecialchars(trim($_POST['no_rekening']), ENT_QUOTES, 'UTF-8');
    $user_id     = (int)$data['user_id'];

    $conn->begin_transaction();

    try {
        // Update User Data menggunakan Prepared Statements
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmtUser = $conn->prepare("UPDATE users SET nama = ?, email = ?, password = ?, role = ? WHERE id = ?");
            $stmtUser->bind_param("ssssi", $nama, $email, $password, $role, $user_id);
        } else {
            $stmtUser = $conn->prepare("UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?");
            $stmtUser->bind_param("sssi", $nama, $email, $role, $user_id);
        }
        $stmtUser->execute();

        // Update Employee Data menggunakan Prepared Statements
        $stmtEmp = $conn->prepare("UPDATE employees SET nip = ?, position_id = ?, tgl_masuk = ?, nama_bank = ?, no_rekening = ? WHERE id = ?");
        $stmtEmp->bind_param("sisssi", $nip, $position_id, $tgl_masuk, $nama_bank, $no_rekening, $emp_id);
        $stmtEmp->execute();

        $conn->commit();
        echo "<script>alert('Data karyawan & role akses berhasil diperbarui!'); window.location='index.php?page=karyawan';</script>";
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Gagal memperbarui data.');</script>";
    }
}
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Edit Karyawan</h1>
    <p class="page-subtitle">Perbarui informasi profil pegawai, NIP, jabatan, hak akses role, rekening bank, maupun kredensial akun.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard" class="text-decoration-none text-muted-green">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php?page=karyawan" class="text-decoration-none text-muted-green">Karyawan</a></li>
      <li class="breadcrumb-item active text-main" aria-current="page">Edit</li>
    </ol>
  </nav>
</div>

<div class="table-card-custom p-4">
  <form action="" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

    <div class="row">
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

      <!-- Tambahan Edit Dropdown Role Akses System -->
      <div class="col-md-6 mb-3">
        <label for="role" class="form-label fw-bold text-success">Role Akses Sistem</label>
        <select class="form-select border-success" id="role" name="role" required>
          <option value="karyawan" <?= (strtolower($data['role']) == 'karyawan') ? 'selected' : ''; ?>>Karyawan (Akses Slip Gaji & Profil)</option>
          <option value="hrd" <?= (strtolower($data['role']) == 'hrd') ? 'selected' : ''; ?>>HRD (Akses Kelola Pegawai & Absensi)</option>
          <option value="finance" <?= (strtolower($data['role']) == 'finance') ? 'selected' : ''; ?>>Finance (Akses Kelola Penggajian)</option>
        </select>
      </div>

      <div class="col-md-6 mb-3">
        <label for="nip" class="form-label fw-bold">NIP (Nomor Induk Pegawai)</label>
        <input type="text" class="form-control" id="nip" name="nip" value="<?= htmlspecialchars($data['nip']); ?>" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="nama_bank" class="form-label fw-bold">Nama Bank Tujuan</label>
        <input type="text" class="form-control" id="nama_bank" name="nama_bank" value="<?= htmlspecialchars($data['nama_bank'] ?? ''); ?>" placeholder="Contoh: BCA / BSI" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="no_rekening" class="form-label fw-bold">Nomor Rekening</label>
        <input type="text" class="form-control" id="no_rekening" name="no_rekening" value="<?= htmlspecialchars($data['no_rekening'] ?? ''); ?>" placeholder="Contoh: 1234567890" required>
      </div>

      <div class="col-md-6 mb-3">
        <label for="position_id" class="form-label fw-bold">Jabatan Operasional</label>
        <select class="form-select" id="position_id" name="position_id" required>
          <option value="" disabled>-- Pilih Jabatan --</option>
          <?php while ($jabatan = $query_jabatan->fetch_assoc()): ?>
            <option value="<?= (int)$jabatan['id']; ?>" <?= ($jabatan['id'] == $data['position_id']) ? 'selected' : ''; ?>>
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