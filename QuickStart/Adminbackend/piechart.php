<?php
$conn = new mysqli("localhost", "root", "", "aiot");

$pbl = $_GET['pbl'] ?? '';
$nim = $_GET['nim'] ?? '';

$query = "SELECT status_kehadiran, status_masuk, tanggal FROM absen";
$params = [];
$types = "";

if ($nim !== '') {
    $query .= " WHERE NIM = ?";
    $params[] = $nim;
    $types = "s";
} elseif ($pbl !== '') {
    $query .= " WHERE PBL = ?";
    $params[] = $pbl;
    $types = "s";
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$absent = 0;
$late = 0;
$present = 0;
$tanggalUnik = [];

while ($row = $result->fetch_assoc()) {
    // Simpan tanggal sebagai key agar unik
    $tanggalUnik[$row['tanggal']] = true;

    if ($row['status_kehadiran'] === "Tidak Hadir") {
        $absent++;
    } elseif ($row['status_masuk'] === "Terlambat") {
        $late++;
    } elseif ($row['status_masuk'] === "Tepat Waktu") {
        $present++;
    }
}

$totalHari = count($tanggalUnik); // Jumlah hari unik

header('Content-Type: application/json');
echo json_encode([
    "absent" => $absent,
    "late" => $late,
    "present" => $present,
    "total" => $totalHari
]);
