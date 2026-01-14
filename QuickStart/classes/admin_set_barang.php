<?php
session_start();
include("../classes/connect.php");

if ($_SESSION['aiot_role'] !== 'admin') {
    http_response_code(403);
    exit("Forbidden");
}

$db = new Database();
$conn = $db->connect();

$locker = $_POST['locker'];
$status = $_POST['status']; // IN atau OUT
$admin_id = $_SESSION['aiot_userid'];

if ($status == 'IN') {
    $sql = "INSERT INTO log_loker 
    (user_id, locker_number, status, waktu_masuk, source)
    VALUES (?, ?, 'IN', NOW(), 'admin')";
} else {
    $sql = "INSERT INTO log_loker 
    (user_id, locker_number, status, waktu_keluar, source)
    VALUES (?, ?, 'OUT', NOW(), 'admin')";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $admin_id, $locker);
$stmt->execute();

echo "OK";
?>