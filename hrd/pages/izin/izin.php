<?php
// Path koneksi dari hrd/pages/izin/izin.php ke hrd/database/connection.php
require_once __DIR__ . '/../../database/connection.php';
$db = new Database();
$conn = $db->conn;

// 1. LOGIKA AKSI SETUJUI / TOLAK PENGAJUAN IZIN
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $status_baru = 'sudah';

    $updateStmt = $conn->prepare("UPDATE notifications SET status_baca = ? WHERE id = ?");
    $updateStmt->bind_param("si", $status_baru, $id);
    
    if ($updateStmt->execute()) {
        echo "<script>
                alert('Status permohonan izin berhasil diperbarui!');
                window.location.href = 'index.php?page=izin';
              </script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui status izin.'); window.location.href = 'index.php?page=izin';</script>";
        exit;
    }
}

// 2. LOGIKA PAGINATION (5 DATA PER HALAMAN)
$limit = 5; 
$page_no = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
if ($page_no < 1) $page_no = 1;
$offset = ($page_no - 1) * $limit;

$total_query = "SELECT COUNT(*) as total FROM notifications";
$total_result = $conn->query($total_query);
$total_rows = $total_result ? $total_result->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_rows / $limit);

$query = "SELECT notifications.*, users.nama AS nama_karyawan, employees.nip 
          FROM notifications 
          LEFT JOIN employees ON notifications.employee_id = employees.id 
          LEFT JOIN users ON employees.user_id = users.id 
          ORDER BY notifications.id DESC 
          LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$data_pengajuan = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data_pengajuan[] = $row;
    }
}
?>

<style>
    /* Header Banner Tema Hijau-Putih */
    .header-card-hrd {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #bbf7d0;
        border-radius: 1.25rem;
        padding: 1.75rem 2rem;
        box-shadow: 0 10px 25px rgba(20, 83, 45, 0.05);
    }

    .btn-approve {
        background-color: #10b981;
        color: #ffffff;
        border: none;
        font-weight: 600;
        border-radius: 50rem;
        padding: 0.35rem 1rem;
        font-size: 0.82rem;
        transition: all 0.2s ease;
    }
    .btn-approve:hover { background-color: #059669; color: #ffffff; }

    .btn-reject {
        background-color: #ef4444;
        color: #ffffff;
        border: none;
        font-weight: 600;
        border-radius: 50rem;
        padding: 0.35rem 1rem;
        font-size: 0.82rem;
        transition: all 0.2s ease;
    }
    .btn-reject:hover { background-color: #dc2626; color: #ffffff; }

    .btn-disabled-custom {
        background-color: #94a3b8 !important;
        color: #ffffff !important;
        border: none;
        font-weight: 600;
        border-radius: 50rem;
        padding: 0.35rem 1rem;
        font-size: 0.82rem;
        cursor: not-allowed;
    }

    /* Styling Pagination Indonesia Tema Hijau Tua */
    .pagination .page-item.active .page-link {
        background-color: #143423;
        border-color: #143423;
        color: #ffffff;
    }
    .pagination .page-link {
        color: #143423;
        border-radius: 0.5rem;
        margin: 0 2px;
        font-weight: 600;
    }
    .pagination .page-link:hover {
        background-color: #e8f5e9;
        color: #0b1d13;
    }
</style>

<div class="container-fluid px-4 py-4">
    
    <!-- Header Banner Hijau Putih -->
    <div class="header-card-hrd mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
            <span class="badge px-3 py-2 rounded-pill fw-bold mb-2 shadow-sm" style="background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;">
                <i class="fa-solid fa-clipboard-check me-1"></i> Modul Verifikasi HRD
            </span>
            <h3 class="fw-extrabold text-dark mb-1">Manajemen Pengajuan Izin / Cuti</h3>
            <p class="text-muted mb-0 small">Periksa dan berikan tanggapan persetujuan pada permohonan izin karyawan.</p>
        </div>
        <div class="mt-3 mt-md-0 text-md-end">
            <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm fw-semibold">
                Total Data: <?= $total_rows; ?> Pengajuan
            </span>
        </div>
    </div>

    <!-- Card Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama Karyawan</th>
                            <th>Perihal / Judul</th>
                            <th>Keterangan / Alasan</th>
                            <th class="text-center">Status HRD</th>
                            <th>Tanggal Kirim</th>
                            <th class="text-center" style="width: 200px;">Aksi HRD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data_pengajuan) > 0): ?>
                            <?php $no = $offset + 1; foreach ($data_pengajuan as $row): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_karyawan'] ?? 'Karyawan'); ?></div>
                                    <small class="text-muted">NIP: <?= htmlspecialchars($row['nip'] ?? '-'); ?></small>
                                </td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($row['judul']); ?></td>
                                <td class="text-secondary small" style="max-width: 250px;"><?= htmlspecialchars($row['pesan']); ?></td>
                                <td class="text-center">
                                    <?php 
                                        $status = strtolower(trim($row['status_baca'] ?? 'belum'));
                                        if ($status === 'sudah') {
                                            echo '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-check me-1"></i> Telah Direspons</span>';
                                        } else {
                                            echo '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-clock me-1"></i> Menunggu</span>';
                                        }
                                    ?>
                                </td>
                                <td class="text-muted small"><?= date('d M Y H:i', strtotime($row['created_at'])); ?></td>
                                <td class="text-center">
                                    <?php 
                                        $status = strtolower(trim($row['status_baca'] ?? 'belum'));
                                    ?>

                                    <?php if ($status === 'sudah'): ?>
                                        <button class="btn btn-disabled-custom btn-sm" disabled>
                                            <i class="fa-solid fa-check-double me-1"></i> Sudah Diproses
                                        </button>
                                    <?php else: ?>
                                        <a href="index.php?page=izin&action=setujui&id=<?= $row['id']; ?>" 
                                           class="btn btn-approve btn-sm me-1" 
                                           onclick="return confirm('Proses pengajuan izin ini?')">
                                            <i class="fa-solid fa-check me-1"></i> Setujui
                                        </a>
                                        <a href="index.php?page=izin&action=tolak&id=<?= $row['id']; ?>" 
                                           class="btn btn-reject btn-sm" 
                                           onclick="return confirm('Proses pengajuan izin ini?')">
                                            <i class="fa-solid fa-xmark me-1"></i> Tolak
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Empty" style="width: 70px;" class="mb-3 opacity-50"><br>
                                    <span class="text-muted fw-semibold">Belum ada data pengajuan izin dari karyawan.</span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer Control (Bahasa Indonesia & Tema Hijau) -->
            <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <div class="text-muted small">
                    Menampilkan halaman <strong><?= $page_no; ?></strong> dari <strong><?= $total_pages; ?></strong> (Total <?= $total_rows; ?> Data Pengajuan)
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= ($page_no <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?page=izin&p=<?= $page_no - 1; ?>">Sebelumnya</a>
                        </li>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($page_no == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="index.php?page=izin&p=<?= $i; ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page_no >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?page=izin&p=<?= $page_no + 1; ?>">Selanjutnya</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php else: ?>
            <div class="table-footer-control mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                <span class="badge bg-light text-success border px-3 py-2 rounded-2">Status: Sinkron Aktif</span>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>