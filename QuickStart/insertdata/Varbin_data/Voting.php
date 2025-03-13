<?php
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json');

    $conn = new mysqli("localhost", "root", "", "aiot");
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["error" => "Database connection error"]);
        exit;
    }

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

    $users = [];
    $logs = [];

    $u = $conn->query("SELECT id, Nama, Template1, Template2, Template3 FROM user");
    while ($row = $u->fetch_assoc()) $users[] = $row;

    $l = $conn->query("SELECT id, Template1 FROM packet_log");
    while ($row = $l->fetch_assoc()) $logs[] = $row;

    $finalResults = [];

    foreach ($logs as $log) {
        $candidates = [];

        foreach ($users as $user) {
            foreach (['Template1', 'Template2', 'Template3'] as $templateField) {
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fingerprint Matching - Voting Relatif</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: center; }
        th { background: #f2f2f2; }
        .highlight { background-color: #d4f4dd; }
    </style>
</head>
<body>
    <h2>Fingerprint Matching (Voting Relatif per Log)</h2>
    <table>
        <thead>
            <tr>
                <th>Skor</th>
                <th>User ID</th><th>Nama</th><th>Log ID</th><th>Template</th>
                <th>Byte3 User</th><th>Byte3 Log</th>
                <th>Byte4 User</th><th>Byte4 Log</th>
                <th>Byte3 Dist</th><th>Byte4 Dist</th>
                <th>Byte Similarity</th><th>Pattern Similarity</th>
                <th>Avg Distance</th>
            </tr>
        </thead>
        <tbody id="match-results">
            <tr><td colspan="14">Loading...</td></tr>
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

        loadResults();
        setInterval(loadResults, 5000);
    </script>
</body>
</html>
