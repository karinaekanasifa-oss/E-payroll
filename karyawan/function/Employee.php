<?php
class Employee
{
    public function __construct(private PDO $db) {}

    // Ambil data karyawan + jabatan berdasarkan users.id (dari session login)
    public function findByUserId(int $userId): ?array
    {
        $sql = "SELECT e.id, e.nip, e.tgl_masuk, u.nama, u.email,
                       p.nama_jabatan, p.gaji_pokok, p.tunjangan
                FROM employees e
                JOIN users u ON u.id = e.user_id
                JOIN positions p ON p.id = e.position_id
                WHERE e.user_id = :uid LIMIT 1";
        $st = $this->db->prepare($sql);
        $st->execute(['uid' => $userId]);
        return $st->fetch() ?: null;
    }
}
