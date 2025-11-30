<?php
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

function xorSimilarity($hex1, $hex2)
{
    $arr1 = str_split($hex1, 2);
    $arr2 = str_split($hex2, 2);
    $len = min(count($arr1), count($arr2));
    if ($len == 0) return 0;

    $totalBits = 0;
    $sameBits = 0;

    for ($i = 0; $i < $len; $i++) {
        $b1 = hexdec($arr1[$i]);
        $b2 = hexdec($arr2[$i]);
        $xor = $b1 ^ $b2;
        $diffBits = substr_count(decbin($xor), '1');
        $sameBits += (8 - $diffBits);
        $totalBits += 8;
    }
    return ($sameBits / $totalBits) * 100;
}

function hammingSimilarity($hex1, $hex2)
{
    return xorSimilarity($hex1, $hex2);
}

function manhattanSimilarity($hex1, $hex2)
{
    $arr1 = str_split($hex1, 2);
    $arr2 = str_split($hex2, 2);
    $len = min(count($arr1), count($arr2));
    if ($len == 0) return 0;
    $sum = 0;
    for ($i = 0; $i < $len; $i++) {
        $sum += abs(hexdec($arr1[$i]) - hexdec($arr2[$i]));
    }
    $maxSum = $len * 255;
    $similarity = 100 - (($sum / $maxSum) * 100);
    return max(0, $similarity);
}

function jaccardSimilarity($hex1, $hex2)
{
    $arr1 = str_split($hex1, 2);
    $arr2 = str_split($hex2, 2);
    $intersect = count(array_intersect($arr1, $arr2));
    $union = count(array_unique(array_merge($arr1, $arr2)));
    return $union == 0 ? 0 : ($intersect / $union) * 100;
}

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
                if (!ctype_xdigit($b3_user) || !ctype_xdigit($b3_log) || !ctype_xdigit($b4_user) || !ctype_xdigit($b4_log)) continue;

                $d3 = abs(hexdec($b3_user) - hexdec($b3_log));
                $d4 = abs(hexdec($b4_user) - hexdec($b4_log));
                $avg_distance = ($d3 + $d4) / 2;
                $byte_similarity = 100 - (($avg_distance / 255) * 100);

                // semua metode
                $pattern = patternTypeSimilarity($userTemplate, $logTemplate);
                $jac = jaccardSimilarity($userTemplate, $logTemplate);
                $ham = hammingSimilarity($userTemplate, $logTemplate);
                $xor = xorSimilarity($userTemplate, $logTemplate);
                $man = manhattanSimilarity($userTemplate, $logTemplate);

                // normalisasi akhir
                $norm = ($byte_similarity + $pattern + $jac + $ham + $xor + $man) / 6;

                $candidates[] = [
                    'user_id' => $user['id'],
                    'user_name' => $user['Nama'],
                    'log_id' => $log['id'],
                    'template_used' => $templateField,
                    'len_user' => strlen($userTemplate),
                    'len_log' => strlen($logTemplate),
                    'first_20_user' => substr($userTemplate, 0, 40) . '...',
                    'first_20_log' => substr($logTemplate, 0, 40) . '...',
                    'b3_user' => $b3_user,
                    'b3_log' => $b3_log,
                    'b4_user' => $b4_user,
                    'b4_log' => $b4_log,
                    'b3_distance' => $d3,
                    'b4_distance' => $d4,
                    'avg_distance' => round($avg_distance, 2),
                    'byte_similarity' => round($byte_similarity, 2),
                    'pattern_similarity' => round($pattern, 2),
                    'jaccard' => round($jac, 2),
                    'hamming' => round($ham, 2),
                    'xor' => round($xor, 2),
                    'manhattan' => round($man, 2),
                    'normalized_score' => round($norm, 2),
                    'score' => 0
                ];
            }
        }

        foreach (['byte_similarity', 'pattern_similarity', 'jaccard', 'hamming', 'xor', 'manhattan', 'normalized_score'] as $crit) {
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
    <title>Fingerprint Matching - Detail Perbandingan</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { padding: 6px; border: 1px solid #aaa; text-align: center; font-size: 12px; }
        th { background: #f4f4f4; }
        .highlight { background: #d1ffd1; }
    </style>
</head>
<body>
<h2>Fingerprint Matching (Dengan Detail Perbandingan)</h2>
<div id="status">Mengambil data...</div>
<table>
    <thead>
        <tr>
            <th>Score</th><th>User</th><th>Nama</th><th>Log</th><th>Template</th>
            <th>LenU</th><th>LenL</th>
            <th>Data User (awal)</th><th>Data Log (awal)</th>
            <th>B3U</th><th>B3L</th><th>B4U</th><th>B4L</th>
            <th>ByteSim</th><th>Pattern</th><th>Jaccard</th><th>Hamming</th><th>XOR</th><th>Manhattan</th><th>Norm</th>
        </tr>
    </thead>
    <tbody id="match-results"><tr><td colspan="20">Loading...</td></tr></tbody>
</table>

<script>
function loadResults() {
    fetch("?ajax=1")
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById("match-results");
            tbody.innerHTML = "";
            if (!data.length) { tbody.innerHTML = "<tr><td colspan='20'>No data</td></tr>"; return; }
            data.forEach((item, i) => {
                const tr = document.createElement("tr");
                if (i === 0) tr.classList.add("highlight");
                tr.innerHTML = `
                    <td>${item.score}</td>
                    <td>${item.user_id}</td>
                    <td>${item.user_name}</td>
                    <td>${item.log_id}</td>
                    <td>${item.template_used}</td>
                    <td>${item.len_user}</td>
                    <td>${item.len_log}</td>
                    <td>${item.first_20_user}</td>
                    <td>${item.first_20_log}</td>
                    <td>${item.b3_user}</td>
                    <td>${item.b3_log}</td>
                    <td>${item.b4_user}</td>
                    <td>${item.b4_log}</td>
                    <td>${item.byte_similarity}%</td>
                    <td>${item.pattern_similarity}%</td>
                    <td>${item.jaccard}%</td>
                    <td>${item.hamming}%</td>
                    <td>${item.xor}%</td>
                    <td>${item.manhattan}%</td>
                    <td><b>${item.normalized_score}%</b></td>
                `;
                tbody.appendChild(tr);
            });
        });
}
loadResults();
setInterval(loadResults, 2000);
</script>
</body>
</html>
