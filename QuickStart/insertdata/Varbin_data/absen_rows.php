<?php
session_start();

// Cek sesi NIM
if (!isset($_SESSION["aiot_NIM"])) {
    echo "<tr><td colspan='6'>Sesi tidak ditemukan</td></tr>";
    exit;
}

$nim = $_SESSION["aiot_NIM"];

$conn = new mysqli("localhost", "root", "", "aiot");
date_default_timezone_set("Asia/Jakarta");

$result = $conn->query("SELECT Nama, NIM, PBL, absen_hadir, absen_pulang, tanggal FROM absen WHERE NIM = '$nim' ORDER BY tanggal DESC");

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['Nama']}</td>";
    echo "<td>{$row['NIM']}</td>";
    echo "<td>{$row['PBL']}</td>";
    echo "<td>" . ($row['absen_hadir'] ? date('H:i:s', strtotime($row['absen_hadir'])) : '-') . "</td>";
    echo "<td>" . ($row['absen_pulang'] ? date('H:i:s', strtotime($row['absen_pulang'])) : '-') . "</td>";
    echo "<td>{$row['tanggal']}</td>";
    echo "</tr>";
}

$conn->close();
?>
