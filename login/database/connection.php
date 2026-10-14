<?php

class Database
{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "e-payroll";

    public $conn;
    // construct untuk menjalankan koneksi ke database saat objek dibuat
    // ketika ada construct ketika membuat new database maka otomatis akan menjalankan koneksi ke database
    // mysqli adalah ekstensi PHP untuk mengakses database MySQL
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