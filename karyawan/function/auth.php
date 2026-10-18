<?php
// Include file ini di paling atas setiap halaman karyawan.
require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/Employee.php';
require_once __DIR__ . '/Attendance.php';
require_once __DIR__ . '/Payroll.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ubah sesuai URL project kamu di browser (contoh: /e-payroll/karyawan)
const BASE_URL = '/karyawan';
const LOGIN_URL = '/login/login.php';

// ASUMSI: login.php menyimpan $_SESSION['user_id'] dan $_SESSION['role']
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'karyawan') {
    header('Location: ' . LOGIN_URL);
    exit;
}

date_default_timezone_set('Asia/Jakarta');

$db       = Database::getConnection();
$employee = (new Employee($db))->findByUserId((int)$_SESSION['user_id']);
if (!$employee) {
    exit('Data karyawan tidak ditemukan. Hubungi HRD.');
}

function e(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rupiah(float|int|string $n): string { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }

function flash(?string $msg = null, bool $ok = true): ?array
{
    if ($msg !== null) {
        $_SESSION['flash'] = ['msg' => $msg, 'ok' => $ok];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}
