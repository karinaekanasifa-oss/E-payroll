<main id="content" class="content py-10">
    <div class="container-fluid">
        
        <!-- Header Banner -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div>
                        <h1 class="fs-3 mb-1">Tambah Shift Kerja</h1>
                        <p class="mb-0 text-muted">Tambahkan data kategori shift beserta jam masuk dan jam keluar baru.</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <a href="index.php?page=shift" class="btn btn-secondary">Kembali ke Shift</a>
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
                        // Memanggil file function class shift
                        require_once 'function/shift.php';

                        // Proses saat tombol submit ditekan
                        if (isset($_POST['submit_shift'])) {
                            // Instansiasi objek dari Class Shift
                            $shiftObj = new Shift();

                            $nama_shift = $_POST['nama_shift'];
                            $jam_masuk  = $_POST['jam_masuk'];
                            $jam_keluar = $_POST['jam_keluar'];

                            // Memanggil method tambah dari class Shift
                            $result = $shiftObj->tambah($nama_shift, $jam_masuk, $jam_keluar);

                            if ($result) {
                                echo "<script>window.location.href='index.php?page=shift';</script>";
                            } else {
                                echo "<div class='alert alert-danger'>Data shift gagal ditambahkan.</div>";
                            }
                        }
                        ?>

                        <!-- Form Input -->
                        <form method="POST" action="">
                            
                            <!-- Pilihan Nama Shift (Menyesuaikan tipe ENUM di database: pagi, siang, sore, malam) -->
                            <div class="mb-3">
                                <label for="nama_shift" class="form-label fw-bold">Nama Shift</label>
                                <select class="form-select" name="nama_shift" id="nama_shift" required>
                                    <option value="" disabled selected>-- Pilih Shift --</option>
                                    <option value="pagi">Pagi</option>
                                    <option value="siang">Siang</option>
                                    <option value="sore">Sore</option>
                                    <option value="malam">Malam</option>
                                </select>
                            </div>

                            <!-- Input Jam Masuk -->
                            <div class="mb-3">
                                <label for="jam_masuk" class="form-label fw-bold">Jam Masuk</label>
                                <input type="time" class="form-control" name="jam_masuk" id="jam_masuk" required>
                            </div>

                            <!-- Input Jam Keluar -->
                            <div class="mb-4">
                                <label for="jam_keluar" class="form-label fw-bold">Jam Keluar</label>
                                <input type="time" class="form-control" name="jam_keluar" id="jam_keluar" required>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" name="submit_shift" class="btn btn-primary">Simpan Shift</button>
                                <a href="index.php?page=shift" class="btn btn-outline-secondary">Batal</a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</main>