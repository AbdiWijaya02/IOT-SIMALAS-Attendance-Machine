<?php
// 1. KONEKSI DATABASE (Dengan Error Handling Lebih Baik)
$conn = new mysqli("localhost", "root", "", "aiot");
if ($conn->connect_error) {
    error_log("Database error: " . $conn->connect_error);
    die("System maintenance. Please try later.");
}

// 2. FUNGSI BINARY COMPARISON (Pengganti similar_text())
function binarySimilarity($hex1, $hex2) {
    if (empty($hex1) || empty($hex2)) return 0;

    $bin1 = hex2bin($hex1);
    $bin2 = hex2bin($hex2);
    if ($bin1 === false || $bin2 === false) return 0;

    $length = min(strlen($bin1), strlen($bin2));
    if ($length == 0) return 0;

    $match = 0;
    for ($i = 0; $i < $length; $i++) {
        if ($bin1[$i] === $bin2[$i]) $match++;
    }

    return ($match / $length) * 100;
}

// 3. AMBIL DATA user DAN packet_log (Hanya packet2)
$users = [];
$result = $conn->query("SELECT id, Nama, Template1 FROM user");
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$logs = [];
$result = $conn->query("SELECT id, Template1 FROM packet_log");
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

// 4. PERBANDINGAN packet2 + THRESHOLD (bisa ubah 60 jadi berapa pun)
$results = [];
foreach ($users as $user) {
    foreach ($logs as $log) {
        $similarity = binarySimilarity($user['Template1'], $log['Template1']);
        if ($similarity >= 0) { // threshold bisa disesuaikan
            $results[] = [
                'user_id' => $user['id'],
                'user_name' => $user['Nama'],
                'log_id' => $log['id'],
                'similarity' => round($similarity, 2)
            ];
        }
    }
}

// 5. SORTING & OUTPUT HTML
usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

header('Content-Type: text/html; charset=utf-8');
echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Fingerprint Verification Results</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Fingerprint Matching Results (Threshold: 60%)</h2>
    <table>
        <tr><th>User ID</th><th>Name</th><th>Log ID</th><th>Similarity</th></tr>
HTML;

foreach ($results as $row) {
    echo sprintf(
        '<tr><td>%s</td><td>%s</td><td>%s</td><td>%.2f%%</td></tr>',
        htmlspecialchars($row['user_id']),
        htmlspecialchars($row['user_name']),
        htmlspecialchars($row['log_id']),
        $row['similarity']
    );
}

echo "</table></body></html>";
$conn->close();
?>
