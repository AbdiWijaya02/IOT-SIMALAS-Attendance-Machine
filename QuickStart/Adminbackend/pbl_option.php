<?php
$conn = new mysqli("localhost", "root", "", "aiot");

$q = $conn->query("SELECT DISTINCT PBL FROM absen ORDER BY PBL");

$options = [];

while ($row = $q->fetch_assoc()) {
    $options[] = $row['PBL'];
}

echo json_encode($options);
