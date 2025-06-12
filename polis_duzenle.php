<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: listele_guncelle.php?tablo=polisler");
    exit;
}

$polis_id = intval($_GET['id']);
$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) die("Bağlantı hatası: " . $conn->connect_error);

$hata = $basarili = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adi = $_POST['adi'] ?? '';
    $soyadi = $_POST['soyadi'] ?? '';
    $sicil_no = $_POST['sicil_no'] ?? '';
    $rutbe = $_POST['rutbe'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $kullanici_adi = $_POST['kullanici_adi'] ?? '';
    $sifre = $_POST['sifre'] ?? '';

    if ($adi && $soyadi && $sicil_no && $kullanici_adi && $sifre) {
        $stmt = $conn->prepare("CALL PolisGuncelle(?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $polis_id, $adi, $soyadi, $sicil_no, $rutbe, $telefon, $kullanici_adi, $sifre);
        if ($stmt->execute()) $basarili = "Polis başarıyla güncellendi.";
        else $hata = "Hata: " . $stmt->error;
        $stmt->close();
    } else {
        $hata = "Zorunlu alanları doldurun.";
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM polisler WHERE polis_id = ?");
    $stmt->bind_param("i", $polis_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows != 1) die("Polis bulunamadı.");
    $row = $result->fetch_assoc();

    $adi = $row['adi'];
    $soyadi = $row['soyadi'];
    $sicil_no = $row['sicil_no'];
    $rutbe = $row['rutbe'];
    $telefon = $row['telefon'];
    $kullanici_adi = $row['kullanici_adi'];
    $sifre = $row['sifre'];

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Polis Düzenle</title>
</head>
<body>
    <h1>Polis Düzenle</h1>
    <?php if ($hata) echo "<p style='color:red;'>$hata</p>"; ?>
    <?php if ($basarili) echo "<p style='color:green;'>$basarili</p>"; ?>

    <form method="post" action="">
        <label>Adı*: <input type="text" name="adi" value="<?php echo htmlspecialchars($adi); ?>" required></label><br><br>
        <label>Soyadı*: <input type="text" name="soyadi" value="<?php echo htmlspecialchars($soyadi); ?>" required></label><br><br>
        <label>Sicil No*: <input type="text" name="sicil_no" value="<?php echo htmlspecialchars($sicil_no); ?>" required></label><br><br>
        <label>Rütbe: <input type="text" name="rutbe" value="<?php echo htmlspecialchars($rutbe); ?>"></label><br><br>
        <label>Telefon: <input type="text" name="telefon" value="<?php echo htmlspecialchars($telefon); ?>"></label><br><br>
        <label>Kullanıcı Adı*: <input type="text" name="kullanici_adi" value="<?php echo htmlspecialchars($kullanici_adi); ?>" required></label><br><br>
        <label>Şifre*: <input type="password" name="sifre" value="<?php echo htmlspecialchars($sifre); ?>" required></label><br><br>

        <button type="submit">Güncelle</button>
    </form>

    <br><a href="listele_guncelle.php?tablo=polisler">← Polis Listesine Dön</a>
</body>
</html>
