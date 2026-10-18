<?php
class Payroll
{
    public function __construct(private PDO $db) {}

    public function listByEmployee(int $employeeId): array
    {
        $st = $this->db->prepare(
            "SELECT * FROM payrolls WHERE employee_id = :e ORDER BY bulan_tahun DESC"
        );
        $st->execute(['e' => $employeeId]);
        return $st->fetchAll();
    }

    // Wajib cek employee_id supaya karyawan tidak bisa membuka slip milik orang lain
    public function findForEmployee(int $payrollId, int $employeeId): ?array
    {
        $sql = "SELECT pr.*, e.nip, u.nama, p.nama_jabatan, p.gaji_pokok, p.tunjangan
                FROM payrolls pr
                JOIN employees e ON e.id = pr.employee_id
                JOIN users u ON u.id = e.user_id
                JOIN positions p ON p.id = e.position_id
                WHERE pr.id = :id AND pr.employee_id = :e LIMIT 1";
        $st = $this->db->prepare($sql);
        $st->execute(['id' => $payrollId, 'e' => $employeeId]);
        return $st->fetch() ?: null;
    }
}
