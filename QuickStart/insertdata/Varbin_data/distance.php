<?php
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json');

    $conn = new mysqli("localhost", "root", "", "aiot");
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(["error" => "Database connection error"]);
        exit;
    }

    function byteDistanceSimilarity($hex1, $hex2, $byteIndex = 2) {
        $offset = $byteIndex * 2;
        if (strlen($hex1) < $offset + 2 || strlen($hex2) < $offset + 2) return 0;

        $byte1 = substr($hex1, $offset, 2);
        $byte2 = substr($hex2, $offset, 2);

        $dec1 = hexdec($byte1);
        $dec2 = hexdec($byte2);

        $distance = abs($dec1 - $dec2);
        return 100 - (($distance / 255) * 100);
    }

    function detectByteType($hex, $byteIndex = 2) {
        $offset = $byteIndex * 2;
        $byte = substr($hex, $offset, 2);
        if (ctype_digit($byte)) return "angka";
        elseif (ctype_alpha($byte)) return "huruf-huruf";
        elseif (ctype_alnum($byte)) return "campuran";
        else return "non-print";
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
            $similarity = byteDistanceSimilarity($user['Template1'], $log['Template1'], 2);

            // Ambil byte ke-3 dan ke-4
            $byte3_user = strtoupper(substr($user['Template1'], 4, 2));
            $byte3_log = strtoupper(substr($log['Template1'], 4, 2));
            $byte4_user = strtoupper(substr($user['Template1'], 6, 2));
            $byte4_log = strtoupper(substr($log['Template1'], 6, 2));

            // Tipe byte ke-3
            $userByteType = detectByteType($user['Template1'], 2);
            $logByteType = detectByteType($log['Template1'], 2);

            // Penalti jika tipe tidak sama
            if ($userByteType !== $logByteType) {
                $similarity -= 20;
                if ($similarity < 0) $similarity = 0;
            }

            // Bonus jika byte ke-3 dekat
            $selisih = abs(hexdec($byte3_user) - hexdec($byte3_log));
            $bonus = max(0, 10 - ($selisih / 2)); // Bonus maksimal 10
            $similarity += $bonus;
            if ($similarity > 100) $similarity = 100;

            if ($similarity >= 60) {
                $results[] = [
                    'user_id' => $user['id'],
                    'user_name' => $user['Nama'],
                    'log_id' => $log['id'],
                    'similarity' => round($similarity, 2),
                    'byte3_user' => $byte3_user,
                    'byte3_log' => $byte3_log,
                    'byte4_user' => $byte4_user,
                    'byte4_log' => $byte4_log,
                    'byte_type_user' => $userByteType,
                    'byte_type_log' => $logByteType,
                    'selisih_byte3' => $selisih,
                    'bonus' => round($bonus, 2)
                ];
            }
        }
    }

    usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
    echo json_encode($results);
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fingerprint Byte-Based Matching</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Fingerprint Matching (Byte ke-3 dan ke-4, Threshold ≥ 60%)</h2>
    <table>
        <thead>
            <tr>
                <th>User ID</th><th>Name</th><th>Log ID</th><th>Similarity</th>
                <th>Byte3 (User)</th><th>Byte3 (Log)</th>
                <th>Byte4 (User)</th><th>Byte4 (Log)</th>
                <th>Tipe Byte3 (User)</th><th>Tipe Byte3 (Log)</th>
                <th>Selisih Byte3</th><th>Bonus %</th>
            </tr>
        </thead>
        <tbody id="match-results">
            <tr><td colspan="12">Loading...</td></tr>
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
                        tbody.innerHTML = "<tr><td colspan='12'>No match found</td></tr>";
                        return;
                    }

                    data.forEach(item => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${item.user_id}</td>
                            <td>${item.user_name}</td>
                            <td>${item.log_id}</td>
                            <td>${item.similarity}%</td>
                            <td>${item.byte3_user}</td>
                            <td>${item.byte3_log}</td>
                            <td>${item.byte4_user}</td>
                            <td>${item.byte4_log}</td>
                            <td>${item.byte_type_user}</td>
                            <td>${item.byte_type_log}</td>
                            <td>${item.selisih_byte3}</td>
                            <td>+${item.bonus}%</td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => {
                    document.getElementById("match-results").innerHTML =
                        "<tr><td colspan='12'>Failed to load data</td></tr>";
                    console.error("AJAX Error:", error);
                });
        }

        loadResults();
        setInterval(loadResults, 5000);
    </script>
</body>
</html>
