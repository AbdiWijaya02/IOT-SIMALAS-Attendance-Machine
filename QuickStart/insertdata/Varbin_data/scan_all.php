<?php
include("Voting.php"); // Jika perlu akses fungsi patternTypeSimilarity()

header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "aiot");
if ($conn->connect_error) {
    echo json_encode(["error" => "DB Error"]);
    exit;
}

// --- Fungsi patternTypeSimilarity bisa didefinisikan ulang di sini jika tidak di-include
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

// --- Eksekusi pencocokan seperti sebelumnya
$users = [];
$logs = [];

$u = $conn->query("SELECT id, Nama, NIM, Template1, Template2, Template3, Template4, Template5, Template6 FROM user");
while ($row = $u->fetch_assoc()) $users[] = $row;

$l = $conn->query("SELECT id, Template1 FROM packet_log ORDER BY id DESC");
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

            if (
                !ctype_xdigit($b3_user) || !ctype_xdigit($b3_log) ||
                !ctype_xdigit($b4_user) || !ctype_xdigit($b4_log)
            ) continue;

            $d3 = abs(hexdec($b3_user) - hexdec($b3_log));
            $d4 = abs(hexdec($b4_user) - hexdec($b4_log));
            $avg_distance = ($d3 + $d4) / 2;

            $byte_similarity = 100 - (($avg_distance / 255) * 100);
            $pattern_similarity = patternTypeSimilarity($userTemplate, $logTemplate);

            $candidates[] = [
                'user_id' => $user['id'],
                'user_name' => $user['Nama'],
                'NIM' => $user['NIM'],
                'log_id' => $log['id'],
                'template_used' => $templateField,
                'score' => 0,
                'byte_similarity' => $byte_similarity,
                'pattern_similarity' => $pattern_similarity,
                'b3_distance' => $d3,
                'b4_distance' => $d4
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

echo json_encode($finalResults);
$conn->close();
exit;
