<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "aiot";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari database
$sql = "SELECT Paket2 FROM user WHERE Nama = 'Joy'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $packet2_retrieved = $row['Paket2'];

    // Konversi ke array kembali
    $packet2_array = unpack("C*", $packet2_retrieved);
    print_r($packet2_array);
} else {
    echo "Data tidak ditemukan!";
}

$conn->close();
?>
