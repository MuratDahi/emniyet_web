<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) die("Bağlantı hatası: " . $conn->connect_error);

$sonuclar = [];
$hata = '';

$sql = "CALL MalzemeTuruneGoreMiktar()";
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $sonuclar[] = $row;
    }
    $result->close();
    $conn->next_result();
} else {
    $hata = "Sorgu hatası: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Malzeme Türüne Göre Miktarlar</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; background: #f0f2f5; }
        h1 { color: #0074D9; margin-bottom: 20px; text-align: center; }
        table {
            margin: 0 auto;
            width: 50%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #0074D9;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .message {
            text-align: center;
            color: red;
            font-weight: 600;
            margin-bottom: 20px;
        }
        a.back {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #0074D9;
            text-decoration: none;
            font-weight: 600;
        }
        a.back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Malzeme Türüne Göre Miktarlar</h1>

    <?php if ($hata): ?>
        <div class="message"><?= htmlspecialchars($hata) ?></div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Malzeme Türü</th>
                    <th>Toplam Miktar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sonuclar as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['tur']) ?></td>
                    <td><?= htmlspecialchars($row['toplam_miktar']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="iliskili_sorgulama_secim.php" class="back">← İlişkili Sorgulama Seçimine Dön</a>
</body>
</html>
