<?php
session_start();
$conn = new mysqli("localhost", "root", "", "aiot");

if (!isset($_SESSION["aiot_NIM"])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$nim = $_SESSION["aiot_NIM"];

// Ambil semua tanggal dan status
$query = "SELECT tanggal, status_masuk, status_kehadiran FROM absen WHERE NIM = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $nim);
$stmt->execute();
$result = $stmt->get_result();

$absenData = [];
while ($row = $result->fetch_assoc()) {
    $tgl = date("Y-m-d", strtotime($row["tanggal"]));
    if ($row["status_kehadiran"] == "Tidak Hadir") {
        $absenData[$tgl] = "Absent";
    } else if ($row["status_masuk"] == "Terlambat") {
        $absenData[$tgl] = "In Late";
    } else if ($row["status_masuk"] == "Tepat Waktu") {
        $absenData[$tgl] = "Present";
    }
}

echo json_encode($absenData);
