<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Jakarta'); // Pastikan zona waktu benar

$conn = new mysqli("localhost", "root", "", "aiot");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Lokasi file log
$log_file = __DIR__ . '/packet_log.json';

// Fungsi simpan log
function simpanLog($file, $dataLog) {
    if (file_exists($file)) {
        $existing = json_decode(file_get_contents($file), true);
        if (!is_array($existing)) $existing = [];
    } else {
        $existing = [];
    }

    $existing[] = $dataLog; // tambahkan log baru
    file_put_contents($file, json_encode($existing, JSON_PRETTY_PRINT));
}

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
    $packet_combined = implode('', [
        $_POST['packet2_hex'],
        $_POST['packet3_hex'],
        $_POST['packet4_hex'],
        $_POST['packet5_hex'],
        $_POST['packet6_hex'],
        $_POST['packet7_hex']
    ]);

    $action = "";
    $status = "";
    $message = "";

    // Cek apakah user_id sudah ada
    $check = $conn->prepare("SELECT id, Template1, Template2, Template3, Template4,Template5,Template6 FROM packet_log WHERE user_id = ?");
    $check->bind_param("s", $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['id'];

        if ($user_id === "999") {
            $update = $conn->prepare("UPDATE packet_log SET Template1 = ? WHERE id = ?");
            $update->bind_param("si", $packet_combined, $id);
            if ($update->execute()) {
                $message = "Template1 user 999 berhasil diperbarui.";
                $status = "success";
                $action = "update_999";
            } else {
                $message = "Error update user 999: " . $update->error;
                $status = "error";
                $action = "update_999_failed";
            }
            $update->close();
        } else {
            // Isi Template yang kosong secara berurutan
            $template_field = '';
            if (empty($row['Template1'])) $template_field = 'Template1';
            elseif (empty($row['Template2'])) $template_field = 'Template2';
            elseif (empty($row['Template3'])) $template_field = 'Template3';
            elseif (empty($row['Template4'])) $template_field = 'Template4';
            elseif (empty($row['Template5'])) $template_field = 'Template5';
            elseif (empty($row['Template6'])) $template_field = 'Template6';

            if ($template_field !== '') {
                $sql = "UPDATE packet_log SET $template_field = ? WHERE id = ?";
                $update = $conn->prepare($sql);
                $update->bind_param("si", $packet_combined, $id);
                if ($update->execute()) {
                    $message = "Template baru ($template_field) berhasil ditambahkan.";
                    $status = "success";
                    $action = "update_template";
                } else {
                    $message = "Error update: " . $update->error;
                    $status = "error";
                    $action = "update_failed";
                }
                $update->close();
            } else {
                $message = "Semua template sudah terisi untuk user ini.";
                $status = "full";
                $action = "no_update";
            }
        }
    } else {
        // Insert user baru
        $insert = $conn->prepare("INSERT INTO packet_log (user_id, Template1) VALUES (?, ?)");
        $insert->bind_param("ss", $user_id, $packet_combined);
        if ($insert->execute()) {
            $message = ($user_id === "999")
                ? "User 999 ditambahkan dengan Template1."
                : "User baru ditambahkan dengan Template1.";
            $status = "success";
            $action = "insert_new";
        } else {
            $message = "Error insert: " . $insert->error;
            $status = "error";
            $action = "insert_failed";
        }
        $insert->close();
    }

    // Simpan log aktivitas
    simpanLog($log_file, [
        'timestamp' => date('Y-m-d H:i:s'),
        'user_id'   => $user_id,
        'action'    => $action,
        'status'    => $status,
        'message'   => $message,
        'packet_length' => strlen($packet_combined)
    ]);

    echo $message;

    $check->close();
} else {
    echo "Data tidak lengkap.";
    simpanLog($log_file, [
        'timestamp' => date('Y-m-d H:i:s'),
        'action'    => 'invalid_post',
        'status'    => 'error',
        'message'   => 'Data POST tidak lengkap'
    ]);
}

$conn->close();
?>
