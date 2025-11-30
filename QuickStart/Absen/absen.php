<?php
date_default_timezone_set('Asia/Jakarta');
$conn = new mysqli("localhost", "root", "", "aiot");

$tanggal = date("Y-m-d");

$conn->query("
  UPDATE absen
  SET 
    status_kehadiran = 'Tidak Hadir',
    status_masuk = 'Absent',
    kategori_durasi = 'Absent'
  WHERE absen_hadir IS NULL AND tanggal = '$tanggal'
");

echo "Data absensi kosong hari ini sudah ditandai sebagai Tidak Hadir.";
