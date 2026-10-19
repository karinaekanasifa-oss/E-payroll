<?php
/**
 * --------------------------------------------------------------------------
 * ADVANCED SECURITY AUTHENTICATION CLASS - E-PAYROLL SYSTEM (MYSQLI SUPPORT)
 * --------------------------------------------------------------------------
 */

// Panggil file koneksi secara aman lintas modul
if (!class_exists('Database')) {
    $paths = [
        __DIR__ . '/../../karyawan/database/connection.php',
        __DIR__ . '/../database/connection.php',
        '../database/connection.php',
        '../../karyawan/database/connection.php',
        '../../hrd/database/connection.php'
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            break;
        }
    }
}

class Auth {
    private $conn;

    public function __construct() {
        if (class_exists('Database')) {
            $db = new Database();
            // Mengambil koneksi MySQLi ($this->conn)
            $this->conn = isset($db->conn) ? $db->conn : null;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            session_start();
        }
    }

    public function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function verifyCsrfToken($token) {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }
        return true;
    }

    private function checkRateLimit() {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['lockout_time'] = 0;
        }

        if ($_SESSION['login_attempts'] >= 5) {
            $lockout_duration = 30;
            $time_left = $lockout_duration - (time() - $_SESSION['lockout_time']);
            
            if ($time_left > 0) {
                return "Terlalu banyak percobaan gagal. Silakan tunggu $time_left detik lagi.";
            } else {
                $_SESSION['login_attempts'] = 0;
            }
        }
        return null;
    }

    public function login($email, $password, $csrf_token) {
        if (!$this->verifyCsrfToken($csrf_token)) {
            return "Validasi keamanan gagal (Invalid CSRF Token).";
        }

        $rateLimitError = $this->checkRateLimit();
        if ($rateLimitError) {
            return $rateLimitError;
        }

        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['login_attempts']++;
            return "Format email tidak valid!";
        }

        // Kueri Login Menggunakan Standar MySQLi
        $stmt = $this->conn->prepare("SELECT users.*, positions.nama_jabatan 
                                      FROM users 
                                      LEFT JOIN employees ON users.id = employees.user_id 
                                      LEFT JOIN positions ON employees.position_id = positions.id 
                                      WHERE users.email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Mendukung password hash maupun teks biasa
            $passwordMatch = password_verify($password, $user['password']) || ($password === $user['password']);

            if ($passwordMatch) {
                $_SESSION['login_attempts'] = 0;
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama']    = $user['nama'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = strtolower(trim($user['role']));
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                $stmtEmp = $this->conn->prepare("SELECT id FROM employees WHERE user_id = ? LIMIT 1");
                $stmtEmp->bind_param("i", $user['id']);
                $stmtEmp->execute();
                $empRes = $stmtEmp->get_result()->fetch_assoc();
                if ($empRes) {
                    $_SESSION['employee_id'] = $empRes['id'];
                }

                unset($_SESSION['csrf_token']);

                $roleUser = $_SESSION['role'];
                $namaJabatan = strtolower(trim($user['nama_jabatan'] ?? ''));

                // Routing Tujuan Berdasarkan Role secara Otomatis
                $basePath = '/E-payroll';

                if ($roleUser == 'hrd' || str_contains($namaJabatan, 'hrd')) {
                    header("Location: " . $basePath . "/hrd/index.php");
                } elseif ($roleUser == 'finance' || str_contains($namaJabatan, 'finance')) {
                    header("Location: " . $basePath . "/finance/index.php");
                } elseif ($roleUser == 'karyawan') {
                    header("Location: " . $basePath . "/karyawan/pages/dashboard.php");
                } else {
                    header("Location: " . $basePath . "/index.php");
                }
                exit;
            } else {
                $_SESSION['login_attempts']++;
                $_SESSION['lockout_time'] = time();
                return "Email atau password salah!";
            }
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['lockout_time'] = time();
            return "Email atau password salah!";
        }
    }

    public function requireLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            header("Location: /E-payroll/login/login.php");
            exit;
        }

        if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            $this->logout();
            exit;
        }
    }

    public function restrictRole($allowed_role) {
        $this->requireLogin();

        if (strtolower($_SESSION['role'] ?? '') !== strtolower($allowed_role)) {
            echo "<script>alert('Peringatan Keamanan: Akses ilegal ditolak!'); window.location='/E-payroll/login/login.php';</script>";
            exit;
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        header("Location: /E-payroll/login/login.php");
        exit;
    }
}
?>