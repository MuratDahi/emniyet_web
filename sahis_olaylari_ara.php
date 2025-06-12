<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$adi       = $_POST['adi']     ?? '';
$soyadi    = $_POST['soyadi']  ?? '';
$hata      = '';
$sonuclar  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$adi && !$soyadi) {
        $hata = "En az bir alanı doldurun (Ad veya Soyad).";
    } else {
        
        $sql = "SELECT sahis_id FROM sahislar WHERE adi LIKE ? AND soyadi LIKE ?";
        $stmt = $conn->prepare($sql);
        $adiParam    = $adi    ? "%{$adi}%"    : "%";
        $soyadiParam = $soyadi ? "%{$soyadi}%" : "%";
        $stmt->bind_param("ss", $adiParam, $soyadiParam);
        $stmt->execute();
        $resIds = $stmt->get_result();

        $sahisIds = [];
        while ($rid = $resIds->fetch_assoc()) {
            $sahisIds[] = $rid['sahis_id'];
        }
        $stmt->close();

        if (empty($sahisIds)) {
            $hata = "Kayıt bulunamadı.";
        } else {
            
            foreach ($sahisIds as $sid) {
                $call = $conn->prepare("CALL SahisVeOlaylariListele(?)");
                $call->bind_param("i", $sid);
                $call->execute();

                if ($res = $call->get_result()) {
                    while ($row = $res->fetch_assoc()) {
                        $sonuclar[] = $row;
                    }
                    $res->free();
                }
                $call->close();

                
                while ($conn->more_results()) {
                    $conn->next_result();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Şahısın Olaylarını Ara</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; background: #f0f2f5; }
        h1 { color: #0074D9; text-align: center; margin-bottom: 20px; }
        form { max-width: 400px; margin: 0 auto 30px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; }
        input[type="text"] {
            width: 100%; padding: 10px; font-size: 16px;
            border-radius: 6px; border: 1px solid #ccc;
            margin-bottom: 15px; box-sizing: border-box;
        }
        button {
            width: 100%; padding: 12px; font-size: 16px;
            border: none; border-radius: 6px;
            background: #0074D9; color: #fff; cursor: pointer;
            transition: background 0.3s;
        }
        button:hover { background: #005fa3; }
        .message {
            text-align: center; color: red; font-weight: 600;
            margin-bottom: 20px;
        }
        table {
            width: 90%; margin: 0 auto; border-collapse: collapse;
            background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px; overflow: hidden;
        }
        th, td {
            padding: 12px; border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th { background: #0074D9; color: #fff; }
        tr:hover { background: #f1f1f1; }
        a.back {
            display: block; text-align: center;
            margin-top: 30px; color: #0074D9;
            text-decoration: none; font-weight: 600;
        }
        a.back:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Şahısın Olaylarını Ara</h1>
    <form method="post" action="">
        <label for="adi">Adı (isteğe bağlı):</label>
        <input type="text" id="adi" name="adi" placeholder="Adı girin"
               value="<?= htmlspecialchars($adi) ?>" />

        <label for="soyadi">Soyadı (isteğe bağlı):</label>
        <input type="text" id="soyadi" name="soyadi" placeholder="Soyadı girin"
               value="<?= htmlspecialchars($soyadi) ?>" />

        <button type="submit">Ara</button>
    </form>

    <?php if ($hata): ?>
        <div class="message"><?= htmlspecialchars($hata) ?></div>
    <?php endif; ?>

    <?php if ($sonuclar): ?>
        <table>
            <thead>
                <tr>
                    <th>Şahıs ID</th>
                    <th>Adı</th>
                    <th>Soyadı</th>
                    <th>TC No</th>
                    <th>Olay ID</th>
                    <th>Tarih</th>
                    <th>Saat</th>
                    <th>Yer</th>
                    <th>Tür</th>
                    <th>Rol</th>
                    <th>İfade</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sonuclar as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['sahis_id']) ?></td>
                    <td><?= htmlspecialchars($row['adi']) ?></td>
                    <td><?= htmlspecialchars($row['soyadi']) ?></td>
                    <td><?= htmlspecialchars($row['tc_no']) ?></td>
                    <td><?= htmlspecialchars($row['olay_id']) ?></td>
                    <td><?= htmlspecialchars($row['olay_tarihi']) ?></td>
                    <td><?= htmlspecialchars($row['saat']) ?></td>
                    <td><?= htmlspecialchars($row['olay_yeri']) ?></td>
                    <td><?= htmlspecialchars($row['olay_tipi']) ?></td>
                    <td><?= htmlspecialchars($row['rol']) ?></td>
                    <td><?= htmlspecialchars($row['ifade']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !$hata): ?>
        <p style="text-align:center; font-weight:600; color:#555;">
            Kayıt bulunamadı.
        </p>
    <?php endif; ?>

    <a href="dashboard.php" class="back">← Ana Panele Dön</a>
</body>
</html>
