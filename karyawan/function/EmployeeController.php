<?php
class EmployeeController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Input Presensi Masuk / Keluar
    public function inputPresensi($employee_id, $shift_id, $tanggal, $jam_masuk, $jam_keluar, $status) {
        $query = "INSERT INTO attendances (employee_id, shift_id, tanggal, jam_masuk, jam_keluar, status) 
                  VALUES (:employee_id, :shift_id, :tanggal, :jam_masuk, :jam_keluar, :status)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":employee_id", $employee_id);
        $stmt->bindParam(":shift_id", $shift_id);
        $stmt->bindParam(":tanggal", $tanggal);
        $stmt->bindParam(":jam_masuk", $jam_masuk);
        $stmt->bindParam(":jam_keluar", $jam_keluar);
        $stmt->bindParam(":status", $status);

        return $stmt->execute();
    }

    // Mengajukan Izin / Cuti
    public function ajukanIzin($employee_id, $judul, $pesan) {
        $query = "INSERT INTO notifications (employee_id, judul, pesan, status_baca) 
                  VALUES (:employee_id, :judul, :pesan, 'belum')";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":employee_id", $employee_id);
        $stmt->bindParam(":judul", $judul);
        $stmt->bindParam(":pesan", $pesan);

        return $stmt->execute();
    }

    // Mengambil Slip Gaji Karyawan
    public function getSlipGaji($employee_id) {
        $query = "SELECT p.*, e.nip, u.nama FROM payrolls p 
                  JOIN employees e ON p.employee_id = e.id 
                  JOIN users u ON e.user_id = u.id 
                  WHERE p.employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":employee_id", $employee_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>