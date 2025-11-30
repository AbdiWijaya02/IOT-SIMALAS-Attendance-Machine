<?php
session_start();
$conn = new mysqli("localhost", "root", "", "aiot");

if (!isset($_SESSION["aiot_NIM"])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$nim = $_SESSION["aiot_NIM"];
$tanggal = date("Y-m-d");

// Hitung data total user ini dari awal tahun / atau semua
$q = $conn->query("SELECT status_kehadiran, status_masuk FROM absen WHERE NIM = '$nim'");
$total = 0;
$absent = 0;
$late = 0;
$present = 0;

if ($q) {
    $total = $q->num_rows;
    while ($row = $q->fetch_assoc()) {
        if ($row['status_kehadiran'] == "Tidak Hadir") {
            $absent++;
        } elseif ($row['status_masuk'] == "Terlambat") {
            $late++;
        } elseif ($row['status_masuk'] == "Tepat Waktu") {
            $present++;
        }
    }
}

$percent_absent = $total > 0 ? round(($absent / $total) * 100) : 0;
$percent_late = $total > 0 ? round(($late / $total) * 100) : 0;
$percent_present = $total > 0 ? round(($present / $total) * 100) : 0;

echo json_encode([
    "absent" => $percent_absent,
    "late" => $percent_late,
    "present" => $percent_present
]);
