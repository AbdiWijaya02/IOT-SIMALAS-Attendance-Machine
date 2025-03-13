<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "fingerprint_db";

function hexStringToArray($str) {
    $bytes = explode(',', $str);
    return array_map(function($b) {
        return intval($b, 0);
    }, $bytes);
}

function cosineSimilarity($a, $b) {
    $dot = 0; $normA = 0; $normB = 0;
    for ($i = 0; $i < count($a); $i++) {
        $dot += $a[$i] * $b[$i];
        $normA += $a[$i] * $a[$i];
        $normB += $b[$i] * $b[$i];
    }
    return $dot / (sqrt($normA) * sqrt($normB) + 1e-10);
}

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    die("Koneksi gagal: " . $conn->connect_error);
}

// Validasi input
$required = ['packet2','packet3','packet4','packet5','packet6','packet7'];
foreach ($required as $key) {
    if (!isset($_POST[$key])) {
        http_response_code(400);
        die("Error: parameter '$key' tidak ditemukan");
    }
}

// Gabungkan template input dari ESP32
$input = array_merge(
    hexStringToArray($_POST['packet2']),
    hexStringToArray($_POST['packet3']),
    hexStringToArray($_POST['packet4']),
    hexStringToArray($_POST['packet5']),
    hexStringToArray($_POST['packet6']),
    hexStringToArray($_POST['packet7'])
);

// Ambil semua template dari database
$sql = "SELECT * FROM templates";
$result = $conn->query($sql);
if (!$result) {
    http_response_code(500);
    die("Query gagal: " . $conn->error);
}

$similarities = [];

while ($row = $result->fetch_assoc()) {
    $db_template = array_merge(
        hexStringToArray($row['packet2']),
        hexStringToArray($row['packet3']),
        hexStringToArray($row['packet4']),
        hexStringToArray($row['packet5']),
        hexStringToArray($row['packet6']),
        hexStringToArray($row['packet7'])
    );

    if (count($db_template) !== count($input)) continue; // Jaga agar panjang sama

    $score = cosineSimilarity($input, $db_template);

    $similarities[] = [
        'id' => $row['user_id'],
        'packet2' => $row['packet2'],
        'packet3' => $row['packet3'],
        'packet4' => $row['packet4'],
        'packet5' => $row['packet5'],
        'packet6' => $row['packet6'],
        'packet7' => $row['packet7'],
        'similarity' => $score
    ];
}

// Urutkan berdasarkan similarity tertinggi
usort($similarities, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

// Ambil 3 teratas
$response = array_slice($similarities, 0, 3);

// Output JSON
header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>
