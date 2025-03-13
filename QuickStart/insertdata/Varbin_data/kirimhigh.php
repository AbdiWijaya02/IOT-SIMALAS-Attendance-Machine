<?php
// === Fungsi global (digunakan oleh ajax & topmatch) ===
function byteType($byte) {
    if (ctype_digit($byte)) return "angka";
    elseif (ctype_alpha($byte)) return "huruf";
    elseif (ctype_alnum($byte)) return "campuran";
    else return "lain";
}

function patternTypeSimilarity($hex1, $hex2) {
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

// === Endpoint untuk ESP32 ===
if (isset($_GET['topmatch']) && $_GET['topmatch'] === '1') {
    header('Content-Type: application/json');
    $conn = new mysqli("localhost", "root", "", "aiot");
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["error" => "DB connection error"]);
        exit;
    }

    $users = [];
    $logs = [];

    $u = $conn->query("SELECT id, Nama, Template1, Template2, Template3 FROM user");
    while ($row = $u->fetch_assoc()) $users[] = $row;

    $l = $conn->query("SELECT id, Template1 FROM packet_log ORDER BY id DESC LIMIT 1");
    while ($row = $l->fetch_assoc()) $logs[] = $row;

    $bestMatch = null;

    foreach ($users as $user) {
        foreach ($logs as $log) {
            foreach (['Template1', 'Template2', 'Template3'] as $templateField) {
                $userTemplate = $user[$templateField];
                $logTemplate = $log['Template1'];

                $b3_user = substr($userTemplate, 4, 2);
                $b3_log  = substr($logTemplate, 4, 2);
                if (byteType($b3_user) !== byteType($b3_log)) continue;

                $b4_user = substr($userTemplate, 6, 2);
                $b4_log  = substr($logTemplate, 6, 2);
                if (!ctype_xdigit($b3_user) || !ctype_xdigit($b3_log) ||
                    !ctype_xdigit($b4_user) || !ctype_xdigit($b4_log)) {
                    continue;
                }

                $d3 = abs(hexdec($b3_user) - hexdec($b3_log));
                $d4 = abs(hexdec($b4_user) - hexdec($b4_log));
                $avg_distance = ($d3 + $d4) / 2;
                $byte_similarity = 100 - (($avg_distance / 255) * 100);
                if ($byte_similarity < 60) continue;

                $pattern_similarity = patternTypeSimilarity($userTemplate, $logTemplate);

                $currentMatch = [
                    'user_id' => $user['id'],
                    'user_name' => $user['Nama'],
                    'log_id' => $log['id'],
                    'template_used' => $templateField,
                    'template_content' => $userTemplate,
                    'byte_similarity' => round($byte_similarity, 2),
                    'pattern_similarity' => round($pattern_similarity, 2)
                ];

                if (!$bestMatch || $byte_similarity > $bestMatch['byte_similarity']) {
                    $bestMatch = $currentMatch;
                }
            }
        }
    }

    echo $bestMatch ? json_encode($bestMatch) : json_encode(["message" => "No match found"]);
    $conn->close();
    exit;
}

// === Endpoint AJAX untuk tampilan web ===
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

    $u = $conn->query("SELECT id, Nama, Template1, Template2, Template3 FROM user");
    while ($row = $u->fetch_assoc()) $users[] = $row;

    $l = $conn->query("SELECT id, Template1 FROM packet_log");
    while ($row = $l->fetch_assoc()) $logs[] = $row;

    $results = [];
    foreach ($users as $user) {
        foreach ($logs as $log) {
            foreach (['Template1', 'Template2', 'Template3'] as $templateField) {
                $userTemplate = $user[$templateField];
                $logTemplate = $log['Template1'];

                $b3_user = substr($userTemplate, 4, 2);
                $b3_log  = substr($logTemplate, 4, 2);

                if (byteType($b3_user) !== byteType($b3_log)) continue;

                $b4_user = substr($userTemplate, 6, 2);
                $b4_log  = substr($logTemplate, 6, 2);

                if (!ctype_xdigit($b3_user) || !ctype_xdigit($b3_log) ||
                    !ctype_xdigit($b4_user) || !ctype_xdigit($b4_log)) {
                    continue;
                }

                $d3 = abs(hexdec($b3_user) - hexdec($b3_log));
                $d4 = abs(hexdec($b4_user) - hexdec($b4_log));
                $avg_distance = ($d3 + $d4) / 2;

                $byte_similarity = 100 - (($avg_distance / 255) * 100);
                if ($byte_similarity < 60) continue;

                $pattern_similarity = patternTypeSimilarity($userTemplate, $logTemplate);

                $results[] = [
                    'user_id' => $user['id'],
                    'user_name' => $user['Nama'],
                    'log_id' => $log['id'],
                    'template_used' => $templateField,
                    'byte3_user' => $b3_user,
                    'byte3_log' => $b3_log,
                    'byte4_user' => $b4_user,
                    'byte4_log' => $b4_log,
                    'byte_similarity' => round($byte_similarity, 2),
                    'pattern_similarity' => round($pattern_similarity, 2),
                    'template_content' => $userTemplate
                ];
            }
        }
    }

    usort($results, fn($a, $b) => $b['byte_similarity'] <=> $a['byte_similarity']);
    echo json_encode($results);
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Multi-Template Fingerprint Matching</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
        th { background: #f2f2f2; }
        code { background-color: #eee; padding: 2px 4px; border-radius: 4px; }
        #top-template-box {
            background: #f9f9f9;
            padding: 10px;
            margin: 10px 0;
            border-left: 4px solid #4CAF50;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <h2>Fingerprint Matching (Bandingkan ke Semua Template, Tampilkan Byte 3 & 4)</h2>

    <div id="top-template-box">
        <strong>Top Matching Template:</strong>
        <div id="top-template-content">Loading...</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>User ID</th><th>Name</th><th>Log ID</th>
                <th>Template Used</th>
                <th>Byte3 User</th><th>Byte3 Log</th>
                <th>Byte4 User</th><th>Byte4 Log</th>
                <th>Byte Similarity</th><th>Pattern Similarity</th>
                <th>Template Content</th>
            </tr>
        </thead>
        <tbody id="match-results">
            <tr><td colspan="11">Loading...</td></tr>
        </tbody>
    </table>

    <script>
        function loadResults() {
            fetch("?ajax=1")
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById("match-results");
                    const topTemplateContent = document.getElementById("top-template-content");
                    tbody.innerHTML = "";

                    if (data.length === 0) {
                        tbody.innerHTML = "<tr><td colspan='11'>No match found</td></tr>";
                        topTemplateContent.innerHTML = "<em>No match found</em>";
                        return;
                    }

                    // Tampilkan template content dari hasil terbaik
                    topTemplateContent.innerHTML = `<code>${data[0].template_content}</code>`;

                    data.forEach(item => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${item.user_id}</td>
                            <td>${item.user_name}</td>
                            <td>${item.log_id}</td>
                            <td>${item.template_used}</td>
                            <td>${item.byte3_user}</td>
                            <td>${item.byte3_log}</td>
                            <td>${item.byte4_user}</td>
                            <td>${item.byte4_log}</td>
                            <td>${item.byte_similarity}%</td>
                            <td>${item.pattern_similarity}%</td>
                            <td><code>${item.template_content}</code></td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => {
                    document.getElementById("match-results").innerHTML =
                        "<tr><td colspan='11'>Failed to load data</td></tr>";
                    console.error("AJAX Error:", error);
                });
        }

        loadResults();
        setInterval(loadResults, 5000);
    </script>
</body>
</html>
