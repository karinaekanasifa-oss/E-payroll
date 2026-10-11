<main id="content" class="content py-10">
    <div class="container-fluid">
        
        <!-- Header Banner -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div>
                        <h1 class="fs-3 mb-1">Edit Jabatan</h1>
                        <p class="mb-0 text-muted">Perbarui data posisi jabatan dan rincian gaji pokok karyawan.</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <a href="index.php?page=jabatan" class="btn btn-secondary">Kembali ke Jabatan</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card Layout -->
        <div class="row">
            <div class="col-12">
                <div class="card border-light shadow-sm">
                    <div class="card-body p-4">
                        
                        <?php
                        // Memanggil file function class jabatan
                        require_once 'function/jabatan.php';

                        // Instansiasi objek dari Class Jabatan
                        $jabatanObj = new Jabatan();

                        // Mengambil parameter ID dari URL (contoh: index.php?page=edit-jabatan&id=1)
                        $id = isset($_GET['id']) ? $_GET['id'] : '';
                        
                        // Mengambil data lama berdasarkan ID untuk ditampilkan ke form
                        $data = $jabatanObj->ambilDataById($id);

                        // Jika data tidak ditemukan di database, kembalikan ke halaman utama jabatan
                        if (!$data) {
                            echo "<script>window.location.href='index.php?page=jabatan';</script>";
                            exit;
                        }

                        // Proses saat tombol update ditekan
                        if (isset($_POST['update_jabatan'])) {
                            $nama_jabatan = $_POST['nama_jabatan'];
                            $gaji_pokok   = $_POST['gaji_pokok'];
                            $tunjangan    = $_POST['tunjangan'];

                            // Memanggil method update dari class Jabatan
                            $result = $jabatanObj->update($id, $nama_jabatan, $gaji_pokok, $tunjangan);

                            if ($result) {
                                echo "<script>window.location.href='index.php?page=jabatan';</script>";
                            } else {
                                echo "<div class='alert alert-danger'>Data jabatan gagal diperbarui.</div>";
                            }
                        }
                        ?>

                        <!-- Form Edit Input -->
                        <form method="POST" action="">
                            
                            <div class="mb-3">
                                <label for="nama_jabatan" class="form-label fw-bold">Nama Jabatan</label>
                                <!-- Nilai lama otomatis ditampilkan di dalam value menggunakan htmlspecialchars -->
                                <input type="text" class="form-control" name="nama_jabatan" id="nama_jabatan" value="<?php echo htmlspecialchars($data['nama_jabatan']); ?>" placeholder="Masukkan nama jabatan..." required>
                            </div>

                            <!-- Input Gaji Pokok (Dilengkapi number_format agar titik ribuan muncul otomatis sesuai data lama) -->
                            <div class="mb-3">
                                <label for="gaji_pokok" class="form-label fw-bold">Gaji Pokok</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" name="gaji_pokok" id="gaji_pokok" value="<?php echo number_format($data['gaji_pokok'], 0, ',', '.'); ?>" placeholder="Contoh: 4.000.000" required>
                                </div>
                            </div>

                            <!-- Input Tunjangan (Dilengkapi number_format agar titik ribuan muncul otomatis sesuai data lama) -->
                            <div class="mb-4">
                                <label for="tunjangan" class="form-label fw-bold">Tunjangan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" name="tunjangan" id="tunjangan" value="<?php echo number_format($data['tunjangan'], 0, ',', '.'); ?>" placeholder="Contoh: 500.000" required>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" name="update_jabatan" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="index.php?page=jabatan" class="btn btn-outline-secondary">Batal</a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- SCRIPT JAVASCRIPT: OTOMATIS MEMBUAT TITIK RIBUAN SAAT DIKETIK -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const inputGaji = document.getElementById('gaji_pokok');
    const inputTunjangan = document.getElementById('tunjangan');

    function formatRupiah(angka) {
        let numberString = angka.replace(/[^,\d]/g, "").toString();
        let split = numberString.split(",");
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        return rupiah;
    }

    if (inputGaji) {
        inputGaji.addEventListener("input", function (e) {
            this.value = formatRupiah(this.value);
        });
    }

    if (inputTunjangan) {
        inputTunjangan.addEventListener("input", function (e) {
            this.value = formatRupiah(this.value);
        });
    }
});
</script>