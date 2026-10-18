<?php
// Koneksi PDO (Singleton). Kalau project kamu sudah punya class koneksi di login/database/, pakai itu saja.
class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = 'localhost';
            $db   = 'e_payroll';   // <- ganti sesuai nama database kamu
            $user = 'root';
            $pass = '';
            self::$instance = new PDO(
                "mysql:host=$host;dbname=$db;charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }
        return self::$instance;
    }
}
