<?php
// File: check_locker.php
// Lokasi: C:\xampp\htdocs\AIoT\QuickStart\insertdata\Varbin_data\

// --- 1. KONEKSI DATABASE LANGSUNG (Agar tidak error path) ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aiot"; // Sesuai dengan database di user.sql

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("DB_CONNECTION_ERROR"); 
}
// ------------------------------------------------------------

if (isset($_POST['nim'])) {
    $nim = $_POST['nim'];

    // 1. Cari tahu User ini anggota PBL apa?
    // Kolom PBL dan NIM tersedia di tabel user
    $sqlUser = "SELECT PBL FROM user WHERE NIM = ?";
    $stmt = mysqli_stmt_init($conn);
    
    if (mysqli_stmt_prepare($stmt, $sqlUser)) {
        mysqli_stmt_bind_param($stmt, "s", $nim);
        mysqli_stmt_execute($stmt);
        $resultUser = mysqli_stmt_get_result($stmt);
        
        if ($rowUser = mysqli_fetch_assoc($resultUser)) {
            $userPBL = $rowUser['PBL'];

            // 2. Cari tahu PBL ini dapet Loker nomor berapa?
            // Mencari di tabel 'locker_status' berdasarkan nama PBL
            $sqlLocker = "SELECT locker_number FROM locker_status WHERE PBL = ?";
            $stmt2 = mysqli_stmt_init($conn);
            
            if (mysqli_stmt_prepare($stmt2, $sqlLocker)) {
                mysqli_stmt_bind_param($stmt2, "s", $userPBL);
                mysqli_stmt_execute($stmt2);
                $resultLocker = mysqli_stmt_get_result($stmt2);

                if ($rowLocker = mysqli_fetch_assoc($resultLocker)) {
                    // BERHASIL: Mengirim nomor loker (1, 2, 3, atau 4) murni sebagai teks
                    echo $rowLocker['locker_number'];
                } else {
                    echo "NO_LOCKER"; // PBL belum di-assign ke nomor loker manapun
                }
            }
        } else {
            echo "UNKNOWN_USER"; // NIM tidak ditemukan di tabel user
        }
    } else {
        echo "SQL_ERROR";
    }
} else {
    echo "MISSING_DATA";
}
$conn->close();
?>