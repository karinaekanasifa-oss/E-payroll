<?php
class Attendance
{
    private const JAM_BATAS_TELAT = '08:00:00';

    public function __construct(private PDO $db) {}

    public function getByDate(int $employeeId, string $tanggal): ?array
    {
        $st = $this->db->prepare(
            "SELECT * FROM attendances WHERE employee_id = :e AND tanggal = :t LIMIT 1"
        );
        $st->execute(['e' => $employeeId, 't' => $tanggal]);
        return $st->fetch() ?: null;
    }

    public function checkIn(int $employeeId): array
    {
        $today = date('Y-m-d');
        $now   = date('H:i:s');

        if ($this->getByDate($employeeId, $today)) {
            return ['ok' => false, 'msg' => 'Kamu sudah memiliki catatan presensi hari ini.'];
        }

        $status = $now > self::JAM_BATAS_TELAT ? 'terlambat' : 'hadir';
        $st = $this->db->prepare(
            "INSERT INTO attendances (employee_id, tanggal, jam_masuk, status)
             VALUES (:e, :t, :jm, :s)"
        );
        $st->execute(['e' => $employeeId, 't' => $today, 'jm' => $now, 's' => $status]);
        return ['ok' => true, 'msg' => "Presensi masuk tercatat pukul $now ($status)."];
    }

    public function checkOut(int $employeeId): array
    {
        $today = date('Y-m-d');
        $now   = date('H:i:s');
        $row   = $this->getByDate($employeeId, $today);

        if (!$row || empty($row['jam_masuk'])) {
            return ['ok' => false, 'msg' => 'Kamu belum presensi masuk hari ini.'];
        }
        if (!empty($row['jam_keluar'])) {
            return ['ok' => false, 'msg' => 'Kamu sudah presensi keluar hari ini.'];
        }

        $st = $this->db->prepare("UPDATE attendances SET jam_keluar = :jk WHERE id = :id");
        $st->execute(['jk' => $now, 'id' => $row['id']]);
        return ['ok' => true, 'msg' => "Presensi keluar tercatat pukul $now."];
    }

    // Izin / sakit / cuti disimpan sebagai baris attendances dengan status tertentu
    public function submitLeave(int $employeeId, string $tanggal, string $status): array
    {
        $allowed = ['izin', 'sakit', 'cuti'];
        if (!in_array($status, $allowed, true)) {
            return ['ok' => false, 'msg' => 'Jenis pengajuan tidak valid.'];
        }
        $d = DateTime::createFromFormat('Y-m-d', $tanggal);
        if (!$d || $d->format('Y-m-d') !== $tanggal) {
            return ['ok' => false, 'msg' => 'Format tanggal tidak valid.'];
        }
        if ($tanggal < date('Y-m-d')) {
            return ['ok' => false, 'msg' => 'Tanggal tidak boleh sebelum hari ini.'];
        }
        if ($this->getByDate($employeeId, $tanggal)) {
            return ['ok' => false, 'msg' => 'Sudah ada catatan kehadiran pada tanggal tersebut.'];
        }

        $st = $this->db->prepare(
            "INSERT INTO attendances (employee_id, tanggal, status) VALUES (:e, :t, :s)"
        );
        $st->execute(['e' => $employeeId, 't' => $tanggal, 's' => $status]);
        return ['ok' => true, 'msg' => ucfirst($status) . " untuk tanggal $tanggal berhasil diajukan."];
    }

    public function history(int $employeeId, int $limit = 31, array $statuses = []): array
    {
        $sql = "SELECT * FROM attendances WHERE employee_id = :e";
        if ($statuses) {
            $in = implode(',', array_map(fn($s) => $this->db->quote($s), $statuses));
            $sql .= " AND status IN ($in)";
        }
        $sql .= " ORDER BY tanggal DESC LIMIT " . (int)$limit;
        $st = $this->db->prepare($sql);
        $st->execute(['e' => $employeeId]);
        return $st->fetchAll();
    }
}
