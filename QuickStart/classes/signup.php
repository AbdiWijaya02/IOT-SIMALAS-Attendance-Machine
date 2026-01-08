<?php

class Signup
{
    private $error = "";

    public function evaluate($data)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                $this->error = $this->error . $key . " is empty!<br>";
            }
        }

        if ($this->error == "") {
            // No error
            return $this->create_user($data);
        } else {
            return $this->error;
        }
    }

    public function create_user($data)
    {
        $Nama = isset($data['Nama']) ? $data['Nama'] : '';
        $NIM = isset($data['NIM']) ? $data['NIM'] : '';
        $PBL = isset($data['PBL']) ? $data['PBL'] : '';
        $Password = isset($data['Password']) ? $data['Password'] : '';
        $email = isset($data['email']) ? $data['email'] : '';
        $gender = isset($data['gender']) ? $data['gender'] : '';
        $Angkatan = isset($data['Angkatan']) ? $data['Angkatan'] : '';

        $DB = new Database();

        // 1. Cek apakah NIM sudah terdaftar
        $nim_check_query = "SELECT * FROM user WHERE NIM = '$NIM'";
        $result_nim = $DB->read($nim_check_query);

        // 2. Cek apakah email sudah terdaftar
        $email_check_query = "SELECT * FROM user WHERE email = '$email'";
        $result_email = $DB->read($email_check_query);

        if (is_array($result_nim) && count($result_nim) > 0) {
            return "NIM sudah terdaftar, gunakan NIM lain!";
        } elseif (is_array($result_email) && count($result_email) > 0) {
            return "Email sudah terdaftar, gunakan email lain!";
        } else {
            // 3. NIM dan email aman. Sekarang cari UserID
            $userid = $this->create_userid();

            // Jika UserID false, berarti penuh (1-127 terpakai semua)
            if ($userid === false) {
                return "Maaf, Database Penuh (Maksimal 127 User). Hubungi Admin.";
            }

            $hashedPassword = password_hash($Password, PASSWORD_DEFAULT);
            $url_addres = strtolower($Nama) . "." . strtolower($NIM);

            // Simpan data dengan UserID yang sudah divalidasi (1-127)
            $query = "INSERT INTO user (userid, Nama, NIM, PBL, Password, email, gender, Angkatan, url_addres) 
                      VALUES ('$userid', '$Nama', '$NIM', '$PBL', '$hashedPassword', '$email', '$gender', '$Angkatan', '$url_addres')";

            $DB->save($query);
            
            // Redirect ke login
            header("Location: login.php");
            exit();
        }
    }

    // --- FUNGSI BARU: MENCARI SLOT KOSONG 1-127 ---
    private function create_userid()
    {
        $DB = new Database();
        
        // Loop dari angka 1 sampai 127
        for ($i = 1; $i <= 127; $i++) {
            // Cek apakah angka $i ini sudah dipakai orang lain?
            $query = "SELECT userid FROM user WHERE userid = '$i' LIMIT 1";
            $result = $DB->read($query);

            // Jika result kosong (tidak ada data), berarti angka $i TERSEDIA
            if (!is_array($result) || count($result) == 0) {
                return $i; // Kembalikan angka ini untuk dipakai
            }
        }

        // Jika loop selesai dan tidak ada yang return, berarti penuh
        return false;
    }
}
?>