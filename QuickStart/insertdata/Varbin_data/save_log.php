<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
date_default_timezone_set('Asia/Jakarta');

// 🔌 Koneksi Database
$conn = new mysqli("localhost", "root", "", "aiot");
if ($conn->connect_error) {
    die(json_encode(["status" => "❌ Koneksi gagal", "error" => $conn->connect_error]));
}

// 📥 Ambil data dari ESP32
$user_id        = $_POST['user_id'] ?? 'unknown';
$activity_type  = $_POST['request_url'] ?? '';
$latency        = isset($_POST['latency_ms']) ? floatval($_POST['latency_ms']) : 0;
$jitter         = isset($_POST['jitter_ms']) ? floatval($_POST['jitter_ms']) : 0;
$throughput     = isset($_POST['throughput_kbps']) ? floatval($_POST['throughput_kbps']) : 0;
$total_packets  = isset($_POST['total_packets']) ? intval($_POST['total_packets']) : 6;
$received_packets = isset($_POST['received_packets']) ? intval($_POST['received_packets']) : $total_packets;

$bytes_per_packet = 128;
$total_bytes = $total_packets * $bytes_per_packet;
if (isset($_POST['total_bytes'])) {
    $total_bytes = intval($_POST['total_bytes']);
}
$received_bytes = $received_packets * $bytes_per_packet;
$lost_bytes = isset($_POST['lost_bytes']) ? intval($_POST['lost_bytes']) : ($total_bytes - $received_bytes);

// 🧮 Hitung packet loss otomatis
$packet_loss = ($total_bytes > 0) ? (($lost_bytes / $total_bytes) * 100) : 0;

// 📋 Data tambahan
$status_code = isset($_POST['status_code']) ? intval($_POST['status_code']) : 0;
$request_url = $_POST['request_url'] ?? '';
$note        = $_POST['note'] ?? '';

// =============================
// 🧠 Fungsi Penilaian QoS
// =============================

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

// =============================
// 🔍 Klasifikasi Berdasarkan Nilai
// =============================
$kategori_throughput = kategori_throughput($throughput);
$kategori_packetloss = kategori_packetloss($packet_loss);
$kategori_delay      = kategori_delay($latency);
$kategori_jitter     = kategori_jitter($jitter);

// =============================
// 🧠 Catatan otomatis
// =============================
if ($latency > 450) $note .= " | Delay sangat tinggi ($latency ms)";
if ($packet_loss > 25) $note .= " | Packet loss parah (" . round($packet_loss, 2) . "%)";
if ($throughput < 700) $note .= " | Throughput rendah ($throughput kbps)";
if ($lost_bytes > 0) $note .= " | Hilang $lost_bytes byte dari total $total_bytes byte";

// =============================
// 💾 Simpan ke database
// =============================

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

// =============================
// 🧾 Respon ke ESP32 / Dashboard
// =============================
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
            "kategori" => [
                "throughput" => $kategori_throughput,
                "packet_loss" => $kategori_packetloss,
                "delay" => $kategori_delay,
                "jitter" => $kategori_jitter
            ],
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
