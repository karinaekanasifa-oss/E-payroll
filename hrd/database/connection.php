<?php
/**
 * --------------------------------------------------------------------------
 * DATABASE CONNECTION - E-PAYROLL SYSTEM
 * --------------------------------------------------------------------------
 */

if (!class_exists('Database')) {
    class Database
    {
        private $host = "localhost";
        private $username = "root";
        private $password = "";
        private $database = "e-payroll"; // <-- Ubah ke e-payroll, jangan rental-mobil

        public $conn;

        public function __construct()
        {
            $this->conn = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database
            );

            if ($this->conn->connect_error) {
                die("Koneksi gagal: " . $this->conn->connect_error);
            }
        }
    }
}
?>