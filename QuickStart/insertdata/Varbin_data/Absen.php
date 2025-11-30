<?php
date_default_timezone_set('Asia/Jakarta');

if (!isset($_GET['nim'])) {
    echo json_encode(["status" => "gagal", "pesan" => "NIM tidak dikirim"]);
    exit;
}

$nim = $_GET['nim'];
$conn = new mysqli("localhost", "root", "", "aiot");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "gagal", "pesan" => "Koneksi database gagal"]);
    exit;
}

// Ambil data user berdasarkan NIM
$q = $conn->query("SELECT Nama, PBL FROM user WHERE NIM = '$nim'");
if (!$q || $q->num_rows == 0) {
    echo json_encode(["status" => "gagal", "pesan" => "NIM tidak ditemukan"]);
    exit;
}

$user = $q->fetch_assoc();
$nama = $user['Nama'];
$pbl = $user['PBL'];
$tanggal = date("Y-m-d");
$waktuSekarang = date("Y-m-d H:i:s");

// Cek apakah sudah ada entri absen hari ini untuk NIM ini
$cek = $conn->query("SELECT * FROM absen WHERE NIM = '$nim' AND tanggal = '$tanggal'");

if ($cek->num_rows == 0) {
    // Insert data absen default harian
    $conn->query("INSERT INTO absen (NIM, Nama, PBL, tanggal, status_kehadiran) 
                  VALUES ('$nim', '$nama', '$pbl', '$tanggal', 'Tidak Hadir')");

    // Cek ulang setelah insert
    $cek = $conn->query("SELECT * FROM absen WHERE NIM = '$nim' AND tanggal = '$tanggal'");
}

$data = $cek->fetch_assoc();

// Jika sudah absen masuk → proses absen pulang
if (!empty($data['absen_hadir']) && empty($data['absen_pulang'])) {
    $absen_hadir = strtotime($data['absen_hadir']);
    $absen_pulang = strtotime($waktuSekarang);
    $durasi_menit = round(($absen_pulang - $absen_hadir) / 60, 2);
    $durasi_jam = round($durasi_menit / 60, 2);

    // Kategori durasi
    if ($durasi_jam >= 8) {
        $kategori = "Full Day";
    } elseif ($durasi_jam >= 4) {
        $kategori = "Half Day";
    } else {
        $kategori = "Short";
    }

    $conn->query("UPDATE absen SET 
        absen_pulang = '$waktuSekarang',
        durasi_jam = $durasi_jam,
        kategori_durasi = '$kategori'
        WHERE NIM = '$nim' AND tanggal = '$tanggal'");

    echo json_encode([
        "status" => "sukses",
        "pesan" => "Absen pulang tercatat",
        "nama" => $nama,
        "waktu" => $waktuSekarang,
        "durasi_jam" => $durasi_jam,
        "kategori" => $kategori
    ]);
} else {
    // Absen masuk
    $jam_masuk = date("H:i:s", strtotime($waktuSekarang));
    $status_masuk = ($jam_masuk <= "08:00:00") ? "Tepat Waktu" : "Terlambat";

    $conn->query("UPDATE absen SET 
        absen_hadir = '$waktuSekarang',
        status_kehadiran = 'Hadir',
        status_masuk = '$status_masuk'
        WHERE NIM = '$nim' AND tanggal = '$tanggal'");

    echo json_encode([
        "status" => "sukses",
        "pesan" => "Absen hadir tercatat",
        "nama" => $nama,
        "waktu" => $waktuSekarang,
        "status_masuk" => $status_masuk
    ]);
}

$conn->close();
