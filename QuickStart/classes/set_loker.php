<?php
include "connect.php";

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"), true);

foreach ($data as $entry) {
    $locker = $conn->real_escape_string($entry['locker']);
    $pbl = $conn->real_escape_string($entry['pbl']);

    if (strtolower($pbl) === 'available') {
        // Hapus entri jika PBL adalah "Available"
        $sql = "DELETE FROM locker_status WHERE locker_number = '$locker'";
    } else {
        // Insert atau update
        $sql = "INSERT INTO locker_status (locker_number, PBL)
                VALUES ('$locker', '$pbl')
                ON DUPLICATE KEY UPDATE PBL = '$pbl'";
                
    }

    $conn->query($sql);
}

echo "Data berhasil disimpan.";
