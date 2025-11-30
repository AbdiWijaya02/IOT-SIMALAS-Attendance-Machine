<?php
$conn = new mysqli("localhost", "root", "", "aiot");
$conn->query("
    INSERT INTO absen (NIM, Nama, PBL, tanggal, status_kehadiran)
    SELECT NIM, Nama, PBL, CURDATE(), 'Tidak Hadir'
    FROM user
    WHERE NOT EXISTS (
        SELECT 1 FROM absen 
        WHERE absen.NIM = user.NIM AND absen.tanggal = CURDATE()
    )
");
echo "Absensi awal berhasil dibuat untuk hari ini.";
