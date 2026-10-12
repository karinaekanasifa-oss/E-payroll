<?php
// Pastikan error reporting aktif sementara untuk melacak jika ada salah ketik
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/connection.php';

$db = new Database();
$conn = $db->conn;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query_cek = $conn->query("SELECT * FROM shifts WHERE id = $id");
$data = $query_cek->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data shift tidak ditemukan!'); window.location='index.php?page=shift';</script>";
    exit;
}

if (isset($_POST['update_shift'])) {
    $nama_shift = mysqli_real_escape_string($conn, $_POST['nama_shift']);
    $jam_masuk  = mysqli_real_escape_string($conn, $_POST['jam_masuk']);
    $jam_keluar = mysqli_real_escape_string($conn, $_POST['jam_keluar']);

    $update = $conn->query("UPDATE shifts SET nama_shift = '$nama_shift', jam_masuk = '$jam_masuk', jam_keluar = '$jam_keluar' WHERE id = $id");

    if ($update) {
        echo "<script>alert('Berhasil diperbarui!'); window.location='index.php?page=shift';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal diperbarui.');</script>";
    }
}
?>

<div class="container py-4">
    <h3>Edit Shift Kerja</h3>
    <hr>
    <form method="POST" action="index.php?page=edit-shift&id=<?= $id; ?>">
        <div class="mb-3">
            <label class="form-label fw-bold">Nama Shift</label>
            <select class="form-select" name="nama_shift" required>
                <option value="pagi" <?= ($data['nama_shift'] == 'pagi') ? 'selected' : ''; ?>>Pagi</option>
                <option value="siang" <?= ($data['nama_shift'] == 'siang') ? 'selected' : ''; ?>>Siang</option>
                <option value="sore" <?= ($data['nama_shift'] == 'sore') ? 'selected' : ''; ?>>Sore</option>
                <option value="malam" <?= ($data['nama_shift'] == 'malam') ? 'selected' : ''; ?>>Malam</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Jam Masuk</label>
            <input type="time" class="form-control" name="jam_masuk" value="<?= $data['jam_masuk']; ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Jam Keluar</label>
            <input type="time" class="form-control" name="jam_keluar" value="<?= $data['jam_keluar']; ?>" required>
        </div>
        <button type="submit" name="update_shift" class="btn btn-success">Simpan Perubahan</button>
        <a href="index.php?page=shift" class="btn btn-secondary">Kembali</a>
    </form>
</div>