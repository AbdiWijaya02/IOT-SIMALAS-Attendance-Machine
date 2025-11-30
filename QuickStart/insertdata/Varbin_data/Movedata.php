<?php
$conn = new mysqli("localhost", "root", "", "aiot");
if ($conn->connect_error) {
    if (isset($_GET['run']) || isset($_GET['nim'])) {
        echo json_encode(["status" => "error", "message" => "Koneksi gagal: " . $conn->connect_error]);
    } else {
        echo "Koneksi gagal: " . $conn->connect_error;
    }
    exit;
}

// ========== HANDLE PINDAH TEMPLATE1–6 ==========
if (isset($_GET['run'])) {
    $sql = "SELECT id, user_id, Template1, Template2, Template3, Template4, Template5, Template6 FROM packet_log ORDER BY id ASC";
    $result = $conn->query($sql);

    $count_moved = 0;

    while ($row = $result->fetch_assoc()) {
        $log_id = $row['id'];
        $user_id = $row['user_id'];

        // Lewatkan user_id 999
        if ($user_id == '999') {
            continue;
        }

        $templates = [
            $row['Template1'],
            $row['Template2'],
            $row['Template3'],
            $row['Template4'],
            $row['Template5'],
            $row['Template6']
        ];

        $stmt = $conn->prepare("SELECT Template1, Template2, Template3, Template4, Template5, Template6 FROM user WHERE userid = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $user_result = $stmt->get_result();

        if (
            !empty($row['Template1']) && !empty($row['Template2']) &&
            !empty($row['Template3']) && !empty($row['Template4']) &&
            !empty($row['Template5']) && !empty($row['Template6'])
        ) {

            $update = $conn->prepare("
                UPDATE user 
                SET Template1 = ?, Template2 = ?, Template3 = ?, 
                    Template4 = ?, Template5 = ?, Template6 = ?
                WHERE userid = ?
            ");
            $update->bind_param(
                "sssssss",
                $row['Template1'],
                $row['Template2'],
                $row['Template3'],
                $row['Template4'],
                $row['Template5'],
                $row['Template6'],
                $user_id
            );

            if ($update->execute()) {
                $count_moved += 6; // karena kita pindahkan 6 sekaligus
            }

            // Hapus setelah berhasil
            $delete = $conn->prepare("DELETE FROM packet_log WHERE id = ?");
            $delete->bind_param("i", $log_id);
            $delete->execute();
        }
    }

    $conn->close();
    echo json_encode(["status" => "success", "moved" => $count_moved]);
    exit;
}

// ========== HANDLE KONFIRMASI MATCH ==========
if (isset($_GET['nim'])) {
    $nim = $_GET['nim'];

    $stmt = $conn->prepare("SELECT userid FROM user WHERE NIM = ?");
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $target_userid = $user['userid'];

        // Ambil Template1 dari user_id = 999
        $tpl999 = $conn->query("SELECT Template1 FROM packet_log WHERE user_id = '999' ORDER BY id DESC LIMIT 1");
        if ($tpl999->num_rows > 0) {
            $tpl_data = $tpl999->fetch_assoc()['Template1'];

            // Update Template7
            $update = $conn->prepare("UPDATE user SET Template7 = ? WHERE userid = ?");
            $update->bind_param("ss", $tpl_data, $target_userid);
            if ($update->execute()) {
                echo "Template dari user 999 dipindahkan ke Template7 user dengan NIM $nim.";
            } else {
                echo "Gagal update Template7.";
            }
        } else {
            echo "Template dari user 999 tidak ditemukan.";
        }
    } else {
        echo "User dengan NIM $nim tidak ditemukan.";
    }

    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Auto Move Template</title>
</head>

<body>
    <h2>Status Pemindahan Template</h2>
    <div id="status">Menunggu data...</div>

    <script>
        function autoMoveData() {
            fetch("movedata.php?run=1")
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        const moved = data.moved || 0;
                        document.getElementById("status").innerText =
                            moved > 0 ?
                            "Berhasil memindahkan " + moved + " template ke user." :
                            "Tidak ada data baru untuk dipindahkan.";
                    } else {
                        document.getElementById("status").innerText =
                            "Gagal: " + (data.message || "Terjadi kesalahan tidak diketahui.");
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    document.getElementById("status").innerText = "Gagal menghubungi server.";
                });
        }

        // Jalankan setiap 1 detik
        setInterval(autoMoveData, 1000);
    </script>
</body>

</html>