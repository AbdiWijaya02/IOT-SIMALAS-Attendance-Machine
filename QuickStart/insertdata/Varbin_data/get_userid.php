<?php
// File: get_userid.php
// Lokasi: C:\xampp\htdocs\AIoT\QuickStart\insertdata\Varbin_data\

// --- 1. KONEKSI DATABASE LANGSUNG DISINI ---
// Kita buat koneksi langsung agar tidak perlu memanggil file lain yang bikin error path
$servername = "localhost";
$username = "root";       // Default XAMPP
$password = "";           // Default XAMPP (kosong)
$dbname = "aiot";         // Sesuaikan dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// -------------------------------------------

if (isset($_POST['nim'])) {
    $nim_input = $_POST['nim'];

    // Query mencari userid berdasarkan NIM
    $sql = "SELECT userid FROM user WHERE NIM = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        // Jika query SQL salah ketik
        echo "SQL_ERROR";
    } else {
        mysqli_stmt_bind_param($stmt, "s", $nim_input);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $uid = (int)$row['userid'];
            
            // Validasi Rentang 1-127 (Syarat sensor fingerprint)
            if ($uid >= 1 && $uid <= 127) {
                echo $uid; 
            } else {
                echo "ERROR_RANGE"; 
            }
        } else {
            echo "NOT_FOUND"; // NIM tidak ada di database
        }
    }
} else {
    echo "MISSING_DATA";
}
?>