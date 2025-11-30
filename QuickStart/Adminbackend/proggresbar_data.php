<?php
$conn = new mysqli("localhost", "root", "", "aiot");

$pbl = $_GET['pbl'] ?? '';
$nim = $_GET['nim'] ?? '';

if ($nim !== '') {
    // Kalau spesifik NIM
    $stmt = $conn->prepare("SELECT status_kehadiran, status_masuk FROM absen WHERE NIM = ?");
    $stmt->bind_param("s", $nim);
} elseif ($pbl !== '') {
    // Kalau filter berdasarkan PBL
    $stmt = $conn->prepare("SELECT status_kehadiran, status_masuk FROM absen WHERE PBL = ?");
    $stmt->bind_param("s", $pbl);
} else {
    // Semua data
    $stmt = $conn->prepare("SELECT status_kehadiran, status_masuk FROM absen");
}

$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$absent = 0;
$late = 0;
$present = 0;

while ($row = $result->fetch_assoc()) {
    $total++;
    if ($row['status_kehadiran'] == "Tidak Hadir") {
        $absent++;
    } elseif ($row['status_masuk'] == "Terlambat") {
        $late++;
    } elseif ($row['status_masuk'] == "Tepat Waktu") {
        $present++;
    }
}

$percent_absent = $total > 0 ? round(($absent / $total) * 100) : 0;
$percent_late = $total > 0 ? round(($late / $total) * 100) : 0;
$percent_present = $total > 0 ? round(($present / $total) * 100) : 0;

header('Content-Type: application/json');
echo json_encode([
    "absent" => $percent_absent,
    "late" => $percent_late,
    "present" => $percent_present
]);
