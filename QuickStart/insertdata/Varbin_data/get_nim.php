<?php
// File: get_nim.php
// Lokasi: C:\xampp\htdocs\AIoT\QuickStart\insertdata\Varbin_data\

// --- 1. KONEKSI DATABASE LANGSUNG ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aiot"; // Pastikan nama database sesuai (huruf kecil semua sesuai dump SQL anda)

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    // Jangan echo HTML error, cukup echo text singkat agar ESP32 paham
    die("DB_ERROR"); 
}
// ------------------------------------

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Query mencari NIM berdasarkan userid (ID sidik jari)
    $sql = "SELECT NIM FROM user WHERE userid = ?";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            echo $row['NIM']; // Mengembalikan NIM murni (tanpa spasi/html)
        } else {
            echo "NOT_FOUND";
        }
    } else {
        echo "SQL_ERROR";
    }
} else {
    echo "MISSING_DATA";
}
?>