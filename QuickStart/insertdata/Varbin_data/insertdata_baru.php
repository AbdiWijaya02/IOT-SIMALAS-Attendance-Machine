
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$log_file = __DIR__ . '/performance_log.json';

// 🔄 RESET LOG
if (isset($_GET['reset']) && $_GET['reset'] == '1') {
    file_put_contents($log_file, json_encode([], JSON_PRETTY_PRINT));
    echo "<h3 style='color:green;'>✅ Log berhasil direset.</h3>";
    exit;
}

// ⏱️ Waktu mulai
$start_time = microtime(true);

// 🔗 Koneksi Database
$conn = new mysqli("localhost", "root", "", "aiot");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// 📦 Konfigurasi paket
$packet_names = ['packet2_hex','packet3_hex','packet4_hex','packet5_hex','packet6_hex','packet7_hex'];
$expected_length_per_packet = 128 * 2; // 128 byte × 2 hex
$total_expected = $expected_length_per_packet * count($packet_names);
$total_received = 0;
$packet_combined = '';
$received_count = 0;
$process_type = '';
$user_id = 'unknown';

$is_post = ($_SERVER['REQUEST_METHOD'] === 'POST');

// ✅ Jika ada data dikirim via POST (dari ESP32)
if ($is_post && isset($_POST['user_id'])) {
    $user_id = htmlspecialchars(trim($_POST['user_id']));

    // 🔄 Hitung panjang data aktual yang diterima
    foreach ($packet_names as $key) {
        if (isset($_POST[$key])) {
            $len = strlen($_POST[$key]);
            $total_received += $len;
            if ($len > 0) $received_count++;
            $packet_combined .= $_POST[$key];
        }
    }

    // 📉 Hitung packet loss
    $packet_loss_percent = 0;
    if ($total_expected > 0) {
        $packet_loss_percent = (($total_expected - $total_received) / $total_expected) * 100;
    }

    // 💾 Proses database
    if ($total_received > 0 && $user_id !== 'unknown') {

        $check = $conn->prepare("SELECT id, Template1, Template2, Template3, Template4, Template5, Template6 FROM packet_log WHERE user_id = ?");
        $check->bind_param("s", $user_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = $row['id'];

            if ($user_id === "999") {
                $process_type = 'Verifikasi';
                $update = $conn->prepare("UPDATE packet_log SET Template1 = ? WHERE id = ?");
                $update->bind_param("si", $packet_combined, $id);
                $update->execute();
                echo $update->affected_rows > 0 ? "✅ Template1 user 999 berhasil diperbarui.<br>" : "⚠️ Error update user 999.<br>";
                $update->close();
            } else {
                $process_type = 'Pendaftaran Sidik Jari';
                $target_column = '';
                foreach (['Template1','Template2','Template3','Template4','Template5','Template6'] as $col) {
                    if (empty($row[$col])) {
                        $target_column = $col;
                        break;
                    }
                }
                if ($target_column) {
                    $sql = "UPDATE packet_log SET $target_column = ? WHERE id = ?";
                    $update = $conn->prepare($sql);
                    $update->bind_param("si", $packet_combined, $id);
                    $update->execute();
                    echo "✅ Template baru ditambahkan ke kolom $target_column.<br>";
                    $update->close();
                } else {
                    echo "⚠️ Semua kolom template penuh.<br>";
                }
            }
        } else {
            $process_type = 'Pendaftaran Sidik Jari Baru';
            $insert = $conn->prepare("INSERT INTO packet_log (user_id, Template1) VALUES (?, ?)");
            $insert->bind_param("ss", $user_id, $packet_combined);
            $insert->execute();
            echo $insert->affected_rows > 0 ? "✅ User baru ditambahkan.<br>" : "⚠️ Error insert.<br>";
            $insert->close();
        }

        $check->close();
    } else {
        $process_type = 'Invalid / Data Tidak Lengkap';
        echo "⚠️ Data tidak lengkap.<br>";
    }

    // 🕒 Hitung waktu dan performa
    $end_time = microtime(true);
    $latency_ms = ($end_time - $start_time) * 1000;
    $speed_kbps = ($total_received > 0 && ($end_time - $start_time) > 0)
        ? ($total_received / ($end_time - $start_time)) / 1024
        : 0;

    // 📊 Ambil log sebelumnya
    $existing_logs = [];
    if (file_exists($log_file)) {
        $existing_logs = json_decode(file_get_contents($log_file), true) ?: [];
    }

    // 📉 Hitung jitter
    $previous_latency = count($existing_logs) > 0 ? end($existing_logs)['latency_ms'] : $latency_ms;
    $jitter_ms = abs($latency_ms - $previous_latency);

    // 📝 Simpan log baru
    $entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user_id' => $user_id,
        'process' => $process_type,
        'latency_ms' => round($latency_ms, 2),
        'jitter_ms' => round($jitter_ms, 2),
        'packet_expected' => count($packet_names),
        'packet_received' => $received_count,
        'expected_chars' => $total_expected,
        'received_chars' => $total_received,
        'packet_loss_percent' => round($packet_loss_percent, 2),
        'speed_kbps' => round($speed_kbps, 2)
    ];

    $existing_logs[] = $entry;
    file_put_contents($log_file, json_encode($existing_logs, JSON_PRETTY_PRINT));
}

// ✅ Tampilkan tabel log
$logs = [];
if (file_exists($log_file)) {
    $logs = json_decode(file_get_contents($log_file), true) ?: [];
}

echo "<h2>📊 Performance Log</h2>";
echo "<a href='?reset=1' style='color:red;'>Reset Log</a><br><br>";

if (empty($logs)) {
    echo "<p>Tidak ada data log.</p>";
} else {
    echo "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse: collapse;'>
    <tr style='background:#ddd;'>
        <th>No</th>
        <th>Timestamp</th>
        <th>User ID</th>
        <th>Process</th>
        <th>Latency (ms)</th>
        <th>Jitter (ms)</th>
        <th>Packet Expected</th>
        <th>Packet Received</th>
        <th>Expected Chars</th>
        <th>Received Chars</th>
        <th>Packet Loss (%)</th>
        <th>Speed (KB/s)</th>
    </tr>";
    $i = 1;
    foreach ($logs as $row) {
        // 🎨 Tentukan warna
        $latency_color = ($row['latency_ms'] < 1000) ? 'green' : (($row['latency_ms'] < 3000) ? 'orange' : 'red');
        $jitter_color = ($row['jitter_ms'] < 200) ? 'green' : (($row['jitter_ms'] < 500) ? 'orange' : 'red');
        $loss_color = ($row['packet_loss_percent'] < 5) ? 'green' : (($row['packet_loss_percent'] < 20) ? 'orange' : 'red');
        $speed_color = ($row['speed_kbps'] > 100) ? 'green' : (($row['speed_kbps'] > 50) ? 'orange' : 'red');

        echo "<tr>
            <td>{$i}</td>
            <td>{$row['timestamp']}</td>
            <td>{$row['user_id']}</td>
            <td>{$row['process']}</td>
            <td style='color:$latency_color'>{$row['latency_ms']}</td>
            <td style='color:$jitter_color'>{$row['jitter_ms']}</td>
            <td>{$row['packet_expected']}</td>
            <td>{$row['packet_received']}</td>
            <td>{$row['expected_chars']}</td>
            <td>{$row['received_chars']}</td>
            <td style='color:$loss_color'>{$row['packet_loss_percent']}</td>
            <td style='color:$speed_color'>{$row['speed_kbps']}</td>
        </tr>";
        $i++;
    }
    echo "</table>";
}

$conn->close();
?>
