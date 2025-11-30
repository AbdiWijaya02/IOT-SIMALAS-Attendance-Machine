<?php
session_start();
$conn = new mysqli("localhost", "root", "", "aiot"); // Ganti sesuai database kamu

if (!isset($_SESSION["aiot_NIM"])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$nim = $_SESSION["aiot_NIM"];

$query = "SELECT 
    SUM(status_masuk = 'Tepat Waktu') AS present,
    SUM(status_masuk = 'Terlambat') AS in_late,
    SUM(status_kehadiran = 'Tidak Hadir') AS absent
FROM absen
WHERE NIM = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $nim);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode([
    "present" => (int)$result["present"],
    "in_late" => (int)$result["in_late"],
    "absent" => (int)$result["absent"],
    "total" => array_sum($result)
]);
