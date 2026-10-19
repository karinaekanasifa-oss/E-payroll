<?php
/**
 * --------------------------------------------------------------------------
 * HALAMAN HITUNG & PROSES GAJI - OOP VERSION (BURGUNDY THEME + SWEETALERT)
 * --------------------------------------------------------------------------
 */

if (file_exists('database/connection.php')) {
    require_once 'database/connection.php';
} elseif (file_exists('../database/connection.php')) {
    require_once '../database/connection.php';
} elseif (file_exists('../../database/connection.php')) {
    require_once '../../database/connection.php';
}

class CalculatePayrollOOP {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn =$db->conn;
    }

    // 1. Mengambil data pegawai beserta informasi bank & rekening
    public function getEmployeesForPayroll() {
        $query = "SELECT employees.id as emp_id, employees.nip, employees.nama_bank, employees.no_rekening, users.nama, positions.nama_jabatan, positions.gaji_pokok, positions.tunjangan 
                  FROM employees 
                  JOIN users ON employees.user_id = users.id 
                  JOIN positions ON employees.position_id = positions.id 
                  ORDER BY users.nama ASC";
        return $this->conn->query($query);
    }

    // 2. Fungsi Hitung Otomatis Potongan Berdasarkan Absensi (Izin/Sakit = 10rb, Alfa = 20rb)
    public function getPotonganAbsensiKaryawan($employee_id,$bulan_tahun) {
        $stmt =$this->conn->prepare("SELECT status, COUNT(*) as jumlah FROM attedances WHERE employee_id = ? AND DATE_FORMAT(tanggal, '%Y-%m') = ? GROUP BY status");
        $stmt->bind_param("is", $employee_id,$bulan_tahun);
        $stmt->execute();$result = $stmt->get_result();$total_potongan = 0;
        while ($row =$result->fetch_assoc()) {
            $status = strtolower($row['status']);
            $jumlah = intval($row['jumlah']);
            
            if ($status == 'izin' || $status == 'sakit') {$total_potongan += $jumlah * 10000; } elseif ($status == 'alfa') {
                $total_potongan +=$jumlah * 20000; 
            }
        }
        return $total_potongan;
    }

    // 3. Simpan data gaji & sesuaikan notifikasi berdasarkan status 'Dibayar' atau 'Pending'
    public function processSavePayroll($data) {
        $employee_id = intval($data['employee_id']);
        $bulan_tahun =$data['bulan_tahun'];
        $potongan    = floatval($data['potongan']);
        $status_cair =$data['status_cair'];

        // Ambil data gaji pokok & tunjangan
        $stmtPos =$this->conn->prepare("SELECT positions.gaji_pokok, positions.tunjangan 
                                          FROM employees 
                                          JOIN positions ON employees.position_id = positions.id 
                                          WHERE employees.id = ?");
        $stmtPos->bind_param("i", $employee_id);$stmtPos->execute();
        $pos =$stmtPos->get_result()->fetch_assoc();

        $gaji_pokok =$pos['gaji_pokok'] ?? 0;
        $tunjangan  =$pos['tunjangan'] ?? 0;
        $total_gaji = ($gaji_pokok + $tunjangan) -$potongan;

        // Simpan ke tabel payrolls
        $stmt =$this->conn->prepare("INSERT INTO payrolls (employee_id, bulan_tahun, potongan, total_gaji, status_cair) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdds", $employee_id, $bulan_tahun,$potongan, $total_gaji,$status_cair);
        
        if ($stmt->execute()) {
            if (strtolower($status_cair) == 'dibayar') {$judul = "Gaji Telah Diterbitkan & Dikirim";
                $pesan = "Halo, gaji Anda untuk periode {$bulan_tahun} sebesar Rp " . number_format($total_gaji, 0, ',', '.') . " telah berhasil diproses dan dikirim ke rekening Anda.";
                
                $stmtNotif =$this->conn->prepare("INSERT INTO notifications (employee_id, judul, pesan, status_baca, created_at) VALUES (?, ?, ?, 'belum', NOW())");
                $stmtNotif->bind_param("iss", $employee_id,$judul, $pesan);$stmtNotif->execute();

                $alertTitle = "Gaji Sukses Dikirim!";
                $alertText  = "Perhitungan gaji berhasil disimpan dan notifikasi otomatis telah dikirimkan ke akun karyawan.";
                $alertIcon  = "success";
            } else {
                $alertTitle = "Data Gaji Disimpan (Pending)";
                $alertText  = "Perhitungan gaji berhasil disimpan dengan status Pending. Belum ada notifikasi yang dikirim ke karyawan.";
                $alertIcon  = "info";
            }

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: '$alertIcon',
                        title: '$alertTitle',
                        text: '$alertText',
                        confirmButtonColor: '#800020',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location='index.php?page=gaji';
                        }
                    });
                });
            </script>";
            exit;
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan!',
                        text: 'Terjadi kesalahan saat memproses data penggajian.',
                        confirmButtonColor: '#800020'
                    });
                });
            </script>";
        }
    }
}

$calcObj = new CalculatePayrollOOP();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $calcObj->processSavePayroll($_POST);
}

$employees =$calcObj->getEmployeesForPayroll();
?>

<!-- CDN SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- CSS Custom Burgundy -->
<style>
.btn-burgundy-theme {
  background-color: #800020;
  color: #fff;
  border: none;
}
.btn-burgundy-theme:hover {
  background-color: #600018;
  color: #fff;
}
@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.page-header, .card {
  animation: slideUpFade 0.3s ease-in-out;
}
</style>

<!-- Header Banner -->
<div class="page-header mb-4">
  <div>
    <h1 class="page-title fw-bold" style="color: #800020;">Proses Hitung Gaji Baru</h1>
    <p class="page-subtitle text-muted">Hitung komponen gaji pokok, tunjangan, potongan otomatis dari absensi, serta informasi rekening.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php?page=dashboard-finance" class="text-decoration-none" style="color: #800020;">Home</a></li>
      <li class="breadcrumb-item text-muted">Finance</li>
      <li class="breadcrumb-item"><a href="index.php?page=gaji" class="text-decoration-none" style="color: #800020;">Penggajian</a></li>
      <li class="breadcrumb-item active text-dark" aria-current="page">Hitung Gaji</li>
    </ol>
  </nav>
</div>

<!-- Form Card -->
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
      <form action="" method="POST" id="formHitungGaji">
        
        <!-- Dropdown Pilihan Karyawan -->
        <div class="mb-3">
          <label class="form-label fw-bold small text-uppercase text-muted">Pilih Karyawan & Rekening Tujuan</label>
          <select name="employee_id" id="employeeSelect" class="form-select rounded-3 shadow-none border" required onchange="hitungOtomatis()">
            <option value="" disabled selected>-- Pilih Nama Karyawan --</option>
            <?php 
            // Reset pointer result query agar bisa diloop ulang untuk atribut JS
            $employees_data = [];
            while ($emp =$employees->fetch_assoc()) {
                $employees_data[] =$emp;
            }
            foreach ($employees_data as$emp): 
            ?>
              <option value="<?= $emp['emp_id']; ?>" 
                      data-bank="<?= htmlspecialchars($emp['nama_bank'] ?? ''); ?>" 
                      data-rekening="<?= htmlspecialchars($emp['no_rekening'] ?? ''); ?>"
                      data-jabatan="<?= htmlspecialchars($emp['nama_jabatan']); ?>"
                      data-pokok="<?= $emp['gaji_pokok']; ?>"
                      data-tunjangan="<?= $emp['tunjangan']; ?>">
                <?= htmlspecialchars($emp['nama'] . ' (' . $emp['nip'] . ' - ' .$emp['nama_jabatan'] . ')'); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Box Informasi Bank & Rekening Tujuan -->
        <div class="alert alert-light border rounded-3 p-3 mb-3 small text-secondary">
          <i class="bi bi-info-circle-fill me-1 text-danger"></i> Tujuan Transfer Bank: 
          <span id="infoBank" class="fw-bold text-dark">-</span> | No. Rekening: <span id="infoRekening" class="fw-bold text-dark">-</span>
        </div>

        <!-- Periode Gaji -->
        <div class="mb-3">
          <label class="form-label fw-bold small text-uppercase text-muted">Periode Gaji (Bulan-Tahun)</label>
          <input type="month" name="bulan_tahun" id="inputBulan" class="form-control rounded-3 shadow-none border" value="<?= date('Y-m'); ?>" required onchange="hitungOtomatis()">
        </div>

        <!-- Gaji Pokok & Tunjangan -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold small text-uppercase text-muted">Gaji Pokok (Rp)</label>
            <input type="text" id="inputPokok" class="form-control rounded-3 bg-light" readonly value="0">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold small text-uppercase text-muted">Tunjangan (Rp)</label>
            <input type="text" id="inputTunjangan" class="form-control rounded-3 bg-light" readonly value="0">
          </div>
        </div>

        <!-- Potongan -->
        <div class="mb-3">
          <label class="form-label fw-bold small text-uppercase text-muted">Potongan Keterlambatan / Absensi (Rp)</label>
          <input type="number" name="potongan" id="inputPotongan" class="form-control rounded-3 shadow-none border" value="0" min="0" required>
          <div class="form-text small text-muted">*Dihitung otomatis dari data presensi: Izin/Sakit (Rp 10.000), Alfa (Rp 20.000). Bisa disesuaikan manual jika diperlukan.</div>
        </div>

        <!-- Status Pencairan -->
        <div class="mb-4">
          <label class="form-label fw-bold small text-uppercase text-muted">Status Pencairan</label>
          <select name="status_cair" class="form-select rounded-3 shadow-none border" required>
            <option value="pending">Pending (Simpan Saja, Tanpa Notifikasi)</option>
            <option value="dibayar">Dibayar (Kirim Notifikasi Masuk)</option>
          </select>
          <div class="form-text small text-muted">Jika dipilih "Dibayar", sistem akan otomatis mencatatkan notifikasi masuk ke akun karyawan.</div>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex justify-content-end gap-2">
          <a href="index.php?page=gaji" class="btn btn-light border px-4 py-2 rounded-3">Batal</a>
          <button type="submit" class="btn btn-burgundy-theme px-4 py-2 rounded-3 shadow-sm">
            <i class="bi bi-send-fill me-1"></i> Simpan & Terbitkan Gaji
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- JavaScript Fetch Potongan Absensi via AJAX / Fetch API -->
<script>
function hitungOtomatis() {
    let select = document.getElementById('employeeSelect');
    let selectedOption = select.options[select.selectedIndex];
    
    if (!selectedOption.value) return;

    let bank = selectedOption.getAttribute('data-bank');
    let rekening = selectedOption.getAttribute('data-rekening');
    let pokok = selectedOption.getAttribute('data-pokok');
    let tunjangan = selectedOption.getAttribute('data-tunjangan');
    let employeeId = selectedOption.value;
    let bulanTahun = document.getElementById('inputBulan').value;

    // Update info bank
    document.getElementById('infoBank').innerText = (bank && bank.trim() !== '') ? bank : 'Belum diatur';
    document.getElementById('infoRekening').innerText = (rekening && rekening.trim() !== '') ? rekening : 'Belum diatur';
    
    // Update nominal pokok & tunjangan
    document.getElementById('inputPokok').value = new Intl.NumberFormat('id-ID').format(pokok);
    document.getElementById('inputTunjangan').value = new Intl.NumberFormat('id-ID').format(tunjangan);

    // Ambil data potongan absensi via Fetch API (AJAX sederhana)
    fetch(`get_potongan_ajax.php?employee_id=${employeeId}&bulan=${bulanTahun}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('inputPotongan').value = data.total_potongan;
        })
        .catch(error => {
            console.error('Gagal mengambil data potongan:', error);
        });
}
</script>