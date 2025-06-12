<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}


if (!isset($_GET['id'])) {
    header("Location: listele_guncelle.php?tablo=gorevler");
    exit;
}
$gorev_id = intval($_GET['id']);


$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) die("Bağlantı hatası: " . $conn->connect_error);

$hata = $basarili = '';


$polisler   = $conn->query("SELECT polis_id, adi, soyadi FROM polisler")->fetch_all(MYSQLI_ASSOC);
$olaylar    = $conn->query("SELECT olay_id, olay_tipi, olay_yeri FROM olaylar")->fetch_all(MYSQLI_ASSOC);
$araclar    = $conn->query("SELECT arac_id, plaka FROM araclar")->fetch_all(MYSQLI_ASSOC);
$malzemeler = $conn->query("SELECT malzeme_id, tur, malzeme_adi FROM malzemeler")->fetch_all(MYSQLI_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $polis_id   = intval($_POST['polis_id']   ?? 0);
    $olay_id    = intval($_POST['olay_id']    ?? 0);
    $arac_id    = intval($_POST['arac_id']    ?? 0);
    $malzeme_id = intval($_POST['malzeme_id'] ?? 0);
    $tarih      = $_POST['tarih']             ?? '';
    $saat       = $_POST['saat']              ?? '';

    
    if (!$polis_id || !$olay_id || !$tarih || !$saat) {
        $hata = "Lütfen zorunlu alanları doldurun (Polis, Olay, Tarih, Saat).";
    } elseif ($malzeme_id && !$conn->query("SELECT 1 FROM malzemeler WHERE malzeme_id = $malzeme_id")->fetch_row()) {
        $hata = "Geçersiz malzeme seçimi.";
    }

    
    if (!$hata) {
        $stmt = $conn->prepare(
            "UPDATE gorevler
               SET olay_id = ?, polis_id = ?, arac_id = ?, malzeme_id = ?, tarih = ?, saat = ?
             WHERE gorev_id = ?"
        );
        $stmt->bind_param(
            "iiisisi",
            $olay_id,
            $polis_id,
            $arac_id,
            $malzeme_id,
            $tarih,
            $saat,
            $gorev_id
        );
        if ($stmt->execute()) {
            $basarili = "Görev başarıyla güncellendi.";
            header("Refresh:0");
        } else {
            $hata = "Hata: " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    
    $stmt = $conn->prepare(
        "SELECT olay_id, polis_id, arac_id, malzeme_id, tarih, saat
           FROM gorevler
          WHERE gorev_id = ?"
    );
    $stmt->bind_param("i", $gorev_id);
    $stmt->execute();
    $stmt->bind_result($sel_olay, $sel_polis, $sel_arac, $sel_malzeme, $sel_tarih, $sel_saat);
    $stmt->fetch();
    $stmt->close();
    // prefill
    $tarih = $sel_tarih;
    $saat  = $sel_saat;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görev Düzenle</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 30px; }
        form { background: #fff; padding: 20px; border-radius: 8px; max-width: 600px; margin: 0 auto; }
        label { display: block; margin-bottom: 10px; font-weight: bold; }
        select, input { width: 100%; padding: 8px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 4px; }
        .error { color: #a94442; font-weight: bold; }
        .success { color: #3c763d; font-weight: bold; }
        button { background: #0074D9; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005fa3; }
        a.back { display: block; text-align: center; margin-top: 20px; color: #0074D9; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Görev Düzenle</h1>
    <?php if ($hata): ?><p class="error"><?= htmlspecialchars($hata) ?></p><?php endif; ?>
    <?php if ($basarili): ?><p class="success"><?= htmlspecialchars($basarili) ?></p><?php endif; ?>
    <form method="post">
        <label>Polis*:
            <select name="polis_id" required>
                <option value="">Seçiniz</option>
                <?php foreach ($polisler as $p): ?>
                    <option value="<?= $p['polis_id'] ?>" <?= (isset($sel_polis) && $sel_polis == $p['polis_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['adi'] . ' ' . $p['soyadi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Olay*:
            <select name="olay_id" required>
                <option value="">Seçiniz</option>
                <?php foreach ($olaylar as $o): ?>
                    <option value="<?= $o['olay_id'] ?>" <?= (isset($sel_olay) && $sel_olay == $o['olay_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($o['olay_tipi'] . ' @ ' . $o['olay_yeri']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Araç:
            <select name="arac_id">
                <option value="0">Seçiniz</option>
                <?php foreach ($araclar as $a): ?>
                    <option value="<?= $a['arac_id'] ?>" <?= (isset($sel_arac) && $sel_arac == $a['arac_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['plaka']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Malzeme:
            <select name="malzeme_id">
                <option value="0">Seçiniz</option>
                <?php foreach ($malzemeler as $m): ?>
                    <option value="<?= $m['malzeme_id'] ?>" <?= (isset($sel_malzeme) && $sel_malzeme == $m['malzeme_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['tur'] . ' – ' . $m['malzeme_adi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Tarih*:
            <input type="date" name="tarih" required value="<?= htmlspecialchars($tarih) ?>">
        </label>
        <label>Saat*:
            <input type="time" name="saat" required value="<?= htmlspecialchars($saat) ?>">
        </label>
        <button type="submit">Güncelle</button>
    </form>
    <a href="listele_guncelle.php?tablo=gorevler" class="back">← Geri Dön</a>
</body>
</html>
