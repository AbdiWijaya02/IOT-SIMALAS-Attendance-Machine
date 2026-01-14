<?php
session_start();
include("../classes/connect.php");
include("../classes/login.php");

$db = new Database();
$conn = $db->connect();

// pastikan hanya admin
if (!isset($_SESSION["aiot_userid"]) || $_SESSION["aiot_role"] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action == 'add') {
    $userid   = intval($_POST['userid']);
    $Nama     = $conn->real_escape_string($_POST['Nama']);
    $NIM      = $conn->real_escape_string($_POST['NIM']);
    $PBL      = $conn->real_escape_string($_POST['PBL']);
    $email    = $conn->real_escape_string($_POST['email']);
    $gender   = $conn->real_escape_string($_POST['gender'] ?? '');
    $Angkatan = $conn->real_escape_string($_POST['Angkatan'] ?? '');
    $role     = $conn->real_escape_string($_POST['role']);
    $passRaw  = $_POST['Password'];

    // hash password (pakai bcrypt)
    $Password = password_hash($passRaw, PASSWORD_BCRYPT);

    $sql = "INSERT INTO user (userid, Nama, NIM, PBL, Password, email, gender, Angkatan, role)
            VALUES ($userid, '$Nama', '$NIM', '$PBL', '$Password', '$email', '$gender', '$Angkatan', '$role')";
    $conn->query($sql);
    header("Location: admin.php");
    exit();
}

if ($action == 'edit') {
    $id       = intval($_POST['id']);
    $userid   = intval($_POST['userid']);
    $Nama     = $conn->real_escape_string($_POST['Nama']);
    $NIM      = $conn->real_escape_string($_POST['NIM']);
    $PBL      = $conn->real_escape_string($_POST['PBL']);
    $email    = $conn->real_escape_string($_POST['email']);
    $role     = $conn->real_escape_string($_POST['role']);
    $passRaw  = $_POST['Password'] ?? '';

    $sql = "UPDATE user SET 
                userid = $userid,
                Nama   = '$Nama',
                NIM    = '$NIM',
                PBL    = '$PBL',
                email  = '$email',
                role   = '$role'";

    if (!empty($passRaw)) {
        $Password = password_hash($passRaw, PASSWORD_BCRYPT);
        $sql .= ", Password = '$Password'";
    }
    $sql .= " WHERE id = $id";
    $conn->query($sql);
    header("Location: admin.php");
    exit();
}

if ($action == 'delete') {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM user WHERE id = $id";
    $conn->query($sql);
    header("Location: admin.php");
    exit();
}
