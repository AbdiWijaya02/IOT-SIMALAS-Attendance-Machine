<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi database
$conn = new mysqli("localhost", "root", "", "aiot");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek data POST
if (
    isset($_POST['user_id']) &&
    isset($_POST['packet2_hex']) &&
    isset($_POST['packet3_hex']) &&
    isset($_POST['packet4_hex']) &&
    isset($_POST['packet5_hex']) &&
    isset($_POST['packet6_hex']) &&
    isset($_POST['packet7_hex'])
) {
    $user_id = htmlspecialchars(trim($_POST['user_id']));

    // Gabungkan semua hex
    $packet_combined = implode('', [
        $_POST['packet2_hex'],
        $_POST['packet3_hex'],
        $_POST['packet4_hex'],
        $_POST['packet5_hex'],
        $_POST['packet6_hex'],
        $_POST['packet7_hex']
    ]);

    // Konversi ke binary (BLOB)
    $packet_binary = hex2bin($packet_combined);
    if ($packet_binary === false) {
        echo "Format hex tidak valid.";
        exit;
    }

    // Cek apakah user_id sudah ada
    $check = $conn->prepare("SELECT id, Template1, Template2, Template3, Template4 FROM packet_log WHERE user_id = ?");
    $check->bind_param("s", $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['id'];

        // Tentukan kolom kosong berikutnya
        if (empty($row['Template1'])) {
            $update = $conn->prepare("UPDATE packet_log SET Template1 = ? WHERE id = ?");
        } elseif (empty($row['Template2'])) {
            $update = $conn->prepare("UPDATE packet_log SET Template2 = ? WHERE id = ?");
        } elseif (empty($row['Template3'])) {
            $update = $conn->prepare("UPDATE packet_log SET Template3 = ? WHERE id = ?");
        } elseif (empty($row['Template4'])) {
            $update = $conn->prepare("UPDATE packet_log SET Template4 = ? WHERE id = ?");
        } else {
            echo "Semua template (1–4) sudah terisi untuk user ini.";
            exit;
        }

        $update->bind_param("si", $packet_binary, $id);
        if ($update->execute()) {
            echo "Template baru berhasil ditambahkan (binary) untuk user yang sudah ada.";
        } else {
            echo "Error update: " . $update->error;
        }
        $update->close();
    } else {
       $insert = $conn->prepare("INSERT INTO packet_log (user_id, Template1) VALUES (?, ?)");
        $insert->bind_param("sb", $user_id, $packet_binary);
        $insert->send_long_data(1, $packet_binary); // pastikan ini setelah bind_param
        $insert->execute();

        if ($insert->execute()) {
            echo "User baru ditambahkan dengan Template1 (binary).";
        } else {
            echo "Error insert: " . $insert->error;
        }
        $insert->close();
    }

    $check->close();
} else {
    echo "Data tidak lengkap.";
}

$conn->close();
?>
