<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN TERBIT & CETAK SLIP GAJI - OOP VERSION (BURGUNDY THEME)
 * --------------------------------------------------------------------------
 */

// Memanggil file koneksi database terlebih dahulu
require_once 'database/connection.php';

/**
 * Class PrintSlipOOP
 * Membungkus logika pengambilan detail data penggajian untuk dicetak sebagai slip gaji.
 */
class PrintSlipOOP {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Mengambil detail data payroll berdasarkan ID, atau mengambil data terakhir jika ID tidak ada
    public function getPayrollDetail($id = 0) {
        if ($id > 0) {
            $stmt = $this->conn->prepare("SELECT payrolls.*, employees.nip, users.nama, positions.nama_jabatan, positions.gaji_pokok, positions.tunjangan 
                                          FROM payrolls 
                                          JOIN employees ON payrolls.employee_id = employees.id 
                                          JOIN users ON employees.user_id = users.id 
                                          JOIN positions ON employees.position_id = positions.id 
                                          WHERE payrolls.id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } else {
            // Jika diakses tanpa ID (dari sidebar), ambil data gaji terbaru
            $query = "SELECT payrolls.*, employees.nip, users.nama, positions.nama_jabatan, positions.gaji_pokok, positions.tunjangan 
                      FROM payrolls 
                      JOIN employees ON payrolls.employee_id = employees.id 
                      JOIN users ON employees.user_id = users.id 
                      JOIN positions ON employees.position_id = positions.id 
                      ORDER BY payrolls.id DESC LIMIT 1";
            return $this->conn->query($query)->fetch_assoc();
        }
    }
}

$printObj = new PrintSlipOOP();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$slip = $printObj->getPayrollDetail($id);

if (!$slip) {
    echo "<div class='card border-0 shadow-sm rounded-4 p-5 bg-white text-center' style='max-width: 600px; margin: 40px auto;'>
            <h4 class='fw-bold text-danger mb-2'>Belum Ada Data Slip Gaji</h4>
            <p class='text-muted mb-4'>Silakan lakukan proses perhitungan gaji terlebih dahulu pada menu Hitung Gaji.</p>
            <a href='index.php?page=hitung' class='btn btn-dark px-4 py-2 rounded-3'>Proses Hitung Gaji</a>
          </div>";
    return;
}
?>