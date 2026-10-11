<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN HAPUS ABSENSI - OOP VERSION
 * --------------------------------------------------------------------------
 */

require_once 'database/connection.php';

class DeleteAttendanceOOP {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function deleteAttendance($id) {
        $stmt = $this->conn->prepare("DELETE FROM attedances WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $deleteObj = new DeleteAttendanceOOP();
    if ($deleteObj->deleteAttendance($id)) {
        echo "<script>alert('Data absensi berhasil dihapus!'); window.location='index.php?page=absensi';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menghapus data absensi.'); window.location='index.php?page=absensi';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location='index.php?page=absensi';</script>";
    exit;
}
?>