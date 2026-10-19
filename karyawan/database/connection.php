<?php
if (!class_exists('Database')) {
    class Database {
        private $host = "localhost";
        private $username = "root";
        private $password = "";
        private $database = "e-payroll";
        public $conn;
        public $pdo;

        public function __construct() {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            if ($this->conn->connect_error) {
                die("Koneksi gagal: " . $this->conn->connect_error);
            }
            try {
                $this->pdo = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database, $this->username, $this->password);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                die("Koneksi PDO gagal: " . $e->getMessage());
            }
        }

        public function getConnection() {
            return $this->pdo;
        }
    }
}
?>