<?php
/**
 * --------------------------------------------------------------------------
 * CLASS SHIFT - OOP VERSION (DIJAMIN AMAN PATH KONEKSINYA)
 * --------------------------------------------------------------------------
 */

// Menangani jalur koneksi agar terbaca dengan benar dari berbagai posisi folder
if (file_exists('database/connection.php')) {
    require_once 'database/connection.php';
} elseif (file_exists('../database/connection.php')) {
    require_once '../database/connection.php';
} elseif (file_exists('../../database/connection.php')) {
    require_once '../../database/connection.php';
}

class Shift
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conn;
    }

    // Method untuk menambah data shift baru
    public function tambah($nama_shift, $jam_masuk, $jam_keluar)
    {
        $var_nama_shift = mysqli_real_escape_string($this->conn, $nama_shift);
        $var_jam_masuk  = mysqli_real_escape_string($this->conn, $jam_masuk);
        $var_jam_keluar = mysqli_real_escape_string($this->conn, $jam_keluar);

        $query = "INSERT INTO shifts (nama_shift, jam_masuk, jam_keluar)
                  VALUES ('$var_nama_shift', '$var_jam_masuk', '$var_jam_keluar')";

        return mysqli_query($this->conn, $query);
    }

    // Method untuk mengambil data shift berdasarkan ID (untuk form edit)
    public function ambilDataById($id)
    {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "SELECT * FROM shifts WHERE id = '$id'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    // Method untuk memperbarui (update) data shift
    public function update($id, $nama_shift, $jam_masuk, $jam_keluar)
    {
        $var_id         = mysqli_real_escape_string($this->conn, $id);
        $var_nama_shift = mysqli_real_escape_string($this->conn, $nama_shift);
        $var_jam_masuk  = mysqli_real_escape_string($this->conn, $jam_masuk);
        $var_jam_keluar = mysqli_real_escape_string($this->conn, $jam_keluar);

        $query = "UPDATE shifts SET 
                    nama_shift = '$var_nama_shift', 
                    jam_masuk = '$var_jam_masuk', 
                    jam_keluar = '$var_jam_keluar' 
                  WHERE id = '$var_id'";

        return mysqli_query($this->conn, $query);
    }

    // Method untuk menghapus data shift berdasarkan ID
    public function hapus($id)
    {
        $var_id = mysqli_real_escape_string($this->conn, $id);
        $query = "DELETE FROM shifts WHERE id = '$var_id'";
        return mysqli_query($this->conn, $query);
    }

    // Method untuk menampilkan seluruh data shift
    public function tampilSemua()
    {
        $query = "SELECT * FROM shifts ORDER BY id DESC";
        return mysqli_query($this->conn, $query);
    }
}
?>