<?php
function analyzeHexBytes($hex) {
    $hex = strtoupper($hex);
    $result = [];

    for ($i = 0; $i < strlen($hex); $i += 2) {
        $byte = substr($hex, $i, 2);
        if (strlen($byte) < 2) continue;

        if (ctype_digit($byte)) {
            $type = "angka";
        } elseif (ctype_xdigit($byte)) {
            $c1 = $byte[0];
            $c2 = $byte[1];

            if (ctype_alpha($c1) && ctype_alpha($c2)) {
                $type = "huruf-huruf";
            } elseif (ctype_digit($c1) && ctype_digit($c2)) {
                $type = "angka";
            } else {
                $type = "campuran";
            }
        } else {
            $type = "tidak valid";
        }

        $result[] = ["byte" => $byte, "type" => $type];
    }

    return $result;
}

// Contoh hex string (bisa kamu ganti pakai $_POST atau $_GET)
$hexInput = isset($_GET['hex']) ? $_GET['hex'] : "3033";
$analysisResult = analyzeHexBytes($hexInput);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Analisis Byte Hex</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        input[type="text"] { padding: 6px; width: 300px; }
        table { border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; }
        th { background-color: #f2f2f2; }
        .angka { background-color: #d0f0c0; }
        .huruf-huruf { background-color: #f0d0d0; }
        .campuran { background-color: #f0f0c0; }
        .tidak-valid { background-color: #f0cccc; }
    </style>
</head>
<body>

<h2>Analisis Setiap Byte dalam String Hex</h2>

<form method="get">
    Masukkan Hex: <input type="text" name="hex" value="<?= htmlspecialchars($hexInput) ?>">
    <button type="submit">Analisis</button>
</form>

<?php if (!empty($analysisResult)): ?>
    <table>
        <thead>
            <tr>
                <th>Index</th>
                <th>Byte</th>
                <th>Jenis</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($analysisResult as $index => $data): ?>
                <tr class="<?= $data['type'] ?>">
                    <td><?= $index ?></td>
                    <td><?= $data['byte'] ?></td>
                    <td><?= $data['type'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Tidak ada data hex valid yang bisa dianalisis.</p>
<?php endif; ?>

</body>
</html>
