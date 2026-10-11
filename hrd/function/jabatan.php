<?php
/**
 * --------------------------------------------------------------------------
 * CLASS JABATAN - OOP VERSION
 * --------------------------------------------------------------------------
 */

// Memasukkan file koneksi database agar class ini bisa terhubung ke MySQL
require_once 'database/connection.php';

class Jabatan
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conn;
    }

    // Method untuk menambah data jabatan baru
    public function tambah($nama_jabatan, $gaji_pokok, $tunjangan)
    {
        $var_nama_jabatan = mysqli_real_escape_string($this->conn, $nama_jabatan);
        
        $clean_gaji = str_replace('.', '', $gaji_pokok);
        $clean_tunjangan = str_replace('.', '', $tunjangan);

        $var_gaji_pokok = mysqli_real_escape_string($this->conn, $clean_gaji);
        $var_tunjangan = mysqli_real_escape_string($this->conn, $clean_tunjangan);

        $query = "INSERT INTO positions (nama_jabatan, gaji_pokok, tunjangan)
                  VALUES ('$var_nama_jabatan', '$var_gaji_pokok', '$var_tunjangan')";

        return mysqli_query($this->conn, $query);
    }

    // Method untuk mengambil data jabatan berdasarkan ID (untuk form edit)
    public function ambilDataById($id)
    {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "SELECT * FROM positions WHERE id = '$id'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    // Method untuk memperbarui (update) data jabatan
    public function update($id, $nama_jabatan, $gaji_pokok, $tunjangan)
    {
        $var_id = mysqli_real_escape_string($this->conn, $id);
        $var_nama_jabatan = mysqli_real_escape_string($this->conn, $nama_jabatan);
        
        $clean_gaji = str_replace('.', '', $gaji_pokok);
        $clean_tunjangan = str_replace('.', '', $tunjangan);

        $var_gaji_pokok = mysqli_real_escape_string($this->conn, $clean_gaji);
        $var_tunjangan = mysqli_real_escape_string($this->conn, $clean_tunjangan);

        $query = "UPDATE positions SET 
                    nama_jabatan = '$var_nama_jabatan', 
                    gaji_pokok = '$var_gaji_pokok', 
                    tunjangan = '$var_tunjangan' 
                  WHERE id = '$var_id'";

        return mysqli_query($this->conn, $query);
    }

    // Method untuk menghapus data jabatan berdasarkan ID
    public function hapus($id)
    {
        $var_id = mysqli_real_escape_string($this->conn, $id);
        $query = "DELETE FROM positions WHERE id = '$var_id'";
        return mysqli_query($this->conn, $query);
    }

    // Method untuk menampilkan seluruh data jabatan
    public function tampilSemua()
    {
        $query = "SELECT * FROM positions ORDER BY id DESC";
        return mysqli_query($this->conn, $query);
    }
}
?>