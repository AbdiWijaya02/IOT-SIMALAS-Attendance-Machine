<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
date_default_timezone_set('Asia/Jakarta');

// 🔧 Koneksi Database
$conn = new mysqli("localhost", "root", "", "aiot");
if ($conn->connect_error) {
    die(json_encode(["status" => "❌ Koneksi gagal", "error" => $conn->connect_error]));
}

// 📥 Ambil data dari ESP32
$user_id = $_POST['user_id'] ?? 'unknown';
$latency = isset($_POST['latency_ms']) ? floatval($_POST['latency_ms']) : 0;
$jitter = isset($_POST['jitter_ms']) ? floatval($_POST['jitter_ms']) : 0;
$throughput = isset($_POST['throughput_kbps']) ? floatval($_POST['throughput_kbps']) : 0;

// 🔢 Data untuk packet loss
$total_packets = isset($_POST['total_packets']) ? intval($_POST['total_packets']) : 6;
$received_packets = isset($_POST['received_packets']) ? intval($_POST['received_packets']) : $total_packets;

// 📦 Hitung byte total dan byte hilang
$bytes_per_packet = 128;
$total_bytes = $total_packets * $bytes_per_packet;
$received_bytes = $received_packets * $bytes_per_packet;
$lost_bytes = $total_bytes - $received_bytes;

// 🧮 Hitung packet loss otomatis
$packet_loss = ($total_packets > 0) ? (($total_packets - $received_packets) / $total_packets) * 100 : 0;

$status_code = isset($_POST['status_code']) ? intval($_POST['status_code']) : 0;
$request_url = $_POST['request_url'] ?? '';
$note = $_POST['note'] ?? '';

// 🏷️ Tentukan jenis aktivitas
$activity_type = ($user_id == '999') ? 'Verifikasi' : 'Pendaftaran';

// 🧠 Catatan otomatis tambahan
if ($latency > 500) $note .= " | Latency tinggi ($latency ms)";
if ($packet_loss > 5) $note .= " | Packet loss signifikan (" . round($packet_loss, 2) . "%)";
if ($throughput < 10) $note .= " | Throughput rendah ($throughput kbps)";
if ($lost_bytes > 0) $note .= " | Hilang $lost_bytes byte dari total $total_bytes byte";

// 💾 Simpan ke database
$sql = "INSERT INTO http_log (
    user_id, activity_type, latency_ms, jitter_ms, throughput_kbps,
    packet_loss_percent, total_packets, received_packets, total_bytes, lost_bytes,
    status_code, request_url, note
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssddddiiiisss",
    $user_id,
    $activity_type,
    $latency,
    $jitter,
    $throughput,
    $packet_loss,
    $total_packets,
    $received_packets,
    $total_bytes,
    $lost_bytes,
    $status_code,
    $request_url,
    $note
);

// 🧾 Eksekusi dan respon
if ($stmt->execute()) {
    echo json_encode([
        "status" => "✅ Log berhasil disimpan",
        "data" => [
            "user_id" => $user_id,
            "activity_type" => $activity_type,
            "latency_ms" => $latency,
            "jitter_ms" => $jitter,
            "throughput_kbps" => $throughput,
            "packet_loss_percent" => round($packet_loss, 2),
            "total_packets" => $total_packets,
            "received_packets" => $received_packets,
            "total_bytes" => $total_bytes,
            "lost_bytes" => $lost_bytes,
            "status_code" => $status_code,
            "request_url" => $request_url,
            "note" => $note
        ]
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        "status" => "❌ Gagal menyimpan log",
        "error" => $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>
