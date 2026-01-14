<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* Koneksi database */
$host = "localhost";
$user = "root";
$pass = "";
$db   = "aiot";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

/* Ambil data POST dengan aman */
$user_id       = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
$locker_number = isset($_POST['locker_number']) ? trim($_POST['locker_number']) : '';
$status        = isset($_POST['status']) ? trim($_POST['status']) : '';
$waktu_masuk   = isset($_POST['waktu_masuk']) ? trim($_POST['waktu_masuk']) : null;
$waktu_keluar  = isset($_POST['waktu_keluar']) ? trim($_POST['waktu_keluar']) : null;
$durasi        = isset($_POST['durasi']) ? trim($_POST['durasi']) : null;

/* Validasi minimal */
if ($user_id === '' || $locker_number === '' || $status === '') {
    echo "Data tidak lengkap";
    exit;
}

/* Query insert */
$sql = "INSERT INTO log_loker 
        (user_id, locker_number, status, waktu_masuk, waktu_keluar, durasi_detik)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare gagal: " . $conn->error);
}

$stmt->bind_param(
    "sssssi",
    $user_id,
    $locker_number,
    $status,
    $waktu_masuk,
    $waktu_keluar,
    $durasi
);

/* Eksekusi */
if ($stmt->execute()) {
    echo "Log loker berhasil disimpan";
} else {
    echo "Gagal menyimpan log: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
