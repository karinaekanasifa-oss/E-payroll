<?php
/**
 * --------------------------------------------------------------------------
 * CLASS JABATAN - SECURE OOP VERSION
 * --------------------------------------------------------------------------
 */

if (file_exists('../database/connection.php')) {
    require_once '../database/connection.php';
} elseif (file_exists('database/connection.php')) {
    require_once 'database/connection.php';
}

class Jabatan {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Helper untuk membersihkan format titik rupiah menjadi integer bersih
    private function cleanNumber($number) {
        return (int) preg_replace('/[^0-9]/', '', $number);
    }

    // 1. Tambah Data Jabatan (Secure Prepared Statements)
    public function tambah($nama_jabatan, $gaji_pokok, $tunjangan) {
        $nama_clean = htmlspecialchars(trim($nama_jabatan), ENT_QUOTES, 'UTF-8');
        $gaji_clean = $this->cleanNumber($gaji_pokok);
        $tunj_clean = $this->cleanNumber($tunjangan);

        $stmt = $this->conn->prepare("INSERT INTO positions (nama_jabatan, gaji_pokok, tunjangan) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $nama_clean, $gaji_clean, $tunj_clean);
        return $stmt->execute();
    }

    // 2. Ambil Data Jabatan Berdasarkan ID (Secure Type Casting)
    public function ambilDataById($id) {
        $id_clean = (int) $id;
        $stmt = $this->conn->prepare("SELECT * FROM positions WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id_clean);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 3. Update Data Jabatan (Secure Prepared Statements)
    public function update($id, $nama_jabatan, $gaji_pokok, $tunjangan) {
        $id_clean   = (int) $id;
        $nama_clean = htmlspecialchars(trim($nama_jabatan), ENT_QUOTES, 'UTF-8');
        $gaji_clean = $this->cleanNumber($gaji_pokok);
        $tunj_clean = $this->cleanNumber($tunjangan);

        $stmt = $this->conn->prepare("UPDATE positions SET nama_jabatan = ?, gaji_pokok = ?, tunjangan = ? WHERE id = ?");
        $stmt->bind_param("siii", $nama_clean, $gaji_clean, $tunj_clean, $id_clean);
        return $stmt->execute();
    }

    // 4. Hapus Data Jabatan (Secure Prepared Statements)
    public function hapus($id) {
        $id_clean = (int) $id;
        $stmt = $this->conn->prepare("DELETE FROM positions WHERE id = ?");
        $stmt->bind_param("i", $id_clean);
        return $stmt->execute();
    }
}
?>