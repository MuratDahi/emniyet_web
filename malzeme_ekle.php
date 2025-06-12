<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) die("Bağlantı hatası: " . $conn->connect_error);

$hata = $basarili = '';


$turler = [
    'Uzun Namlu',
    'Tabanca',
    'Ağır Silah'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $malzeme_adi  = trim($_POST['malzeme_adi'] ?? '');
    $tur           = $_POST['tur'] ?? '';
    $miktar        = intval($_POST['miktar'] ?? 0);
    $kayit_tarihi  = $_POST['kayit_tarihi'] ?? '';
    $durum         = $_POST['durum'] ?? '';

    
    if (!$malzeme_adi || !in_array($tur, $turler, true) || !$miktar || !$kayit_tarihi || !in_array($durum, ['Kullanımda','Depoda','Hurda'], true)) {
        $hata = "Lütfen tüm zorunlu alanları doğru doldurun.";
    } else {
        $stmt = $conn->prepare("CALL MalzemeEkle(?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $malzeme_adi, $tur, $miktar, $kayit_tarihi, $durum);
        if ($stmt->execute()) {
            $basarili = "Malzeme başarıyla eklendi.";
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
    <meta charset="UTF-8" />
    <title>Malzeme Ekle</title>
</head>
<body>
<h1>Malzeme Ekle</h1>
<?php if ($hata): ?>
    <p style="color:red; font-weight:bold;"><?= htmlspecialchars($hata) ?></p>
<?php elseif ($basarili): ?>
    <p style="color:green; font-weight:bold;"><?= htmlspecialchars($basarili) ?></p>
<?php endif; ?>

<form method="post" action="">
    <label>Malzeme Adı*: <input type="text" name="malzeme_adi" required value="<?= htmlspecialchars($_POST['malzeme_adi'] ?? '') ?>"></label><br><br>

    <label>Tür*:
        <select name="tur" required>
            <option value="">Seçiniz</option>
            <?php foreach ($turler as $t): ?>
                <option value="<?= htmlspecialchars($t) ?>" <?= isset($_POST['tur']) && $_POST['tur'] === $t ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>Miktar*: <input type="number" name="miktar" min="1" required value="<?= htmlspecialchars($_POST['miktar'] ?? '1') ?>"></label><br><br>

    <label>Kayıt Tarihi*: <input type="date" name="kayit_tarihi" required value="<?= htmlspecialchars($_POST['kayit_tarihi'] ?? date('Y-m-d')) ?>"></label><br><br>

    <label>Durum*:
        <select name="durum" required>
            <option value="">Seçiniz</option>
            <option value="Kullanımda" <?= isset($_POST['durum']) && $_POST['durum']==='Kullanımda' ? 'selected' : '' ?>>Kullanımda</option>
            <option value="Depoda"      <?= isset($_POST['durum']) && $_POST['durum']==='Depoda'      ? 'selected' : '' ?>>Depoda</option>
            <option value="Hurda"       <?= isset($_POST['durum']) && $_POST['durum']==='Hurda'       ? 'selected' : '' ?>>Hurda</option>
        </select>
    </label><br><br>

    <button type="submit">Ekle</button>
</form>
<br>
<a href="dashboard.php">← Ana Panele Dön</a>
</body>
</html>
