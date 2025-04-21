<?php

class PacketChecker
{
    private $error = "";

    public function check($packet2, $packet3)
    {
        $query = "SELECT Paket2, Paket3, userid, id, Nama FROM user WHERE PBL = 'Edukasi' ORDER BY id DESC";

        $DB = new Database();
        $result = $DB->read($query);

        // Data referensi
        $packet2Hex = bin2hex($packet2);
        $packet3Hex = bin2hex($packet3);

        if ($result) {
            foreach ($result as $row) {
                $dbPacket2 = bin2hex($row['Paket2']);
                $dbPacket3 = bin2hex($row['Paket3']);

                if ($dbPacket2 === $packet2Hex && $dbPacket3 === $packet3Hex) {
                    // Jika cocok
                    return [
                        'match' => true,
                        'userid' => $row['userid'],
                        'nama' => $row['Nama'],
                        'id' => $row['id'],
                        'packet2_hex' => $dbPacket2,
                        'packet3_hex' => $dbPacket3
                    ];
                }
            }

            // Tidak ada yang cocok
            $this->error = "❌ Tidak ada data yang cocok.";
            return false;

        } else {
            $this->error = "⚠️ Tidak ada data di database.";
            return false;
        }
    }

    public function getError()
    {
        return $this->error;
    }
}
