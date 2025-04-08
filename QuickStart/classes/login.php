<?php

class Login
{
    private $error = "";

    public function evaluate($data)
    {
        
        $NIM = addslashes($data['NIM']);
        $Password = addslashes($data['Password']);

        $query = "SELECT * FROM user WHERE NIM = '$NIM' LIMIT 1";
        
       
        $DB = new Database();
        $result = $DB->read($query);
       
        
        if ($result) {
            $row = $result[0];

            if (password_verify($Password, $row['Password'])) {
                $_SESSION['aiot_userid'] = $row['userid'];
                $_SESSION['aiot_nama'] = $row['Nama'];
                $_SESSION['aiot_NIM'] = $row['NIM'];
                $_SESSION['aiot_email'] = $row['email'];
                $_SESSION['aiot_PBL'] = $row['PBL'];
                $_SESSION['aiot_Tanggal'] = $row['Tanggal'];
            } else {
                $this->error = "Yah Password Kamu Salah Coba lagi Ya!!!<br>";
            }
           
        } else {
           
            $this->error = "No such NIM was found<br>";
        }

        return $this->error;
    }
    
    public function check_login($id){

        $query =  "SELECT * FROM user WHERE userid = '$id' LIMIT 1";
       
        
        $DB = new Database();
        $result = $DB->read($query);

        if ($result) {
            
            return true;
        
        }
        else {
            return false;
        }
  
    }

}