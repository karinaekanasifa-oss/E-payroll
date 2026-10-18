<main id="content" class="content py-10">
    <div class="container-fluid">
        
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div>
                        <h1 class="fs-3 mb-1">Tambah Jabatan</h1>
                        <p class="mb-0 text-muted">Tambahkan data posisi jabatan dan rincian gaji pokok baru.</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <a href="index.php?page=jabatan" class="btn btn-secondary">Kembali ke Jabatan</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-light shadow-sm">
                    <div class="card-body p-4">
                        
                        <?php
                        require_once 'function/jabatan.php';

                        if (empty($_SESSION['csrf_token'])) {
                            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        }

                        if (isset($_POST['submit_jabatan'])) {
                            // Validasi CSRF Token
                            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                                echo "<div class='alert alert-danger'>Validasi Keamanan Gagal (CSRF Token Invalid).</div>";
                            } else {
                                $jabatanObj = new Jabatan();

                                $nama_jabatan = $_POST['nama_jabatan'];
                                $gaji_pokok   = $_POST['gaji_pokok'];
                                $tunjangan    = $_POST['tunjangan'];

                                $result = $jabatanObj->tambah($nama_jabatan, $gaji_pokok, $tunjangan);

                                if ($result) {
                                    echo "<script>window.location.href='index.php?page=jabatan';</script>";
                                } else {
                                    echo "<div class='alert alert-danger'>Data jabatan gagal ditambahkan.</div>";
                                }
                            }
                        }
                        ?>

                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div class="mb-3">
                                <label for="nama_jabatan" class="form-label fw-bold">Nama Jabatan</label>
                                <input type="text" class="form-control" name="nama_jabatan" id="nama_jabatan" placeholder="Masukkan nama jabatan..." required>
                            </div>

                            <div class="mb-3">
                                <label for="gaji_pokok" class="form-label fw-bold">Gaji Pokok</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" name="gaji_pokok" id="gaji_pokok" placeholder="Contoh: 4.000.000" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="tunjangan" class="form-label fw-bold">Tunjangan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" name="tunjangan" id="tunjangan" placeholder="Contoh: 500.000" required>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" name="submit_jabatan" class="btn btn-primary">Simpan Jabatan</button>
                                <a href="index.php?page=jabatan" class="btn btn-outline-secondary">Batal</a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

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