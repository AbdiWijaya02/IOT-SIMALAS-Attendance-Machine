<?php
// =======================================================
// ========== FITUR RESET LOG (opsional via ?reset=1) ====
// =======================================================
$log_file = __DIR__ . "/performance_log.txt";

if (isset($_GET['reset']) && $_GET['reset'] == '1') {
    if (file_exists($log_file)) {
        unlink($log_file);
        echo json_encode(["status" => "reset", "message" => "Log file berhasil dihapus dan direset."]);
    } else {
        echo json_encode(["status" => "not_found", "message" => "Log file belum ada."]);
    }
    exit;
}

// =======================================================
// ========== BAGIAN PENGUKURAN PERFORMA KOMUNIKASI ======
// =======================================================
$start_time = microtime(true);
$data = file_get_contents("php://input"); // ambil payload dari ESP32
$packet_size = strlen($data); // byte

// Simulasikan proses utama
$process_start = microtime(true);
// (diisi oleh proses utama nanti)
$process_end = microtime(true);

$end_time = microtime(true);

// Latency total (ms)
$latency = round(($end_time - $start_time) * 1000, 3);
// Throughput (KB/s)
$duration = $end_time - $start_time;
$throughput = $duration > 0 ? round(($packet_size / 1024) / $duration, 3) : 0;

// Ambil jenis proses (default: UPLOAD_TEMPLATE)
$process_type = $_GET['process'] ?? 'UPLOAD_TEMPLATE';

// Hitung jitter dari log sebelumnya
$prev_latency = 0;
$jitter = 0;
$packet_count = 0;

if (file_exists($log_file)) {
    $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $packet_count = count($lines);
    if ($packet_count > 0) {
        $last_line = end($lines);
        if (preg_match('/Latency: ([\d\.]+)/', $last_line, $m)) {
            $prev_latency = floatval($m[1]);
            $jitter = round(abs($latency - $prev_latency), 3);
        }
    }
}
$packet_count++;

// Simulasi packet loss (jika data kosong)
$packet_loss = ($data === "") ? 1 : 0;

// Buat log baru
$log_entry = sprintf(
    "[%s] Process: %s | Latency: %.3f ms | Size: %d bytes | Throughput: %.3f KB/s | Jitter: %.3f ms | Packet Loss: %d | Total Packet: %d\n",
    date("Y-m-d H:i:s"),
    strtoupper($process_type),
    $latency,
    $packet_size,
    $throughput,
    $jitter,
    $packet_loss,
    $packet_count
);
file_put_contents($log_file, $log_entry, FILE_APPEND);

// =======================================================
// ========== BAGIAN UTAMA PROGRAM (Fingerprint) =========
// =======================================================

function byteType($byte)
{
    if (ctype_digit($byte)) return "angka";
    elseif (ctype_alpha($byte)) return "huruf";
    elseif (ctype_alnum($byte)) return "campuran";
    else return "lain";
}

function patternTypeSimilarity($hex1, $hex2)
{
    $length = min(strlen($hex1), strlen($hex2));
    $byteCount = intval($length / 2);
    if ($byteCount == 0) return 0;

    $match = 0;
    for ($i = 0; $i < $byteCount; $i++) {
        $b1 = substr($hex1, $i * 2, 2);
        $b2 = substr($hex2, $i * 2, 2);
        if (byteType($b1) === byteType($b2)) $match++;
    }
    return ($match / $byteCount) * 100;
}

// ======== RESPON UNTUK ESP (topscore=1) ========
if (isset($_GET['topscore']) && $_GET['topscore'] === '1') {
    header('Content-Type: application/json');
    $conn = new mysqli("localhost", "root", "", "aiot");
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["error" => "Database connection error"]);
        exit;
    }

    $users = [];
    $logs = [];

    $u = $conn->query("SELECT userid, Nama, NIM, Template1, Template2, Template3, Template4, Template5, Template6 FROM user");
    while ($row = $u->fetch_assoc()) $users[] = $row;

    $l = $conn->query("SELECT id, Template1 FROM packet_log WHERE user_id = 999 ORDER BY id DESC LIMIT 1");
    while ($row = $l->fetch_assoc()) $logs[] = $row;

    $finalResults = [];

    foreach ($logs as $log) {
        $candidates = [];

        foreach ($users as $user) {
            foreach (['Template1', 'Template2', 'Template3', 'Template4', 'Template5', 'Template6'] as $templateField) {
                $userTemplate = $user[$templateField];
                $logTemplate = $log['Template1'];
                if (!$userTemplate || !$logTemplate) continue;

                $b3_user = substr($userTemplate, 4, 2);
                $b3_log = substr($logTemplate, 4, 2);
                $b4_user = substr($userTemplate, 6, 2);
                $b4_log = substr($logTemplate, 6, 2);

                if (!ctype_xdigit($b3_user) || !ctype_xdigit($b3_log) ||
                    !ctype_xdigit($b4_user) || !ctype_xdigit($b4_log)) continue;

                $d3 = abs(hexdec($b3_user) - hexdec($b3_log));
                $d4 = abs(hexdec($b4_user) - hexdec($b4_log));
                $avg_distance = ($d3 + $d4) / 2;

                $byte_similarity = 100 - (($avg_distance / 255) * 100);
                $pattern_similarity = patternTypeSimilarity($userTemplate, $logTemplate);

                $candidates[] = [
                    'user_id' => $user['userid'],
                    'user_name' => $user['Nama'],
                    'NIM' => $user['NIM'],
                    'template_used' => $templateField,
                    'score' => 0,
                    'byte_similarity' => $byte_similarity,
                    'pattern_similarity' => $pattern_similarity,
                    'b3_distance' => $d3,
                    'b4_distance' => $d4,
                ];
            }
        }

        // Skoring
        foreach (['b3_distance', 'b4_distance'] as $crit) {
            $min = min(array_column($candidates, $crit));
            foreach ($candidates as &$c) {
                if ($c[$crit] == $min) $c['score']++;
            }
        }
        foreach (['byte_similarity', 'pattern_similarity'] as $crit) {
            $max = max(array_column($candidates, $crit));
            foreach ($candidates as &$c) {
                if ($c[$crit] == $max) $c['score']++;
            }
        }

        usort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);
        $finalResults = array_merge($finalResults, $candidates);
    }

    usort($finalResults, fn($a, $b) => $b['score'] <=> $a['score']);
    $top = $finalResults[0] ?? null;

    if ($top) {
    $tid = $top['user_id'];
    $templateField = $top['template_used'];
    $q = $conn->query("SELECT userid, Nama, NIM, `$templateField` FROM user WHERE userid = $tid");
    if ($r = $q->fetch_assoc()) {
        $nim = $r['NIM'];
        $url = "http://localhost/AIoT/QuickStart/insertdata/Varbin_data/update_absen_kalkulasi.php?nim=$nim";
        $response = file_get_contents($url);

        echo json_encode([
            "user_id" => $r['userid'],              // 🔹 tambahkan ini
            "nama" => $r['Nama'],
            "NIM" => $r['NIM'],
            "template_content" => $r[$templateField]
        ]);
        $conn->close();
        exit;
    }
}

    echo json_encode(["error" => "No match found"]);
    $conn->close();
    exit;
}

// ======== HANDLE PINDAH TEMPLATE1–6 (AUTO RUN) ========
if (isset($_GET['run'])) {
    $conn = new mysqli("localhost", "root", "", "aiot");
    header('Content-Type: application/json');
    if ($conn->connect_error) {
        echo json_encode(["status" => "error", "message" => "Koneksi gagal: " . $conn->connect_error]);
        exit;
    }

    $sql = "SELECT id, user_id, Template1, Template2, Template3, Template4, Template5, Template6 FROM packet_log ORDER BY id ASC";
    $result = $conn->query($sql);

    $count_moved = 0;

    while ($row = $result->fetch_assoc()) {
        $log_id = $row['id'];
        $user_id = $row['user_id'];

        if ($user_id == '999') continue;

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
                $count_moved += 6;
                $delete = $conn->prepare("DELETE FROM packet_log WHERE id = ?");
                $delete->bind_param("i", $log_id);
                $delete->execute();
            }
        }
    }
    $checkLeft = $conn->query("SELECT COUNT(*) as total FROM packet_log WHERE user_id != '999'");
    $rowCount = $checkLeft->fetch_assoc()['total'];

    if ($rowCount == 0) {
        $conn->query("DELETE FROM packet_log WHERE user_id != '999'");
        // $conn->query("TRUNCATE TABLE packet_log"); // kalau ingin hapus total (termasuk 999)
    }

    $conn->close();
    echo json_encode(["status" => "success", "moved" => $count_moved]);
    exit;
}

// ======== AJAX UNTUK BROWSER (ajax=1) ========
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json');
    $conn = new mysqli("localhost", "root", "", "aiot");
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["error" => "Database connection error"]);
        exit;
    }

    $users = [];
    $logs = [];

    $u = $conn->query("SELECT id, Nama, Template1, Template2, Template3,Template4,Template5,Template6 FROM user");
    while ($row = $u->fetch_assoc()) $users[] = $row;

    $l = $conn->query("SELECT id, Template1 FROM packet_log");
    while ($row = $l->fetch_assoc()) $logs[] = $row;

    $finalResults = [];

    foreach ($logs as $log) {
        $candidates = [];

        foreach ($users as $user) {
            foreach (['Template1', 'Template2', 'Template3', 'Template4', 'Template5', 'Template6'] as $templateField) {
                $userTemplate = $user[$templateField];
                $logTemplate = $log['Template1'];
                if (!$userTemplate || !$logTemplate) continue;

                $b3_user = substr($userTemplate, 4, 2);
                $b3_log = substr($logTemplate, 4, 2);
                $b4_user = substr($userTemplate, 6, 2);
                $b4_log = substr($logTemplate, 6, 2);

                if (!ctype_xdigit($b3_user) || !ctype_xdigit($b3_log) || 
                    !ctype_xdigit($b4_user) || !ctype_xdigit($b4_log)) {
                    continue;
                }

                $d3 = abs(hexdec($b3_user) - hexdec($b3_log));
                $d4 = abs(hexdec($b4_user) - hexdec($b4_log));
                $avg_distance = ($d3 + $d4) / 2;

                $byte_similarity = 100 - (($avg_distance / 255) * 100);
                $pattern_similarity = patternTypeSimilarity($userTemplate, $logTemplate);

                $candidates[] = [
                    'user_id' => $user['id'],
                    'user_name' => $user['Nama'],
                    'log_id' => $log['id'],
                    'template_used' => $templateField,
                    'b3_user' => $b3_user,
                    'b3_log' => $b3_log,
                    'b4_user' => $b4_user,
                    'b4_log' => $b4_log,
                    'b3_distance' => $d3,
                    'b4_distance' => $d4,
                    'avg_distance' => round($avg_distance, 2),
                    'byte_similarity' => round($byte_similarity, 2),
                    'pattern_similarity' => round($pattern_similarity, 2),
                    'score' => 0
                ];
            }
        }

        foreach (['b3_distance', 'b4_distance'] as $crit) {
            $min = min(array_column($candidates, $crit));
            foreach ($candidates as &$c) {
                if ($c[$crit] == $min) $c['score']++;
            }
        }

        foreach (['byte_similarity', 'pattern_similarity'] as $crit) {
            $max = max(array_column($candidates, $crit));
            foreach ($candidates as &$c) {
                if ($c[$crit] == $max) $c['score']++;
            }
        }

        usort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);
        $finalResults = array_merge($finalResults, $candidates);
    }

    usort($finalResults, fn($a, $b) => $b['score'] <=> $a['score']);
    echo json_encode($finalResults);
    $conn->close();
    exit;
}

// ========== HANDLE KONFIRMASI MATCH ==========
if (isset($_GET['nim'])) {
    $nim = $_GET['nim'];
    $conn = new mysqli("localhost", "root", "", "aiot");
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["error" => "Database connection error"]);
        exit;
    }
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
<html>

<head>
    <title>Fingerprint Matching - Voting Relatif</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background: #f2f2f2;
        }

        .highlight {
            background-color: #d4f4dd;
        }

        #status {
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h2>Fingerprint Matching (Voting Relatif per Log)</h2>

    <div id="status">Memeriksa pemindahan template...</div>

    <table>
        <thead>
            <tr>
                <th>Skor</th>
                <th>User ID</th>
                <th>Nama</th>
                <th>Log ID</th>
                <th>Template</th>
                <th>Byte3 User</th>
                <th>Byte3 Log</th>
                <th>Byte4 User</th>
                <th>Byte4 Log</th>
                <th>Byte3 Dist</th>
                <th>Byte4 Dist</th>
                <th>Byte Similarity</th>
                <th>Pattern Similarity</th>
                <th>Avg Distance</th>
            </tr>
        </thead>
        <tbody id="match-results">
            <tr>
                <td colspan="14">Loading...</td>
            </tr>
        </tbody>
    </table>

    <script>
        function loadResults() {
            fetch("?ajax=1")
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById("match-results");
                    tbody.innerHTML = "";

                    if (data.length === 0) {
                        tbody.innerHTML = "<tr><td colspan='14'>No match found</td></tr>";
                        return;
                    }

                    data.forEach((item, index) => {
                        const row = document.createElement("tr");
                        if (index === 0) row.classList.add("highlight");

                        row.innerHTML = `
                            <td>${item.score}</td>
                            <td>${item.user_id}</td>
                            <td>${item.user_name}</td>
                            <td>${item.log_id}</td>
                            <td>${item.template_used}</td>
                            <td>${item.b3_user}</td>
                            <td>${item.b3_log}</td>
                            <td>${item.b4_user}</td>
                            <td>${item.b4_log}</td>
                            <td>${item.b3_distance}</td>
                            <td>${item.b4_distance}</td>
                            <td>${item.byte_similarity}%</td>
                            <td>${item.pattern_similarity}%</td>
                            <td>${item.avg_distance}</td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => {
                    document.getElementById("match-results").innerHTML =
                        "<tr><td colspan='14'>Failed to load data</td></tr>";
                    console.error("AJAX Error:", error);
                });
        }

        function autoMoveData() {
            fetch("?run=1")
                .then(response => response.json())
                .then(data => {
                    const statusDiv = document.getElementById("status");
                    if (data.status === "success") {
                        statusDiv.innerText = data.moved > 0 ?
                            "Berhasil memindahkan " + data.moved + " template." :
                            "Tidak ada data untuk dipindahkan.";
                    } else {
                        statusDiv.innerText = "Gagal: " + (data.message || "Terjadi kesalahan.");
                    }
                })
                .catch(error => {
                    console.error("Auto-move error:", error);
                    document.getElementById("status").innerText = "Gagal menghubungi server.";
                });
        }

        loadResults();
        setInterval(loadResults, 1000);
        setInterval(autoMoveData, 1000);
    </script>
    <!-- Tambahkan script di bawah ini, sebelum </body> -->
    <script>
        function updateAbsenLoop() {
            fetch("update_absen_kalkulasi.php")
                .then(response => response.text())
                .then(result => {
                    console.log("Absen Update:", result);
                })
                .catch(error => {
                    console.error("Update error:", error);
                });
        }

        // Jalankan pertama kali saat halaman dibuka
        updateAbsenLoop();

        // Ulangi setiap 5 detik (boleh kamu ganti 10 detik, dsb)
        setInterval(updateAbsenLoop, 5000);
    </script>

</body>

</html>