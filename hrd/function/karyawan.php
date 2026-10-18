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
        $tgl_masuk,
        $nama_bank,
        $no_rekening
    ) {

        // Pembersihan XSS & Sanitasi Data
        $var_nama = htmlspecialchars(trim($nama), ENT_QUOTES, 'UTF-8');
        $var_email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        
        // Melakukan enkripsi password (hashing) sebelum disimpan ke database agar aman
        $var_password = password_hash(
            $password,
            PASSWORD_DEFAULT
        );
        
        // Validasi pembatasan role yang diperbolehkan
        $role_input = strtolower(trim($role));
        $allowed_roles = ['karyawan', 'hrd', 'finance'];
        $var_role = in_array($role_input, $allowed_roles) ? $role_input : 'karyawan';

        $var_nip = htmlspecialchars(trim($nip), ENT_QUOTES, 'UTF-8');
        $var_position_id = (int) $position_id; // Type-casting integer untuk keamanan ID
        $var_tgl_masuk = trim($tgl_masuk);
        $var_nama_bank = htmlspecialchars(trim($nama_bank), ENT_QUOTES, 'UTF-8');
        $var_no_rekening = htmlspecialchars(trim($no_rekening), ENT_QUOTES, 'UTF-8');

        // Memulai transaksi database karena data masuk ke dua tabel berbeda secara berelasi (users & employees)
        mysqli_begin_transaction($this->conn);

        try {
            // 1. Menggunakan Prepared Statement untuk memasukkan data akun ke tabel 'users' (Mencegah SQL Injection)
            $stmtUser = $this->conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
            $stmtUser->bind_param("ssss", $var_nama, $var_email, $var_password, $var_role);
            $stmtUser->execute();
            
            // Mengambil ID terakhir dari tabel users yang baru saja di-generate secara otomatis (Auto Increment)
            $user_id = mysqli_insert_id($this->conn);

            // 2. Menggunakan Prepared Statement untuk memasukkan data detail ke tabel 'employees' beserta rekening bank
            $stmtEmp = $this->conn->prepare("INSERT INTO employees (user_id, position_id, nip, tgl_masuk, nama_bank, no_rekening) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtEmp->bind_param("iissss", $user_id, $var_position_id, $var_nip, $var_tgl_masuk, $var_nama_bank, $var_no_rekening);
            $stmtEmp->execute();

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
?>