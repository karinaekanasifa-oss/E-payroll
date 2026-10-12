<?php
/**
 * --------------------------------------------------------------------------
 * CLASS ATTENDANCE / ABSENSI - OOP VERSION (DENGAN INTEGRASI SHIFT)
 * --------------------------------------------------------------------------
 */

// Memasukkan file koneksi database
require_once 'database/connection.php';

class Attendance
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conn;
    }

    // Method untuk menampilkan semua data kehadiran (JOIN dengan employees, users, & shifts)
    public function tampilData()
    {
        $query = "SELECT attendances.id as att_id, attendances.tanggal, attendances.jam_masuk, 
                         attendances.jam_keluar, attendances.status,
                         employees.nip, users.nama, shifts.nama_shift 
                  FROM attendances 
                  JOIN employees ON attendances.employee_id = employees.id 
                  JOIN users ON employees.user_id = users.id 
                  LEFT JOIN shifts ON attendances.shift_id = shifts.id 
                  ORDER BY attendances.tanggal DESC, attendances.id DESC";
        
        return $this->conn->query($query);
    }

    // Method untuk menambah data presensi / absensi (menyertakan shift_id)
    public function tambah($employee_id, $shift_id, $tanggal, $jam_masuk, $jam_keluar, $status)
    {
        $var_employee_id = mysqli_real_escape_string($this->conn, $employee_id);
        $var_shift_id    = mysqli_real_escape_string($this->conn, $shift_id);
        $var_tanggal     = mysqli_real_escape_string($this->conn, $tanggal);
        $var_jam_masuk   = mysqli_real_escape_string($this->conn, $jam_masuk);
        $var_jam_keluar  = mysqli_real_escape_string($this->conn, $jam_keluar);
        $var_status      = mysqli_real_escape_string($this->conn, $status);

        $query = "INSERT INTO attendances (employee_id, shift_id, tanggal, jam_masuk, jam_keluar, status) 
                  VALUES ('$var_employee_id', '$var_shift_id', '$var_tanggal', '$var_jam_masuk', '$var_jam_keluar', '$var_status')";

        return mysqli_query($this->conn, $query);
    }

    // Method untuk mengambil data berdasarkan ID untuk keperluan edit
    public function getById($id)
    {
        $id = intval($id);
        $result = $this->conn->query("SELECT * FROM attendances WHERE id = $id");
        return $result->fetch_assoc();
    }

    // Method untuk memperbarui data absensi (menyertakan shift_id)
    public function update($id, $employee_id, $shift_id, $tanggal, $jam_masuk, $jam_keluar, $status)
    {
        $var_id          = intval($id);
        $var_employee_id = mysqli_real_escape_string($this->conn, $employee_id);
        $var_shift_id    = mysqli_real_escape_string($this->conn, $shift_id);
        $var_tanggal     = mysqli_real_escape_string($this->conn, $tanggal);
        $var_jam_masuk   = mysqli_real_escape_string($this->conn, $jam_masuk);
        $var_jam_keluar  = mysqli_real_escape_string($this->conn, $jam_keluar);
        $var_status      = mysqli_real_escape_string($this->conn, $status);

        $query = "UPDATE attendances SET 
                  employee_id = '$var_employee_id', 
                  shift_id = '$var_shift_id', 
                  tanggal = '$var_tanggal', 
                  jam_masuk = '$var_jam_masuk', 
                  jam_keluar = '$var_jam_keluar', 
                  status = '$var_status' 
                  WHERE id = $var_id";

        return mysqli_query($this->conn, $query);
    }

    // Method untuk menghapus data absensi
    public function hapus($id)
    {
        $var_id = intval($id);
        $query = "DELETE FROM attendances WHERE id = $var_id";
        return mysqli_query($this->conn, $query);
    }
}