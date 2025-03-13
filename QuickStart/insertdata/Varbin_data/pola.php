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
            $byte1 = substr($hex1, $i * 2, 2);
            $byte2 = substr($hex2, $i * 2, 2);

            if (byteType($byte1) === byteType($byte2)) {
                $match++;
            }
        }
        return ($match / $byteCount) * 100;
    }

    $users = [];
    $logs = [];

    $u = $conn->query("SELECT id, Nama, Template1 FROM user");
    while ($row = $u->fetch_assoc()) $users[] = $row;

    $l = $conn->query("SELECT id, Template1 FROM packet_log");
    while ($row = $l->fetch_assoc()) $logs[] = $row;

    $results = [];
    foreach ($users as $user) {
        foreach ($logs as $log) {
            $patternSimilarity = patternTypeSimilarity($user['Template1'], $log['Template1']);

            if ($patternSimilarity >= 60) {
                $results[] = [
                    'user_id' => $user['id'],
                    'user_name' => $user['Nama'],
                    'log_id' => $log['id'],
                    'pattern_similarity' => round($patternSimilarity, 2)
                ];
            }
        }
    }

    usort($results, fn($a, $b) => $b['pattern_similarity'] <=> $a['pattern_similarity']);
    echo json_encode($results);
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pattern Type Matching</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Fingerprint Pattern Type Matching (All Byte Pattern, Threshold ≥ 60%)</h2>
    <table>
        <thead>
            <tr><th>User ID</th><th>Name</th><th>Log ID</th><th>Pattern Similarity</th></tr>
        </thead>
        <tbody id="match-results">
            <tr><td colspan="4">Loading...</td></tr>
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
                        tbody.innerHTML = "<tr><td colspan='4'>No match found</td></tr>";
                        return;
                    }

                    data.forEach(item => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${item.user_id}</td>
                            <td>${item.user_name}</td>
                            <td>${item.log_id}</td>
                            <td>${item.pattern_similarity}%</td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => {
                    document.getElementById("match-results").innerHTML =
                        "<tr><td colspan='4'>Failed to load data</td></tr>";
                    console.error("AJAX Error:", error);
                });
        }

        loadResults();
        setInterval(loadResults, 5000);
    </script>
</body>
</html>
