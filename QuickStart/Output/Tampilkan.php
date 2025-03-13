<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "aiot");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil Template1 (hex gabungan) dari user_id = 1
$stmt = $conn->prepare("SELECT Template1 FROM user WHERE userid = 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Data tidak ditemukan untuk user_id = 1.";
    exit;
}

$row = $result->fetch_assoc();
$hexCombined = $row['Template1'];

// Validasi panjang hex (harus minimal 768 karakter = 3 packet)
if (strlen($hexCombined) < 768) {
    echo "Data hex terlalu pendek. Minimal 768 karakter.";
    exit;
}

// Pisahkan menjadi 3 bagian dan konversi ke BLOB
$packets = [];
for ($i = 0; $i < 3; $i++) {
    $hexPart = substr($hexCombined, $i * 256, 256);
    $blob = hex2bin($hexPart);

    if ($blob === false) {
        echo "Gagal konversi hex ke BLOB pada bagian ke-" . ($i + 1) . ".";
        exit;
    }

    $packets[] = [
        'packet_number' => $i + 2,
        'hex' => strtoupper($hexPart),
        'blob_hex' => strtoupper(bin2hex($blob)),
        'blob_raw' => $blob
    ];
}

// Tampilkan hasil
echo "<pre>";
foreach ($packets as $pkt) {
    echo "Packet {$pkt['packet_number']}:\n";
    echo "HEX     : {$pkt['hex']}\n";
    echo "BLOBHEX : {$pkt['blob_hex']}\n";
    echo "BINARY  : ";
    for ($j = 0; $j < strlen($pkt['blob_raw']); $j++) {
        printf("\\x%02X", ord($pkt['blob_raw'][$j]));
    }
    echo "\n\n";
}
echo "</pre>";

$stmt->close();
$conn->close();
?>
