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

$hata = $basarili = '';


$polisler   = $conn->query("SELECT polis_id, adi, soyadi FROM polisler")->fetch_all(MYSQLI_ASSOC);
$olaylar    = $conn->query("SELECT olay_id, olay_tipi, olay_yeri FROM olaylar")->fetch_all(MYSQLI_ASSOC);
$araclar    = $conn->query("SELECT arac_id, plaka FROM araclar")->fetch_all(MYSQLI_ASSOC);
$malzemeler = $conn->query("SELECT malzeme_id, malzeme_adi, tur FROM malzemeler")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $polis_id    = intval($_POST['polis_id']    ?? 0);
    $olay_id     = intval($_POST['olay_id']     ?? 0);
    $arac_id     = intval($_POST['arac_id']     ?? 0);
    $malzeme_id  = intval($_POST['malzeme_id']  ?? 0);
    $tarih       = $_POST['tarih']             ?? '';
    $saat        = $_POST['saat']              ?? '';

    
    if (!$polis_id || !$olay_id || !$tarih || !$saat) {
        $hata = "Lütfen zorunlu alanları doldurun (Polis, Olay, Tarih, Saat).";
    } elseif ($malzeme_id && !$conn->query("SELECT 1 FROM malzemeler WHERE malzeme_id = $malzeme_id")->fetch_row()) {
        $hata = "Geçersiz malzeme seçimi.";
    }

   
    if (!$hata) {
        $stmt = $conn->prepare("CALL GorevEkle(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "iiiiss",
            $olay_id,
            $polis_id,
            $arac_id,
            $malzeme_id,
            $tarih,
            $saat
        );
        if ($stmt->execute()) {
            $basarili = "Görev başarıyla eklendi.";
        } else {
            $hata = "Hata: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görev Ekle</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; padding: 30px; }
        h1 { text-align: center; margin-bottom: 20px; }
        form { background: #fff; padding: 20px; border-radius: 8px; max-width: 600px; margin: 0 auto; }
        label { display: block; margin-bottom: 12px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-top: 4px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #0074D9; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; display: block; margin: 0 auto; }
        button:hover { background: #005fa3; }
        .message { max-width: 600px; margin: 20px auto; padding: 12px; border-radius: 4px; text-align: center; }
        .error   { background: #f2dede; color: #a94442; }
        .success { background: #dff0d8; color: #3c763d; }
        a.back { display: block; text-align: center; margin-top: 20px; color: #0074D9; text-decoration: none; }
        a.back:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Görev Ekle</h1>
    <?php if ($hata): ?>
        <div class="message error"><?= htmlspecialchars($hata) ?></div>
    <?php elseif ($basarili): ?>
        <div class="message success"><?= htmlspecialchars($basarili) ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Polis*:
            <select name="polis_id" required>
                <option value="">Seçiniz</option>
                <?php foreach ($polisler as $p): ?>
                    <option value="<?= $p['polis_id'] ?>" <?= isset($_POST['polis_id']) && $_POST['polis_id']==$p['polis_id']?'selected':'' ?>>
                        <?= htmlspecialchars($p['adi'].' '.$p['soyadi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Olay*:
            <select name="olay_id" required>
                <option value="">Seçiniz</option>
                <?php foreach ($olaylar as $o): ?>
                    <option value="<?= $o['olay_id'] ?>" <?= isset($_POST['olay_id']) && $_POST['olay_id']==$o['olay_id']?'selected':'' ?>>
                        <?= htmlspecialchars($o['olay_tipi'].' @ '.$o['olay_yeri']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Tarih*:
            <input type="date" name="tarih" value="<?= htmlspecialchars($_POST['tarih'] ?? '') ?>" required>
        </label>
        <label>Saat*:
            <input type="time" name="saat" value="<?= htmlspecialchars($_POST['saat'] ?? '') ?>" required>
        </label>
        <label>Araç:
            <select name="arac_id">
                <option value="0">Seçiniz</option>
                <?php foreach ($araclar as $a): ?>
                    <option value="<?= $a['arac_id'] ?>" <?= isset($_POST['arac_id']) && $_POST['arac_id']==$a['arac_id']?'selected':'' ?>>
                        <?= htmlspecialchars($a['plaka']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Malzeme*:
            <select name="malzeme_id" required>
                <option value="0">Seçiniz</option>
                <?php foreach ($malzemeler as $m): ?>
                    <option value="<?= $m['malzeme_id'] ?>" <?= isset($_POST['malzeme_id']) && $_POST['malzeme_id']==$m['malzeme_id']?'selected':'' ?>>
                        <?= htmlspecialchars($m['malzeme_id'].' - '.$m['tur'].' – '.$m['malzeme_adi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Görev Ekle</button>
    </form>
    <a href="ekleme_secimi.php" class="back">← Ekleme Seçimine Dön</a>
</body>
</html>
