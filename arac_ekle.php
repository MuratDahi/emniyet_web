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
    $plaka = $_POST['plaka'] ?? '';
    $marka = $_POST['marka'] ?? '';
    $model = $_POST['model'] ?? '';
    $tur = $_POST['tur'] ?? '';
    $durum = $_POST['durum'] ?? '';

    if ($plaka && $durum) {
        $stmt = $conn->prepare("CALL AracEkle(?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $plaka, $marka, $model, $tur, $durum);
        if ($stmt->execute()) $basarili = "Araç başarıyla eklendi.";
        else $hata = "Hata: " . $stmt->error;
        $stmt->close();
    } else $hata = "Zorunlu alanları doldurun.";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head><meta charset="UTF-8" /><title>Araç Ekle</title></head>
<body>
<h1>Araç Ekle</h1>
<?php if ($hata) echo "<p style='color:red;'>$hata</p>"; ?>
<?php if ($basarili) echo "<p style='color:green;'>$basarili</p>"; ?>

<form method="post" action="">
<label>Plaka*: <input type="text" name="plaka" required></label><br><br>
<label>Marka: <input type="text" name="marka"></label><br><br>
<label>Model: <input type="text" name="model"></label><br><br>
<label>Tür: <input type="text" name="tur"></label><br><br>
<label>Durum*:
    <select name="durum" required>
        <option value="">Seçiniz</option>
        <option value="Aktif">Aktif</option>
        <option value="Pasif">Pasif</option>
    </select>
</label><br><br>
<button type="submit">Ekle</button>
</form>
<br><a href="dashboard.php">← Ana Panele Dön</a>
</body>
</html>
