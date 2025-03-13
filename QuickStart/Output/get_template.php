<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "aiot");

if ($conn->connect_error) {
    echo json_encode(["error" => "Koneksi gagal"]);
    exit;
}

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $stmt = $conn->prepare("SELECT Template1 FROM user WHERE userid = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hex = $row["Template1"]; // Sudah disimpan dalam bentuk string hex
        echo json_encode(["template" => $hex]);
    } else {
        echo json_encode(["error" => "Data tidak ditemukan"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Parameter user_id tidak ditemukan"]);
}

$conn->close();
?>
