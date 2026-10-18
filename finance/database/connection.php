<?php
/**
 * --------------------------------------------------------------------------
 * DATABASE CONNECTION - HRD MODULE
 * --------------------------------------------------------------------------
 */

if (!class_exists('Database')) {
    class Database
    {
        private $host = "localhost";
        private $username = "root";
        private $password = "";
        private $database = "e-payroll";

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