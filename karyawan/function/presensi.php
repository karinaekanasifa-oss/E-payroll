<?php
/**
 * --------------------------------------------------------------------------
 * CLASS PRESENSI - OOP VERSION (MENYESUAIKAN STRUKTUR STANDAR)
 * --------------------------------------------------------------------------
 */

if (file_exists('../database/connection.php')) {
    require_once '../database/connection.php';
} elseif (file_exists('../../database/connection.php')) {
    require_once '../../database/connection.php';
} elseif (file_exists('database/connection.php')) {
    require_once 'database/connection.php';
}

class Presensi
{
    private $conn;
    private $employeeId;

    public function __construct($employeeId = 0)
    {
        $database = new Database();
        $this->conn = $database->conn;
        $this->employeeId = (int)$employeeId;
    }

    // Method untuk menampilkan presensi hari ini
    public function getPresensiHariIni()
    {
        $tanggal = date('Y-m-d');
        $stmt = $this->conn->prepare("SELECT * FROM attendances WHERE employee_id = ? AND tanggal = ? LIMIT 1");
        $stmt->bind_param("is", $this->employeeId, $tanggal);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Method untuk mencatat presensi masuk atau keluar
    public function catatPresensi($action)
    {
        $tanggal = date('Y-m-d');
        $waktu = date('H:i:s');
        $statusEnum = 'hadir';
        $defaultShiftId = 1;

        if ($action === 'masuk') {
            $stmt = $this->conn->prepare("INSERT INTO attendances (employee_id, shifts_id, tanggal, jam_masuk, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $this->employeeId, $defaultShiftId, $tanggal, $waktu, $statusEnum);
            return $stmt->execute();
        } elseif ($action === 'keluar') {
            $stmt = $this->conn->prepare("UPDATE attendances SET jam_keluar = ? WHERE employee_id = ? AND tanggal = ?");
            $stmt->bind_param("sis", $waktu, $this->employeeId, $tanggal);
            return $stmt->execute();
        }
        return false;
    }

    // Method untuk menampilkan riwayat presensi karyawan
    public function tampilRiwayat()
    {
        $stmt = $this->conn->prepare("SELECT * FROM attendances WHERE employee_id = ? ORDER BY tanggal DESC");
        $stmt->bind_param("i", $this->employeeId);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>