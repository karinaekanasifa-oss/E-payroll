<?php
/**
 * --------------------------------------------------------------------------
 * PROSES HAPUS SHIFT - OOP VERSION
 * --------------------------------------------------------------------------
 */

// Memanggil file function class shift
require_once 'function/shift.php';

// Pastikan parameter ID tersedia di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Instansiasi objek dari Class Shift
    $shiftObj = new Shift();

    // Menjalankan method hapus berdasarkan ID
    $result = $shiftObj->hapus($id);

    if ($result) {
        echo "<script>window.location.href='index.php?page=shift';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger m-4'>Gagal menghapus data shift. <a href='index.php?page=shift'>Kembali</a></div>";
    }
} else {
    echo "<script>window.location.href='index.php?page=shift';</script>";
    exit;
}
?>