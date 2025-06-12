<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: listele_guncelle.php?tablo=araclar");
    exit;
}

$arac_id = intval($_GET['id']);
$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) die("Bağlantı hatası: " . $conn->connect_error);

$hata = $basarili = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plaka = $_POST['plaka'] ?? '';
    $marka = $_POST['marka'] ?? '';
    $model = $_POST['model'] ?? '';
    $tur = $_POST['tur'] ?? '';
    $durum = $_POST['durum'] ?? '';

    if ($plaka && $marka && $model) {
        $stmt = $conn->prepare("CALL AracGuncelle(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $arac_id, $plaka, $marka, $model, $tur, $durum);
        if ($stmt->execute()) $basarili = "Araç başarıyla güncellendi.";
        else $hata = "Hata: " . $stmt->error;
        $stmt->close();
    } else {
        $hata = "Zorunlu alanları doldurun.";
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM araclar WHERE arac_id = ?");
    $stmt->bind_param("i", $arac_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows != 1) die("Araç bulunamadı.");
    $row = $result->fetch_assoc();

    $plaka = $row['plaka'];
    $marka = $row['marka'];
    $model = $row['model'];
    $tur = $row['tur'];
    $durum = $row['durum'];

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Araç Düzenle</title>
</head>
<body>
    <h1>Araç Düzenle</h1>
    <?php if ($hata) echo "<p style='color:red;'>$hata</p>"; ?>
    <?php if ($basarili) echo "<p style='color:green;'>$basarili</p>"; ?>

    <form method="post" action="">
        <label>Plaka*: <input type="text" name="plaka" value="<?php echo htmlspecialchars($plaka); ?>" required></label><br><br>
        <label>Marka*: <input type="text" name="marka" value="<?php echo htmlspecialchars($marka); ?>" required></label><br><br>
        <label>Model*: <input type="text" name="model" value="<?php echo htmlspecialchars($model); ?>" required></label><br><br>
        <label>Tür: <input type="text" name="tur" value="<?php echo htmlspecialchars($tur); ?>"></label><br><br>
        <label>Durum: 
            <select name="durum">
                <option value="Aktif" <?php if($durum=='Aktif') echo 'selected'; ?>>Aktif</option>
                <option value="Pasif" <?php if($durum=='Pasif') echo 'selected'; ?>>Pasif</option>
            </select>
        </label><br><br>

        <button type="submit">Güncelle</button>
    </form>

    <br><a href="listele_guncelle.php?tablo=araclar">← Araç Listesine Dön</a>
</body>
</html>
