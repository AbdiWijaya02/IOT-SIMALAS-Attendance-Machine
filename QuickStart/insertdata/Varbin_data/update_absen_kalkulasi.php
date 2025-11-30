<?php

date_default_timezone_set("Asia/Jakarta");
$conn = new mysqli("localhost", "root", "", "aiot");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

function isValidDateTime($datetime)
{
    $d = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
    return $d && $d->format('Y-m-d H:i:s') === $datetime;
}

$now = time();

$result = $conn->query("SELECT * FROM absen");

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $hadir = $row['absen_hadir'];
    $pulang = $row['absen_pulang'];
    $tanggal = $row['tanggal'];

    $status_masuk = null;
    $durasi_kerja = null;
    $durasi_jam = null;
    $kategori_durasi = null;
    $status_kehadiran = null;

    $isHadirValid = isValidDateTime($hadir);
    $isPulangValid = isValidDateTime($pulang);

    $hadir_time = $isHadirValid ? strtotime($hadir) : false;
    $pulang_time = $isPulangValid ? strtotime($pulang) : false;

    $limit23 = strtotime("$tanggal 17:00:00");

    if ($hadir_time && $pulang_time) {
        $durasi_kerja = round(($pulang_time - $hadir_time) / 3600, 2);
        $durasi_jam = $durasi_kerja;

        if ($durasi_kerja >= 8) {
            $kategori_durasi = "Full";
        } elseif ($durasi_kerja >= 4) {
            $kategori_durasi = "Half Day";
        } else {
            $kategori_durasi = "Short";
        }

        $status_kehadiran = "Hadir";
    } else {
        // Fix logika Belum Pulang vs Tidak Hadir
        if (!$hadir_time && !$pulang_time && $now > $limit23) {
            $status_kehadiran = "Tidak Hadir";
        } elseif ($hadir_time && !$pulang_time && $now > $limit23) {
            $status_kehadiran = "Tidak Hadir";
        } elseif (($hadir_time || !$hadir_time) && !$pulang_time && $now <= $limit23) {
            $status_kehadiran = "Belum Pulang";
        } elseif (!$hadir_time && !$pulang_time && $now <= $limit23) {
            $status_kehadiran = "Belum Pulang";
        }
    }

    if ($status_kehadiran === "Tidak Hadir") {
        $status_masuk = "Absent";
    } elseif ($hadir_time) {
        $jam_masuk = date("H:i:s", $hadir_time);
        $status_masuk = ($jam_masuk <= "08:00:00") ? "Tepat Waktu" : "Terlambat";
    }

    $update = "
        UPDATE absen SET
            status_masuk = " . ($status_masuk ? "'$status_masuk'" : "NULL") . ",
            durasi_kerja = " . ($durasi_kerja !== null ? $durasi_kerja : "NULL") . ",
            durasi_jam = " . ($durasi_jam !== null ? $durasi_jam : "NULL") . ",
            kategori_durasi = " . ($kategori_durasi ? "'$kategori_durasi'" : "NULL") . ",
            status_kehadiran = " . ($status_kehadiran ? "'$status_kehadiran'" : "NULL") . "
        WHERE id = $id";

    $conn->query($update);
}

$conn->close();
echo "✅ Update selesai";
