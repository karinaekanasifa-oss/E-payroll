<?php

// Memasukkan file koneksi database agar class ini bisa terhubung ke MySQL
require_once '../../config/database.php'; // Sesuaikan path file koneksi database kamu

// Membuat class bernama 'Karyawan' untuk mengelola data karyawan (OOP / Object Oriented Programming)
class Karyawan
{
    // Properti privat untuk menyimpan objek koneksi database
    private $conn;

    // Method constructor yang otomatis dijalankan saat class ini dipanggil/diinisialisasi
    public function __construct()
    {
        // Membuat objek baru dari class Database (yang ada di file connection.php)
        $database = new Database();
        
        // Mengambil koneksi database dan menyimpannya ke variabel $conn milik class ini
        $this->conn = $database->conn;
    }

    // Method (fungsi) untuk menambah data karyawan baru ke database dengan beberapa parameter inputan
    public function tambah(
        $nama,
        $email,
        $password,
        $role,
        $nip,
        $position_id,
        $tgl_masuk
    ) {

        // Mencegah celah keamanan SQL Injection dengan membersihkan karakter khusus pada input nama
        $var_nama = mysqli_real_escape_string(
            $this->conn,
            $nama
        );
        
        // Mencegah celah keamanan SQL Injection pada input email
        $var_email = mysqli_real_escape_string(
            $this->conn,
            $email
        );
        
        // Melakukan enkripsi password (hashing) sebelum disimpan ke database agar aman
        $var_password = password_hash(
            $password,
            PASSWORD_DEFAULT
        );
        
        // Mencegah celah keamanan SQL Injection pada input role
        $var_role = mysqli_real_escape_string(
            $this->conn,
            $role
        );

        // Mencegah celah keamanan SQL Injection pada input nip
        $var_nip = mysqli_real_escape_string(
            $this->conn,
            $nip
        );

        // Mencegah celah keamanan SQL Injection pada input position_id
        $var_position_id = mysqli_real_escape_string(
            $this->conn,
            $position_id
        );

        // Mencegah celah keamanan SQL Injection pada input tgl_masuk
        $var_tgl_masuk = mysqli_real_escape_string(
            $this->conn,
            $tgl_masuk
        );

        // Memulai transaksi database karena data masuk ke dua tabel berbeda secara berelasi (users & employees)
        mysqli_begin_transaction($this->conn);

        try {
            // 1. Menulis perintah SQL (Query) untuk memasukkan data akun ke tabel 'users'
            $queryUser = "INSERT INTO users 
                          (nama, email, password, role)
                          VALUES (
                          '$var_nama',
                          '$var_email',
                          '$var_password',
                          '$var_role'
                          )";
            
            // Menjalankan query ke tabel users
            mysqli_query($this->conn, $queryUser);
            
            // Mengambil ID terakhir dari tabel users yang baru saja di-generate secara otomatis (Auto Increment)
            $user_id = mysqli_insert_id($this->conn);

            // 2. Menulis perintah SQL (Query) untuk memasukkan data detail ke tabel 'employees'
            $queryEmp = "INSERT INTO employees 
                          (user_id, position_id, nip, tgl_masuk)
                          VALUES (
                          '$user_id',
                          '$var_position_id',
                          '$var_nip',
                          '$var_tgl_masuk'
                          )";

            // Menjalankan query ke tabel employees
            mysqli_query($this->conn, $queryEmp);

            // Jika semua query berhasil tanpa error, simpan permanen (commit) ke database
            mysqli_commit($this->conn);
            return true;

        } catch (Exception $e) {
            // Jika terjadi kesalahan pada salah satu query, batalkan semua perubahan (rollback)
            mysqli_rollback($this->conn);
            return false;
        }
    }
}