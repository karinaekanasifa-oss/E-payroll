<?php
/**
 * --------------------------------------------------------------------------
 * PROSES HAPUS KARYAWAN - SECURE OOP VERSION
 * --------------------------------------------------------------------------
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'database/connection.php';

$db = new Database();
$conn = $db->conn;

// Type casting parameter ID untuk mencegah SQL Injection[cite: 12]
$emp_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$csrf_token = $_GET['csrf_token'] ?? '';

// Validasi Token CSRF yang lebih fleksibel
if ($emp_id > 0) {
    if (!empty($csrf_token) && isset($_SESSION['csrf_token']) && !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        echo "<script>alert('Peringatan Keamanan: Validasi CSRF Token Gagal!'); window.location='index.php?page=karyawan';</script>";
        exit;
    }

    // Ambil user_id dengan Prepared Statement[cite: 12]
    $stmt_get = $conn->prepare("SELECT user_id FROM employees WHERE id = ? LIMIT 1");
    $stmt_get->bind_param("i", $emp_id);
    $stmt_get->execute();
    $result_get = $stmt_get->get_result();
    $row = $result_get->fetch_assoc();

    if ($row) {
        $user_id = (int)$row['user_id'];

        // Mulai transaksi database secara aman[cite: 12]
        $conn->begin_transaction();

        try {
            // 1. Hapus data di tabel employees[cite: 12]
            $stmt_del_emp = $conn->prepare("DELETE FROM employees WHERE id = ?");
            $stmt_del_emp->bind_param("i", $emp_id);
            $stmt_del_emp->execute();

            // 2. Hapus data akun login di tabel users[cite: 12]
            $stmt_del_usr = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt_del_usr->bind_param("i", $user_id);
            $stmt_del_usr->execute();

            $conn->commit();
            echo "<script>alert('Data karyawan dan akun login berhasil dihapus!'); window.location='index.php?page=karyawan';</script>";
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Gagal menghapus data karyawan.'); window.location='index.php?page=karyawan';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Data karyawan tidak ditemukan!'); window.location='index.php?page=karyawan';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location='index.php?page=karyawan';</script>";
    exit;
}
?>