<?php
require_once __DIR__ . '/../../function/auth.php';

$id   = (int)($_GET['id'] ?? 0);
$slip = (new Payroll($db))->findForEmployee($id, (int)$employee['id']);
if (!$slip) {
    http_response_code(404);
    exit('Slip gaji tidak ditemukan.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Slip Gaji <?= e($slip['bulan_tahun']) ?> - <?= e($slip['nama']) ?></title>
<style>
  body{font-family:Arial,sans-serif;max-width:700px;margin:30px auto;color:#222}
  h2{text-align:center;margin-bottom:4px} .sub{text-align:center;color:#666;margin-bottom:24px}
  table{width:100%;border-collapse:collapse;margin-bottom:16px} td{padding:6px 8px}
  .line td{border-top:1px solid #333;font-weight:bold}
  .no-print{text-align:center;margin-top:24px}
  @media print{.no-print{display:none}}
</style>
</head>
<body>
  <h2>SLIP GAJI KARYAWAN</h2>
  <div class="sub">Periode: <?= e($slip['bulan_tahun']) ?></div>

  <table>
    <tr><td width="30%">Nama</td><td>: <?= e($slip['nama']) ?></td></tr>
    <tr><td>NIP</td><td>: <?= e($slip['nip']) ?></td></tr>
    <tr><td>Jabatan</td><td>: <?= e($slip['nama_jabatan']) ?></td></tr>
    <tr><td>Status</td><td>: <?= e($slip['status_cair']) ?></td></tr>
  </table>

  <table>
    <tr><td>Gaji Pokok</td><td align="right"><?= rupiah($slip['gaji_pokok']) ?></td></tr>
    <tr><td>Tunjangan</td><td align="right"><?= rupiah($slip['tunjangan']) ?></td></tr>
    <tr><td>Potongan</td><td align="right">- <?= rupiah($slip['potongan']) ?></td></tr>
    <tr class="line"><td>Total Gaji Diterima</td><td align="right"><?= rupiah($slip['total_gaji']) ?></td></tr>
  </table>

  <div class="no-print">
    <button onclick="window.print()">Cetak / Simpan sebagai PDF</button>
  </div>
</body>
</html>
