<?php
$conn = new mysqli("localhost", "root", "", "aiot");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "SELECT user_id, Template1 FROM packet_log ORDER BY id ASC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $user_id = $row['user_id'];
    $new_template = $row['Template1'];

    // Ambil data user saat ini
    $stmt = $conn->prepare("SELECT Template1, Template2, Template3, Template4 FROM user WHERE userid = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();

        if (empty($user_data['Template1'])) {
            $target_column = "Template1";
        } elseif (empty($user_data['Template2'])) {
            $target_column = "Template2";
        } elseif (empty($user_data['Template3'])) {
            $target_column = "Template3";
        } elseif (empty($user_data['Template4'])) {
            $target_column = "Template4";
        } else {
            // Sudah penuh, skip
            continue;
        }

        // Update ke kolom yang kosong
        $query = "UPDATE user SET $target_column = ? WHERE userid = ?";
        $update_stmt = $conn->prepare($query);
        $update_stmt->bind_param("ss", $new_template, $user_id);
        $update_stmt->execute();
    }
}

echo "Data berhasil disimpan ke Template1-4 secara berurutan.";
$conn->close();
?>
