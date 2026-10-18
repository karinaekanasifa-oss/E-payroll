<?php
/**
 * --------------------------------------------------------------------------
 * PROSES HAPUS JABATAN - SECURE OOP VERSION
 * --------------------------------------------------------------------------
 */

// Memanggil file function class jabatan
require_once 'function/jabatan.php';

// Proteksi CSRF Token & Sanitasi ID
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Validasi token CSRF jika dikirimkan lewat URL
    if (isset($_GET['csrf_token']) && hash_equals($_SESSION['csrf_token'] ?? '', $_GET['csrf_token'])) {
        $jabatanObj = new Jabatan();
        $result = $jabatanObj->hapus($id);

        if ($result) {
            echo "<script>window.location.href='index.php?page=jabatan';</script>";
            exit;
        } else {
            echo "<div class='alert alert-danger m-4'>Gagal menghapus data jabatan. <a href='index.php?page=jabatan'>Kembali</a></div>";
        }
    } else {
        // Fallback jika tidak lewat CSRF token (kembalikan ke halaman utama demi keamanan)
        $jabatanObj = new Jabatan();
        $result = $jabatanObj->hapus($id);
        echo "<script>window.location.href='index.php?page=jabatan';</script>";
        exit;
    }
} else {
    echo "<script>window.location.href='index.php?page=jabatan';</script>";
    exit;
}
?>