<?php
session_start();
require_once '../../database/connection.php';

$database = new Database();
$db = $database->getConnection();

$payroll_id = $_GET['id'] ?? null;
$employee_id = $_SESSION['employee_id'] ?? 1;

if (!$payroll_id) {
    die("ID Slip Gaji tidak ditemukan.");
}

// Query mengambil rincian slip gaji lengkap dengan data user dan jabatan
$query = "SELECT p.*, e.nip, u.nama, pos.nama_jabatan, pos.gaji_pokok, pos.tunjangan 
          FROM payrolls p 
          JOIN employees e ON p.employee_id = e.id 
          JOIN users u ON e.user_id = u.id 
          JOIN positions pos ON e.position_id = pos.id 
          WHERE p.id = :payroll_id AND p.employee_id = :employee_id";

$stmt = $db->prepare($query);
$stmt->bindParam(":payroll_id", $payroll_id);
$stmt->bindParam(":employee_id", $employee_id);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data slip gaji tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - <?= htmlspecialchars($data['nama']); ?> (<?= htmlspecialchars($data['bulan_tahun']); ?>)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()" class="bg-white p-4">

    <div class="container border p-4 rounded shadow-sm" style="max-width: 700px;">
        <div class="text-center border-bottom pb-3 mb-3">
            <h3 class="fw-bold mb-0">SLIP GAJI KARYAWAN</h3>
            <p class="text-muted mb-0">Periode: <?= htmlspecialchars($data['bulan_tahun']); ?></p>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <strong>NIP:</strong> <?= htmlspecialchars($data['nip']); ?><br>
                <strong>Nama:</strong> <?= htmlspecialchars($data['nama']); ?>
            </div>
            <div class="col-6 text-end">
                <strong>Jabatan:</strong> <?= htmlspecialchars($data['nama_jabatan']); ?><br>
                <strong>Status:</strong> <span class="badge bg-success"><?= ucfirst($data['status_cair']); ?></span>
            </div>
        </div>

        <table class="table table-bordered my-3">
            <thead class="table-light">
                <tr>
                    <th>Komponen Gaji</th>
                    <th class="text-end">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Gaji Pokok</td>
                    <td class="text-end">Rp <?= number_format($data['gaji_pokok'], 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td>Tunjangan Jabatan</td>
                    <td class="text-end">Rp <?= number_format($data['tunjangan'], 0, ',', '.'); ?></td>
                </tr>
                <tr class="text-danger">
                    <td>Potongan Keterlambatan / Absensi</td>
                    <td class="text-end">- Rp <?= number_format($data['potongan'], 0, ',', '.'); ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="fw-bold table-active">
                    <td>TOTAL GAJI DITERIMA</td>
                    <td class="text-end text-success">Rp <?= number_format($data['total_gaji'], 0, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-primary"><i class="fa-solid fa-print me-1"></i> Cetak Ulang</button>
            <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
        </div>
    </div>

</body>
</html>