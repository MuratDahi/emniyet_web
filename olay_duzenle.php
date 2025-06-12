<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: listele_guncelle.php?tablo=olaylar");
    exit;
}

$olay_id = intval($_GET['id']);
$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) die("Bağlantı hatası: " . $conn->connect_error);

$hata = $basarili = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $olay_tarihi = $_POST['olay_tarihi'] ?? '';
    $saat = $_POST['saat'] ?? '';
    $olay_yeri = $_POST['olay_yeri'] ?? '';
    $olay_tipi = $_POST['olay_tipi'] ?? '';
    $aciklama = $_POST['aciklama'] ?? '';
    $raporlayan_polis_id = $_POST['raporlayan_polis_id'] ?? 0;

    if ($olay_tarihi && $saat && $olay_yeri) {
        $stmt = $conn->prepare("CALL OlayGuncelle(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssi", $olay_id, $olay_tarihi, $saat, $olay_yeri, $olay_tipi, $aciklama, $raporlayan_polis_id);
        if ($stmt->execute()) $basarili = "Olay başarıyla güncellendi.";
        else $hata = "Hata: " . $stmt->error;
        $stmt->close();
    } else {
        $hata = "Zorunlu alanları doldurun.";
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM olaylar WHERE olay_id = ?");
    $stmt->bind_param("i", $olay_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows != 1) die("Olay bulunamadı.");
    $row = $result->fetch_assoc();

    $olay_tarihi = $row['olay_tarihi'];
    $saat = $row['saat'];
    $olay_yeri = $row['olay_yeri'];
    $olay_tipi = $row['olay_tipi'];
    $aciklama = $row['aciklama'];
    $raporlayan_polis_id = $row['raporlayan_polis_id'];

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Olay Düzenle</title>
</head>
<body>
    <h1>Olay Düzenle</h1>
    <?php if ($hata) echo "<p style='color:red;'>$hata</p>"; ?>
    <?php if ($basarili) echo "<p style='color:green;'>$basarili</p>"; ?>

    <form method="post" action="">
        <label>Olay Tarihi*: <input type="date" name="olay_tarihi" value="<?php echo htmlspecialchars($olay_tarihi); ?>" required></label><br><br>
        <label>Saat*: <input type="time" name="saat" value="<?php echo htmlspecialchars($saat); ?>" required></label><br><br>
        <label>Olay Yeri*: <input type="text" name="olay_yeri" value="<?php echo htmlspecialchars($olay_yeri); ?>" required></label><br><br>
        <label>Olay Tipi: <input type="text" name="olay_tipi" value="<?php echo htmlspecialchars($olay_tipi); ?>"></label><br><br>
        <label>Açıklama: <textarea name="aciklama"><?php echo htmlspecialchars($aciklama); ?></textarea></label><br><br>
        <label>Raporlayan Polis ID: <input type="number" name="raporlayan_polis_id" value="<?php echo htmlspecialchars($raporlayan_polis_id); ?>"></label><br><br>

        <button type="submit">Güncelle</button>
    </form>

    <br><a href="listele_guncelle.php?tablo=olaylar">← Olay Listesine Dön</a>
</body>
</html>
