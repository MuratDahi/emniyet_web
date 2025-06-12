<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}
if (!isset($_GET['id'])) {
    header("Location: listele_guncelle.php?tablo=sahislar");
    exit;
}

$sahis_id = intval($_GET['id']);
$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) die("Bağlantı hatası: " . $conn->connect_error);


$columns = [];
$colRes = $conn->query("SHOW COLUMNS FROM sahislar");
while ($col = $colRes->fetch_assoc()) {
    if ($col['Field'] !== 'sahis_id') {
        $columns[] = $col['Field'];
    }
}


$stmt = $conn->prepare("SELECT * FROM sahislar WHERE sahis_id = ?");
$stmt->bind_param("i", $sahis_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$data) die('Şahıs bulunamadı.');

$hata = $basarili = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $setParts = [];
    $types = '';
    $values = [];
    foreach ($columns as $col) {
        $val = $_POST[$col] ?? '';
        if ($val === '') {
            $hata = 'Lütfen tüm alanları doldurun.';
            break;
        }
        $setParts[] = "$col = ?";
        $types .= is_numeric($data[$col]) ? 'i' : 's';
        $values[] = $val;
    }
    if (!$hata) {
        $types .= 'i';
        $values[] = $sahis_id;
        $sql = "UPDATE sahislar SET " . implode(', ', $setParts) . " WHERE sahis_id = ?";
        $upd = $conn->prepare($sql);
        $upd->bind_param($types, ...$values);
        if ($upd->execute()) {
            $basarili = 'Şahıs başarıyla güncellendi.';
            header("Refresh:0");
        } else {
            $hata = 'Güncelleme hatası: ' . $upd->error;
        }
        $upd->close();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Şahıs Güncelle</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 30px; }
        form { background: #fff; padding: 20px; border-radius: 8px; max-width: 600px; margin: 0 auto; }
        label { display: block; margin-bottom: 10px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 4px; }
        .error { color: #a94442; font-weight: bold; }
        .success { color: #3c763d; font-weight: bold; }
        button { background: #0074D9; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005fa3; }
        a.back { display: block; text-align: center; margin-top: 20px; color: #0074D9; text-decoration: none; }
    </style>
</head>
<body>
    <h1>Şahıs Güncelle</h1>
    <?php if ($hata): ?><p class="error"><?= htmlspecialchars($hata) ?></p><?php endif; ?>
    <?php if ($basarili): ?><p class="success"><?= htmlspecialchars($basarili) ?></p><?php endif; ?>
    <form method="post">
        <?php foreach ($columns as $col): ?>
            <label><?= htmlspecialchars(ucfirst(str_replace('_',' ',$col))) ?>*:</label>
            <?php if ($col === 'cinsiyet'): ?>
                <select name="cinsiyet" required>
                    <option value="">Seçiniz</option>
                    <option value="Erkek" <?= ($data['cinsiyet'] === 'Erkek') ? 'selected' : '' ?>>Erkek</option>
                    <option value="Kadın" <?= ($data['cinsiyet'] === 'Kadın') ? 'selected' : '' ?>>Kadın</option>
                </select>
            <?php elseif ($col === 'dogum_tarihi'): ?>
                <input type="date" name="dogum_tarihi" required value="<?= htmlspecialchars($_POST['dogum_tarihi'] ?? $data['dogum_tarihi']) ?>">
            <?php else: ?>
                <input type="text" name="<?= htmlspecialchars($col) ?>" value="<?= htmlspecialchars($_POST[$col] ?? $data[$col]) ?>" required>
            <?php endif; ?>
        <?php endforeach; ?>
        <button type="submit">Güncelle</button>
    </form>
    <a href="listele_guncelle.php?tablo=sahislar" class="back">← Geri Dön</a>
</body>
</html>
