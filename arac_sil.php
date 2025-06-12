<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: listele_sil.php?tablo=araclar");
    exit;
}

$arac_id = intval($_GET['id']);

$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$stmt = $conn->prepare("CALL AracSil(?)");
$stmt->bind_param("i", $arac_id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: listele_sil.php?tablo=araclar&mesaj=Silme başarılı.");
    exit;
} else {
    $hata = "Silme işlemi başarısız: " . $stmt->error;
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Araç Sil</title>
</head>
<body>
    <h1>Araç Sil</h1>
    <?php if (!empty($hata)) echo "<p style='color:red;'>$hata</p>"; ?>
    <a href="listele_sil.php?tablo=araclar">← Araç Listesine Dön</a>
</body>
</html>
