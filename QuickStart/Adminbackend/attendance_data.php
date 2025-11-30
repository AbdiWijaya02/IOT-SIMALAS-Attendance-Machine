<?php
$conn = new mysqli("localhost", "root", "", "aiot");

$tgl_awal = $_GET['start'] ?? '2025-07-01';
$tgl_akhir = $_GET['end'] ?? '2025-07-31';

// Query gabungan
$query = "SELECT 
    SUM(status_masuk = 'Tepat Waktu') AS present,
    SUM(status_masuk = 'Terlambat') AS in_late,
    SUM(status_kehadiran = 'Tidak Hadir') AS absent,
    COUNT(DISTINCT tanggal) AS total_days
FROM absen 
WHERE tanggal BETWEEN ? AND ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $tgl_awal, $tgl_akhir);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode([
    "present" => (int)$result["present"],
    "in_late" => (int)$result["in_late"],
    "absent" => (int)$result["absent"],
    "total" => (int)$result["total_days"]
]);
