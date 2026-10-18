<?php
/**
 * --------------------------------------------------------------------------
 * CLASS IZIN - OOP VERSION (MENYESUAIKAN STRUKTUR STANDAR)
 * --------------------------------------------------------------------------
 */

if (file_exists('../database/connection.php')) {
    require_once '../database/connection.php';
} elseif (file_exists('../../database/connection.php')) {
    require_once '../../database/connection.php';
} elseif (file_exists('database/connection.php')) {
    require_once 'database/connection.php';
}

class Izin
{
    private $conn;
    private $employeeId;

    public function __construct($employeeId = 0)
    {
        $database = new Database();
        $this->conn = $database->conn;
        $this->employeeId = (int)$employeeId;
    }

    // Method untuk mengajukan izin atau sakit
    public function ajukan($tanggal, $jenis, $keterangan)
    {
        $var_tanggal   = mysqli_real_escape_string($this->conn, $tanggal);
        $var_jenis     = mysqli_real_escape_string($this->conn, $jenis);
        $var_ket       = htmlspecialchars(trim($keterangan), ENT_QUOTES, 'UTF-8');
        $defaultShift  = 1;

        // Simpan ke tabel attendances sebagai izin/sakit
        $stmt = $this->conn->prepare("INSERT INTO attendances (employee_id, shifts_id, tanggal, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $this->employeeId, $defaultShift, $var_tanggal, $var_jenis);
        
        if ($stmt->execute()) {
            // Tambahkan notifikasi opsional
            $judul = "Pengajuan " . ucfirst($var_jenis);
            $pesan = "Keterangan: " . $var_ket;
            $stmtNotif = $this->conn->prepare("INSERT INTO notifications (employee_id, judul, pesan, status_baca) VALUES (?, ?, ?, 'belum')");
            $stmtNotif->bind_param("iss", $this->employeeId, $judul, $pesan);
            $stmtNotif->execute();
            
            return true;
        }
        return false;
    }

    // Method untuk menampilkan riwayat izin
    public function tampilRiwayat()
    {
        $stmt = $this->conn->prepare("SELECT * FROM attendances WHERE employee_id = ? AND status IN ('izin', 'sakit') ORDER BY tanggal DESC");
        $stmt->bind_param("i", $this->employeeId);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>