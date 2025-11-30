<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Jakarta');

// 🔧 Koneksi Database
$conn = new mysqli("localhost", "root", "", "aiot");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// 🔍 Ambil filter
$filter_activity = $_GET['activity_type'] ?? '';
$filter_user = $_GET['user_id'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$export = $_GET['export'] ?? '';

// 🧾 Filter Query
$where = " WHERE 1=1 ";
if (!empty($filter_activity)) $where .= " AND activity_type = '" . $conn->real_escape_string($filter_activity) . "'";
if (!empty($filter_user)) $where .= " AND user_id = '" . $conn->real_escape_string($filter_user) . "'";
if (!empty($start_date) && !empty($end_date)) $where .= " AND DATE(created_at) BETWEEN '" . $conn->real_escape_string($start_date) . "' AND '" . $conn->real_escape_string($end_date) . "'";

$sql = "SELECT * FROM http_log $where ORDER BY id DESC";
$result = $conn->query($sql);

// 📊 Statistik
$stat_sql = "SELECT COUNT(*) AS total_log,
    AVG(latency_ms) AS avg_latency,
    AVG(jitter_ms) AS avg_jitter,
    AVG(throughput_kbps) AS avg_throughput,
    AVG(packet_loss_percent) AS avg_packet_loss,
    SUM(total_bytes) AS total_sent_bytes,
    SUM(lost_bytes) AS total_lost_bytes
FROM http_log $where";
$stat = $conn->query($stat_sql)->fetch_assoc();
$total_sent_bytes = $stat['total_sent_bytes'] ?? 0;
$total_lost_bytes = $stat['total_lost_bytes'] ?? 0;
$lost_bytes_percent = ($total_sent_bytes > 0) ? ($total_lost_bytes / $total_sent_bytes * 100) : 0;

// 📤 Export Excel
if ($export === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=esp32_log_export_" . date('Ymd_His') . ".csv");

    // Header kolom file
    echo "User ID\tAktivitas\tLatency (ms)\tJitter (ms)\tThroughput (kbps)\tPacket Loss (%)\tTotal Bytes\tLost Bytes\tCatatan\tWaktu\n";

    if ($result && $result->num_rows > 0) {
        mysqli_data_seek($result, 0);
        while ($r = $result->fetch_assoc()) {
            // Format waktu agar sampai ke detik
            $createdAt = date("Y-m-d H:i:s", strtotime($r['created_at']));

            // Jika kamu ingin sampai milidetik (kalau data di DB mendukung):
            // $createdAt = date("Y-m-d H:i:s.u", strtotime($r['created_at']));

            echo "{$r['user_id']}\t{$r['activity_type']}\t{$r['latency_ms']}\t{$r['jitter_ms']}\t{$r['throughput_kbps']}\t{$r['packet_loss_percent']}\t{$r['total_bytes']}\t{$r['lost_bytes']}\t{$r['note']}\t{$createdAt}\n";
        }
    }
    exit;
}


// 🧮 Fungsi Kategori Warna
function cls($kategori) {
    switch ($kategori) {
        case "Sangat Baik": return "text-success fw-bold";
        case "Baik": return "text-primary fw-bold";
        case "Cukup": return "text-warning fw-bold";
        case "Buruk": return "text-danger fw-bold";
        default: return "";
    }
}
function kategori_throughput($val) {
    if ($val > 2100) return "Sangat Baik";
    if ($val >= 1200 && $val <= 2100) return "Baik";
    if ($val >= 700 && $val < 1200) return "Cukup";
    return "Buruk";
}
function kategori_packetloss($val) {
    if ($val >= 0 && $val <= 2) return "Sangat Baik";
    if ($val >= 3 && $val <= 14) return "Baik";
    if ($val >= 15 && $val <= 24) return "Cukup";
    return "Buruk";
}
function kategori_delay($val) {
    if ($val < 150) return "Sangat Baik";
    if ($val >= 150 && $val < 300) return "Baik";
    if ($val >= 300 && $val < 450) return "Cukup";
    return "Buruk";
}
function kategori_jitter($val) {
    if ($val == 0) return "Sangat Baik";
    if ($val > 0 && $val <= 75) return "Baik";
    if ($val > 75 && $val <= 125) return "Cukup";
    return "Buruk";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>📊 Monitoring Log ESP32</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color:#f8fafc; }
h1 { text-align:center; margin-bottom:25px; color:#0d6efd; }
.table thead th { background-color:#0d6efd; color:white; text-align:center; }
.table tbody td { vertical-align:middle; text-align:center; }
.summary { background:white; padding:15px; border-radius:10px; box-shadow:0 0 6px rgba(0,0,0,0.1); margin-bottom:20px; }
.filter-bar { background:white; padding:15px; border-radius:10px; margin-bottom:20px; box-shadow:0 0 6px rgba(0,0,0,0.1); }
</style>
</head>
<body>
<div class="container">
    <h1>📊 Monitoring Log ESP32</h1>

    <!-- 🔍 Filter -->
    <form class="filter-bar" method="GET">
        <div class="row g-3 align-items-center">
            <div class="col-md-3">
                <label class="form-label">Aktivitas:</label>
                <select name="activity_type" class="form-select">
                    <option value="">Semua</option>
                    <option value="Pendaftaran" <?= ($filter_activity=='Pendaftaran'?'selected':'') ?>>Pendaftaran</option>
                    <option value="Verifikasi" <?= ($filter_activity=='Verifikasi'?'selected':'') ?>>Verifikasi</option>
                    <option value="TopMatch" <?= ($filter_activity=='TopMatch'?'selected':'') ?>>TopMatch</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">User ID:</label>
                <input type="text" name="user_id" class="form-control" value="<?= htmlspecialchars($filter_user) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Dari:</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai:</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
            </div>
            <div class="col-md-1 d-grid">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary">Terapkan</button>
            </div>
        </div>
        <div class="mt-3 d-flex justify-content-between">
            <a href="view_log.php" class="btn btn-outline-secondary">Reset</a>
            <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'excel'])) ?>" class="btn btn-success">⬇️ Export Excel</a>
        </div>
    </form>

    <!-- 📈 Ringkasan Statistik -->
    <div class="summary">
        <strong>Total Log:</strong> <?= $stat['total_log'] ?? 0 ?> |
        <strong>Rata-rata Latency:</strong> <?= number_format($stat['avg_latency'],2) ?> ms |
        <strong>Rata-rata Jitter:</strong> <?= number_format($stat['avg_jitter'],2) ?> ms |
        <strong>Rata-rata Throughput:</strong> <?= number_format($stat['avg_throughput'],2) ?> kbps |
        <strong>Rata-rata Packet Loss:</strong> <?= number_format($stat['avg_packet_loss'],2) ?> % |
        <strong>Bytes Hilang:</strong> <?= number_format($lost_bytes_percent,2) ?> %
    </div>

    <!-- 📋 Tabel Data -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Aktivitas</th>
                    <th>Latency (ms)</th>
                    <th>Jitter (ms)</th>
                    <th>Throughput (kbps)</th>
                    <th>Packet Loss (%)</th>
                    <th>Catatan</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($r = $result->fetch_assoc()): 
                    $kDelay = kategori_delay($r['latency_ms']);
                    $kJitter = kategori_jitter($r['jitter_ms']);
                    $kTh = kategori_throughput($r['throughput_kbps']);
                    $kPL = kategori_packetloss($r['packet_loss_percent']);
                ?>
                <tr>
                    <td><?= htmlspecialchars($r['user_id']) ?></td>
                    <td><?= htmlspecialchars($r['activity_type']) ?></td>
                    <td class="<?= cls($kDelay) ?>"><?= number_format($r['latency_ms'],2) ?><br><small>(<?= $kDelay ?>)</small></td>
                    <td class="<?= cls($kJitter) ?>"><?= number_format($r['jitter_ms'],2) ?><br><small>(<?= $kJitter ?>)</small></td>
                    <td class="<?= cls($kTh) ?>"><?= number_format($r['throughput_kbps'],2) ?><br><small>(<?= $kTh ?>)</small></td>
                    <td class="<?= cls($kPL) ?>"><?= number_format($r['packet_loss_percent'],2) ?><br><small>(<?= $kPL ?>)</small></td>
                    <td><?= htmlspecialchars($r['note']) ?></td>
                    <td><?= $r['created_at'] ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">Tidak ada data log sesuai filter.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
