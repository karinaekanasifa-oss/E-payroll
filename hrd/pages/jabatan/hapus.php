<?php
/**
 * --------------------------------------------------------------------------
 * PROSES HAPUS JABATAN - OOP VERSION
 * --------------------------------------------------------------------------
 * File ini menangkap ID dari URL, memanggil method hapus, lalu mengalihkan
 * kembali ke halaman utama data jabatan.
 */

// Memanggil file function class jabatan
require_once 'function/jabatan.php';

// Pastikan parameter ID tersedia di URL (contoh: index.php?page=hapus-jabatan&id=1)
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Instansiasi objek dari Class Jabatan
    $jabatanObj = new Jabatan();

    // Menjalankan method hapus berdasarkan ID
    $result = $jabatanObj->hapus($id);

    // Validasi hasil penghapusan data
    if ($result) {
        // Jika berhasil terhapus, redirect kembali ke halaman utama jabatan
        echo "<script>window.location.href='index.php?page=jabatan';</script>";
        exit;
    } else {
        // Jika gagal, tampilkan pesan error
        echo "<div class='alert alert-danger m-4'>Gagal menghapus data jabatan. <a href='index.php?page=jabatan'>Kembali</a></div>";
    }
} else {
    // Jika tidak ada ID di URL, kembalikan langsung ke halaman jabatan
    echo "<script>window.location.href='index.php?page=jabatan';</script>";
    exit;
}
?>