<?php
require_once __DIR__ . '/../../function/auth.php';

$payrolls = (new Payroll($db))->listByEmployee((int)$employee['id']);

$pageTitle = 'Slip Gaji';
include __DIR__ . '/../../partials/header.php';
?>
<div class="card">
  <h2>Slip Gaji Saya</h2>
  <table>
    <tr><th>Periode</th><th>Potongan</th><th>Total Gaji</th><th>Status</th><th>Aksi</th></tr>
    <?php foreach ($payrolls as $p): ?>
      <tr>
        <td><?= e($p['bulan_tahun']) ?></td>
        <td><?= rupiah($p['potongan']) ?></td>
        <td><?= rupiah($p['total_gaji']) ?></td>
        <td><span class="badge"><?= e($p['status_cair']) ?></span></td>
        <td><a class="btn" target="_blank" href="cetak.php?id=<?= (int)$p['id'] ?>">Lihat / Unduh</a></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$payrolls): ?><tr><td colspan="5">Belum ada slip gaji.</td></tr><?php endif; ?>
  </table>
</div>
<?php include __DIR__ . '/../../partials/footer.php'; ?>
