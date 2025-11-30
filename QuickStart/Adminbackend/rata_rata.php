<?php
header('Content-Type: application/json');
date_default_timezone_set("Asia/Jakarta");

$conn = new mysqli("localhost", "root", "", "aiot");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Koneksi gagal"]);
    exit;
}

$metric = $_GET['metric'] ?? 'jam_masuk';
$pbl = $_GET['pbl'] ?? '';
$nim = $_GET['nim'] ?? '';

$data = [];

for ($i = 1; $i <= 12; $i++) {
    $baseQuery = "";
    $paramTypes = "";
    $params = [];

    // Pilih query sesuai metric
    if ($metric == 'jam_masuk') {
        $baseQuery = "SELECT AVG(TIME_TO_SEC(TIME(absen_hadir))) as total 
                      FROM absen WHERE absen_hadir IS NOT NULL AND MONTH(tanggal) = ?";
    } elseif ($metric == 'jam_pulang') {
        $baseQuery = "SELECT AVG(TIME_TO_SEC(TIME(absen_pulang))) as total 
                      FROM absen WHERE absen_pulang IS NOT NULL AND MONTH(tanggal) = ?";
    } elseif ($metric == 'durasi_jam') {
        $baseQuery = "SELECT AVG(durasi_jam) as total 
                      FROM absen WHERE durasi_jam IS NOT NULL AND MONTH(tanggal) = ?";
    } else {
        $data[] = 0;
        continue;
    }

    // Tambah filter berdasarkan NIM atau PBL
    $params[] = $i;
    $paramTypes .= "i";

    if (!empty($nim)) {
        $baseQuery .= " AND NIM = ?";
        $params[] = $nim;
        $paramTypes .= "s";
    } elseif (!empty($pbl)) {
        $baseQuery .= " AND PBL = ?";
        $params[] = $pbl;
        $paramTypes .= "s";
    }

    $stmt = $conn->prepare($baseQuery);
    if ($paramTypes !== "") {
        $stmt->bind_param($paramTypes, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $value = $row['total'];

    if ($metric === 'durasi_jam') {
        $data[] = $value ? round($value, 2) : 0;
    } else {
        $data[] = $value ? round($value / 3600, 2) : 0;
    }
}

echo json_encode([
    "status" => "success",
    "data" => $data
]);
$conn->close();
