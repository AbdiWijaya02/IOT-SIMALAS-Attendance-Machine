<?php
header("Content-Type: application/json");

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "aiot");
if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi gagal"]));
}

$sql = "SELECT Template1 FROM packet_log WHERE user_id = 999";
$result = $conn->query($sql);

if ($row = $result->fetch_assoc()) {
    // Ubah biner ke hex
    $hexString = bin2hex($row["Template1"]);
    echo json_encode([
        "success" => true,
        "template" => $hexString
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Data tidak ditemukan"]);
}
?>
