<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: listele_guncelle.php?tablo=malzemeler");
    exit;
}

$malzeme_id = intval($_GET['id']);
$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) die("Bağlantı hatası: " . $conn->connect_error);

$hata = $basarili = '';


$turler = ['UZUN NAMLU', 'TABANCA', 'AĞIR SİLAH'];
$durumlar = ['Kullanımda', 'Depoda', 'Hurda'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $malzeme_adi  = trim($_POST['malzeme_adi'] ?? '');
    $tur           = $_POST['tur'] ?? '';
    $miktar        = intval($_POST['miktar'] ?? 0);
    $kayit_tarihi  = $_POST['kayit_tarihi'] ?? '';
    $durum         = $_POST['durum'] ?? '';

    if (!$malzeme_adi || !in_array($tur, $turler, true) || $miktar !== 1 || !$kayit_tarihi || !in_array($durum, $durumlar, true)) {
        $hata = 'Lütfen tüm zorunlu alanları doğru doldurun.';
    } else {
        $stmt = $conn->prepare("CALL MalzemeGuncelle(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississ", $malzeme_id, $malzeme_adi, $tur, $miktar, $kayit_tarihi, $durum);
        if ($stmt->execute()) {
            $basarili = 'Malzeme başarıyla güncellendi.';
        } else {
            $hata = 'Hata: ' . $stmt->error;
        }
        $stmt->close();
        header("Refresh:0");
    }
} else {
    $stmt = $conn->prepare("SELECT malzeme_adi, tur, miktar, kayit_tarihi, durum FROM malzemeler WHERE malzeme_id = ?");
    $stmt->bind_param("i", $malzeme_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row) die('Malzeme bulunamadı.');

    $malzeme_adi  = $row['malzeme_adi'];
    $tur           = $row['tur'];
    $miktar        = (int)$row['miktar'];
    $kayit_tarihi  = $row['kayit_tarihi'];
    $durum         = $row['durum'];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head><meta charset="UTF-8"><title>Malzeme Düzenle</title></head>
<body>
<h1>Malzeme Düzenle</h1>
<?php if ($hata): ?><p style="color:red; font-weight:bold;"><?= htmlspecialchars($hata) ?></p><?php endif; ?>
<?php if ($basarili): ?><p style="color:green; font-weight:bold;"><?= htmlspecialchars($basarili) ?></p><?php endif; ?>
<form method="post" action="">
    <label>Malzeme Adı*:<br>
      <input type="text" name="malzeme_adi" required value="<?= htmlspecialchars($malzeme_adi) ?>">
    </label><br><br>

    <label>Tür*:<br>
      <select name="tur" required>
        <option value="">Seçiniz</option>
        <?php foreach ($turler as $t): ?>
          <option value="<?= $t ?>" <?= $tur === $t ? 'selected' : '' ?>><?= $t ?></option>
        <?php endforeach; ?>
      </select>
    </label><br><br>

    <label>Miktar*:<br>
      <input type="number" name="miktar" value="1" readonly>
    </label><br><br>

    <label>Kayıt Tarihi*:<br>
      <input type="date" name="kayit_tarihi" required value="<?= htmlspecialchars($kayit_tarihi) ?>">
    </label><br><br>

    <label>Durum*:<br>
      <select name="durum" required>
        <option value="">Seçiniz</option>
        <?php foreach ($durumlar as $d): ?>
          <option value="<?= $d ?>" <?= $durum === $d ? 'selected' : '' ?>><?= $d ?></option>
        <?php endforeach; ?>
      </select>
    </label><br><br>

    <button type="submit">Güncelle</button>
</form>
<br>
<a href="listele_guncelle.php?tablo=malzemeler">← Malzeme Listesine Dön</a>
</body>
</html>
