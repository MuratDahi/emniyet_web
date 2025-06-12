<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "polis_merkezi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$hata = '';
$basarili = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adi = $_POST['adi'] ?? '';
    $soyadi = $_POST['soyadi'] ?? '';
    $sicil_no = $_POST['sicil_no'] ?? '';
    $rutbe = $_POST['rutbe'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $kullanici_adi = $_POST['kullanici_adi'] ?? '';
    $sifre = $_POST['sifre'] ?? '';

    if ($adi && $soyadi && $sicil_no && $kullanici_adi && $sifre) {
        $stmt = $conn->prepare("CALL PolisEkle(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $adi, $soyadi, $sicil_no, $rutbe, $telefon, $kullanici_adi, $sifre);

        if ($stmt->execute()) {
            $basarili = "Polis başarıyla eklendi.";
        } else {
            $hata = "Hata oluştu: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $hata = "Lütfen zorunlu alanları doldurun.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Polis Ekle</title>
</head>
<body>
    <h1>Polis Ekle</h1>
    <?php if ($hata) echo "<p style='color:red;'>$hata</p>"; ?>
    <?php if ($basarili) echo "<p style='color:green;'>$basarili</p>"; ?>

    <form method="post" action="">
        <label>Adı*: <input type="text" name="adi" required></label><br><br>
        <label>Soyadı*: <input type="text" name="soyadi" required></label><br><br>
        <label>Sicil No*: <input type="text" name="sicil_no" required></label><br><br>
        <label>Rütbe: <input type="text" name="rutbe"></label><br><br>
        <label>Telefon: <input type="text" name="telefon"></label><br><br>
        <label>Kullanıcı Adı*: <input type="text" name="kullanici_adi" required></label><br><br>
        <label>Şifre*: <input type="password" name="sifre" required></label><br><br>

        <button type="submit">Ekle</button>
    </form>

    <br><a href="dashboard.php">← Ana Panele Dön</a>
</body>
</html>
