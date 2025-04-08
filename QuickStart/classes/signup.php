<?php

class Signup
{

    private $error = "";
    
    public function evaluate($data)
    {
        foreach ($data as $key => $value) {
            if(empty($value)) {
                $this->error = $this->error . $key . " is empty!<br>";
            }
        }

        if($this->error == "") 
        {
            // No error
            return $this->create_user($data);
        }
        else
        {
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

        // Cek apakah NIM sudah terdaftar
        $DB = new Database();
        $nim_check_query = "SELECT * FROM user WHERE NIM = '$NIM'";
        $result_nim = $DB->read($nim_check_query);

        // Cek apakah email sudah terdaftar
        $email_check_query = "SELECT * FROM user WHERE email = '$email'";
        $result_email = $DB->read($email_check_query);

        if (is_array($result_nim) && count($result_nim) > 0) {
            // NIM sudah terdaftar
            return "NIM sudah terdaftar, gunakan NIM lain!";
        } elseif (is_array($result_email) && count($result_email) > 0) {
            // Email sudah terdaftar
            return "Email sudah terdaftar, gunakan email lain!";
        } else {
            // NIM dan email belum terdaftar, lanjutkan pendaftaran
            $hashedPassword = password_hash($Password, PASSWORD_DEFAULT);
            $url_addres = strtolower($Nama) . "." . strtolower($NIM);
            $userid = $this->create_userid();

            $query = "INSERT INTO user (userid, Nama, NIM, PBL, Password, email, gender, url_addres) 
                      VALUES ('$userid', '$Nama', '$NIM', '$PBL', '$hashedPassword', '$email', '$gender', '$url_addres')";

            $DB->save($query);
            header("Location: login.php");
            exit();
            return "Pendaftaran berhasil!";
            

        }
    }

    private function create_userid()
    {
        $length = rand(4,10);
        $number = "";
        for ($i = 0; $i < $length; $i++){
            $new_rand = rand(0,9);
            $number .= $new_rand;
        }
        return $number;   
    }
}