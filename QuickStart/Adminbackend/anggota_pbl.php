<?php
$conn = new mysqli("localhost", "root", "", "aiot");

$pbl = $_GET['pbl'] ?? '';

$stmt = $conn->prepare("SELECT NIM, Nama FROM absen WHERE PBL = ? GROUP BY NIM");
$stmt->bind_param("s", $pbl);
$stmt->execute();

$result = $stmt->get_result();

$anggota = [];
while ($row = $result->fetch_assoc()) {
    $anggota[] = $row;
}

header('Content-Type: application/json');
echo json_encode($anggota);
