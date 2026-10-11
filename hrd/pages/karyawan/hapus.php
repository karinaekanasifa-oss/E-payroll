<?php
/**
 * --------------------------------------------------------------------------
 * PROSES HAPUS KARYAWAN - OOP VERSION
 * --------------------------------------------------------------------------
 */

require_once 'database/connection.php';

$db = new Database();
$conn = $db->conn;

// Ambil ID karyawan (employees.id) dari parameter URL
$emp_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($emp_id > 0) {
    // Ambil user_id terlebih dahulu sebelum data employees dihapus
    $query_get = $conn->query("SELECT user_id FROM employees WHERE id = $emp_id");
    $row = $query_get->fetch_assoc();

    if ($row) {
        $user_id = $row['user_id'];

        // Mulai transaksi database
        $conn->begin_transaction();

        try {
            // 1. Hapus data di tabel employees terlebih dahulu (karena ada Foreign Key ke users)
            $conn->query("DELETE FROM employees WHERE id = $emp_id");

            // 2. Hapus data akun login di tabel users
            $conn->query("DELETE FROM users WHERE id = $user_id");

            // Commit transaksi
            $conn->commit();
            echo "<script>alert('Data karyawan dan akun login berhasil dihapus!'); window.location='index.php?page=karyawan';</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Gagal menghapus data: " . $e->getMessage() . "'); window.location='index.php?page=karyawan';</script>";
        }
    } else {
        echo "<script>alert('Data karyawan tidak ditemukan!'); window.location='index.php?page=karyawan';</script>";
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location='index.php?page=karyawan';</script>";
}
?>