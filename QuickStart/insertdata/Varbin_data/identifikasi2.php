<?php
// 1. KONEKSI DATABASE DENGAN ERROR HANDLING
$conn = new mysqli("localhost", "root", "", "aiot");
if ($conn->connect_error) {
    error_log("Database error: " . $conn->connect_error);
    die("System maintenance. Please try later.");
}

// 2. FUNGSI UTILITAS UNTUK PROSES TEMPLATE
function normalizeHex($hex, $length = 40) {
    // Bersihkan karakter non-HEX
    $hex = preg_replace('/[^0-9a-fA-F]/', '', $hex);
    
    // Normalisasi panjang
    $currentLength = strlen($hex);
    if ($currentLength < $length) {
        // Tambahkan padding dengan '0' di akhir
        $hex = str_pad($hex, $length, '0', STR_PAD_RIGHT);
    } elseif ($currentLength > $length) {
        // Potong jika terlalu panjang
        $hex = substr($hex, 0, $length);
    }
    return $hex;
}

function extractTemplateCore($hex) {
    $length = strlen($hex);
    if ($length < 20) return $hex; // Jika terlalu pendek, return as-is
    
    // Ambil 20 karakter di sekitar titik tengah
    $start = max(0, floor($length / 2) - 10);
    return substr($hex, $start, 20);
}

function hammingDistance($hex1, $hex2) {
    // Pastikan panjang sama
    $length = max(strlen($hex1), strlen($hex2));
    $hex1 = str_pad($hex1, $length, '0', STR_PAD_RIGHT);
    $hex2 = str_pad($hex2, $length, '0', STR_PAD_RIGHT);
    
    $bin1 = @hex2bin($hex1);
    $bin2 = @hex2bin($hex2);
    if ($bin1 === false || $bin2 === false) return 0;
    
    $len = strlen($bin1);
    $distance = 0;
    
    for ($i = 0; $i < $len; $i++) {
        $xor = ord($bin1[$i]) ^ ord($bin2[$i]);
        while ($xor > 0) {
            $distance += $xor & 1;
            $xor >>= 1;
        }
    }
    
    $totalBits = $len * 8;
    return $totalBits > 0 ? (1 - ($distance / $totalBits)) * 100 : 0;
}

// 3. AMBIL DATA DARI DATABASE
$users = [];
$userQuery = $conn->query("SELECT id, Nama, Template2 FROM user");
if ($userQuery) {
    while ($row = $userQuery->fetch_assoc()) {
        $users[] = $row;
    }
}

// Perbaikan: Hanya ambil kolom yang ada di database
$logs = [];
$logQuery = $conn->query("SELECT id, Template1 FROM packet_log");
if ($logQuery) {
    while ($row = $logQuery->fetch_assoc()) {
        $logs[] = $row;
    }
}

// 4. PREPROCESSING DATA DAN PERBANDINGAN
$results = [];
$threshold = 60; // Minimum similarity threshold

foreach ($users as $user) {
    // Skip jika template kosong
    if (empty($user['Template2'])) continue;
    
    $normalizedUser = normalizeHex($user['Template2']);
    $userCore = extractTemplateCore($normalizedUser);
    
    foreach ($logs as $log) {
        // Skip jika template kosong
        if (empty($log['Template1'])) continue;
        
        $normalizedLog = normalizeHex($log['Template1']);
        $logCore = extractTemplateCore($normalizedLog);
        
        $similarity = hammingDistance($userCore, $logCore);
        
        if ($similarity >= $threshold) {
            $results[] = [
                'user_id' => $user['id'],
                'user_name' => $user['Nama'],
                'log_id' => $log['id'],
                'similarity' => round($similarity, 2),
                'log_time' => 'N/A' // Tidak tersedia di database
            ];
        }
    }
}

// 5. SORTING BERDASARKAN SKOR TERTIMBANG
usort($results, function($a, $b) {
    return $b['similarity'] <=> $a['similarity'];
});

// 6. PREP DATA UNTUK OUTPUT
$resultsCount = count($results);
$usersCount = count($users);
$logsCount = count($logs);
$executionTime = round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3);

// 7. OUTPUT HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Advanced Fingerprint Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .match-high { background-color: #d4edda !important; }
        .match-medium { background-color: #fff3cd !important; }
        .match-low { background-color: #f8d7da !important; }
        .table-hover tbody tr:hover { background-color: rgba(0,0,0,.05); }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="h4 mb-0">Fingerprint Matching System</h1>
                <p class="mb-0">Threshold: <?= $threshold ?>% | Matches found: <?= $resultsCount ?></p>
            </div>
            
            <div class="card-body">
                <?php if ($resultsCount > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Log ID</th>
                                <th>Similarity</th>
                                <th>Scan Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $row): 
                                $rowClass = '';
                                if ($row['similarity'] >= 85) $rowClass = 'match-high';
                                elseif ($row['similarity'] >= 70) $rowClass = 'match-medium';
                                else $rowClass = 'match-low';
                            ?>
                            <tr class="<?= $rowClass ?>">
                                <td><?= htmlspecialchars($row['user_id']) ?></td>
                                <td><?= htmlspecialchars($row['user_name']) ?></td>
                                <td><?= htmlspecialchars($row['log_id']) ?></td>
                                <td><?= round($row['similarity'], 2) ?>%</td>
                                <td><?= htmlspecialchars($row['log_time']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    No matches found with the current threshold (<?= $threshold ?>%).
                    Please try lowering the threshold or add more data.
                </div>
                <?php endif; ?>
            </div>
            
            <div class="card-footer text-muted">
                <small>
                    System processed <?= $usersCount ?> users and <?= $logsCount ?> scans.
                    Execution time: <?= $executionTime ?> seconds.
                    <?php if ($logsCount > 50): ?>
                    <span class="text-danger">Warning: Large dataset may affect performance</span>
                    <?php endif; ?>
                </small>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>