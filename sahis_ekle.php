<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) die("Bağlantı hatası: " . $conn->connect_error);

$hata = $basarili = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adi = $_POST['adi'] ?? '';
    $soyadi = $_POST['soyadi'] ?? '';
    $tc_no = $_POST['tc_no'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $adres = $_POST['adres'] ?? '';
    $dogum_tarihi = $_POST['dogum_tarihi'] ?? null;
    $cinsiyet = $_POST['cinsiyet'] ?? '';

    if ($adi && $soyadi && $tc_no && $cinsiyet) {
        $stmt = $conn->prepare("CALL SahisEkle(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $adi, $soyadi, $tc_no, $telefon, $adres, $dogum_tarihi, $cinsiyet);
        if ($stmt->execute()) $basarili = "Şahıs başarıyla eklendi.";
        else $hata = "Hata: " . $stmt->error;
        $stmt->close();
    } else {
        $hata = "Zorunlu alanları doldurun.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head><meta charset="UTF-8" /><title>Şahıs Ekle</title></head>
<body>
<h1>Şahıs Ekle</h1>
<?php if ($hata) echo "<p style='color:red;'>$hata</p>"; ?>
<?php if ($basarili) echo "<p style='color:green;'>$basarili</p>"; ?>

<form method="post" action="">
    <label>Adı*: <input type="text" name="adi" required></label><br><br>
    <label>Soyadı*: <input type="text" name="soyadi" required></label><br><br>
    <label>TC No*: <input type="text" name="tc_no" maxlength="11" required></label><br><br>
    <label>Telefon: <input type="text" name="telefon"></label><br><br>
    <label>Adres: <textarea name="adres"></textarea></label><br><br>
    <label>Doğum Tarihi: <input type="date" name="dogum_tarihi"></label><br><br>
    <label>Cinsiyet*:
        <select name="cinsiyet" required>
            <option value="">Seçiniz</option>
            <option value="Erkek">Erkek</option>
            <option value="Kadın">Kadın</option>
        </select>
    </label><br><br>
    <button type="submit">Ekle</button>
</form>
<br><a href="dashboard.php">← Ana Panele Dön</a>
</body>
</html>
