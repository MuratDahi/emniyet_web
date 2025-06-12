<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) die("Bağlantı hatası: " . $conn->connect_error);

$hata = $basarili = '';


$policelist = [];
$res = $conn->query("SELECT polis_id, adi, soyadi FROM polisler");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $policelist[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $olay_tarih = $_POST['olay_tarih'] ?? '';
    $olay_saat = $_POST['olay_saat'] ?? '';
    $olay_yer = $_POST['olay_yer'] ?? '';
    $olay_tur = $_POST['olay_tur'] ?? '';
    $olay_aciklama = $_POST['olay_aciklama'] ?? '';
    $raporlayan_polis_id = $_POST['raporlayan_polis_id'] ?? null;

    $sahis1_adi = $_POST['sahis1_adi'] ?? '';
    $sahis1_soyadi = $_POST['sahis1_soyadi'] ?? '';
    $sahis1_tc_no = $_POST['sahis1_tc_no'] ?? '';
    $sahis1_rol = $_POST['sahis1_rol'] ?? '';
    $sahis1_ifade = $_POST['sahis1_ifade'] ?? '';

   
    $sahis2_adi = $_POST['sahis2_adi'] ?? '';
    $sahis2_soyadi = $_POST['sahis2_soyadi'] ?? '';
    $sahis2_tc_no = $_POST['sahis2_tc_no'] ?? '';
    $sahis2_rol = $_POST['sahis2_rol'] ?? '';
    $sahis2_ifade = $_POST['sahis2_ifade'] ?? '';

    
    if ($olay_tarih && $olay_saat && $olay_yer && $olay_tur && $raporlayan_polis_id &&
        $sahis1_adi && $sahis1_soyadi && $sahis1_tc_no && $sahis1_rol &&
        $sahis2_adi && $sahis2_soyadi && $sahis2_tc_no && $sahis2_rol) {

        
        $stmtCheck = $conn->prepare("SELECT polis_id FROM polisler WHERE polis_id = ?");
        $stmtCheck->bind_param("i", $raporlayan_polis_id);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        if ($resultCheck->num_rows === 0) {
            $hata = "Seçilen raporlayan polis bulunamadı!";
        } else {
            $stmtCheck->close();

            $stmt = $conn->prepare("CALL OlaySahisRolEkle(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "sssssissssssssss",
                $olay_tarih,
                $olay_saat,
                $olay_yer,
                $olay_tur,
                $olay_aciklama,
                $raporlayan_polis_id,
                $sahis1_adi,
                $sahis1_soyadi,
                $sahis1_tc_no,
                $sahis1_rol,
                $sahis1_ifade,
                $sahis2_adi,
                $sahis2_soyadi,
                $sahis2_tc_no,
                $sahis2_rol,
                $sahis2_ifade
            );

            if ($stmt->execute()) {
                $basarili = "Olay ve şahıs bilgileri başarıyla eklendi.";
            } else {
                $hata = "Hata: " . $stmt->error;
            }

            $stmt->close();
        }
    } else {
        $hata = "Lütfen zorunlu alanları doldurun.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Olay ve Şahıs Ekle</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #002147, #003366);
            color: white;
            padding: 30px;
            min-height: 100vh;
            margin: 0;
        }
        h1 {
            margin-bottom: 30px;
            text-align: center;
            font-weight: 700;
            font-size: 2.5rem;
        }
        form {
            background: rgba(0, 20, 70, 0.85);
            padding: 30px;
            border-radius: 15px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 6px 18px rgba(0,0,0,0.6);
        }
        label {
            display: block;
            margin-bottom: 12px;
            font-weight: 600;
        }
        input[type="text"], input[type="date"], input[type="time"], select, textarea {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        button {
            background-color: #0074D9;
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: block;
            margin: 0 auto;
        }
        button:hover {
            background-color: #005fa3;
        }
        .message {
            max-width: 600px;
            margin: 20px auto 0;
            padding: 15px;
            border-radius: 12px;
            font-weight: 700;
            text-align: center;
        }
        .error {
            background-color: #d9534f;
            color: white;
        }
        .success {
            background-color: #5cb85c;
            color: white;
        }
        a.back-link {
            display: block;
            max-width: 600px;
            margin: 30px auto 0;
            text-align: center;
            font-weight: 600;
            font-size: 1rem;
            color: #a9d1ff;
            text-decoration: none;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Olay ve Şahıs Ekle</h1>

    <?php if ($hata): ?>
        <div class="message error"><?= htmlspecialchars($hata) ?></div>
    <?php elseif ($basarili): ?>
        <div class="message success"><?= htmlspecialchars($basarili) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <h3>Olay Bilgileri</h3>
        <label>Tarih*:
            <input type="date" name="olay_tarih" required value="<?= htmlspecialchars($_POST['olay_tarih'] ?? '') ?>">
        </label>

        <label>Saat*:
            <input type="time" name="olay_saat" required value="<?= htmlspecialchars($_POST['olay_saat'] ?? '') ?>">
        </label>

        <label>Olay Yeri*:
            <input type="text" name="olay_yer" required value="<?= htmlspecialchars($_POST['olay_yer'] ?? '') ?>">
        </label>

        <label>Olay Türü*:
            <input type="text" name="olay_tur" required value="<?= htmlspecialchars($_POST['olay_tur'] ?? '') ?>">
        </label>

        <label>Açıklama:
            <textarea name="olay_aciklama"><?= htmlspecialchars($_POST['olay_aciklama'] ?? '') ?></textarea>
        </label>

        <label>Raporlayan Polis*:
            <select name="raporlayan_polis_id" required>
                <option value="">Seçiniz</option>
                <?php foreach ($policelist as $polis): ?>
                    <option value="<?= $polis['polis_id'] ?>"
                        <?= (isset($_POST['raporlayan_polis_id']) && $_POST['raporlayan_polis_id'] == $polis['polis_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($polis['adi'] . ' ' . $polis['soyadi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <h3>Şahıs 1 (Örn: Mağdur)</h3>
        <label>Adı*:
            <input type="text" name="sahis1_adi" required value="<?= htmlspecialchars($_POST['sahis1_adi'] ?? '') ?>">
        </label>

        <label>Soyadı*:
            <input type="text" name="sahis1_soyadi" required value="<?= htmlspecialchars($_POST['sahis1_soyadi'] ?? '') ?>">
        </label>

        <label>TC No*:
            <input type="text" name="sahis1_tc_no" required value="<?= htmlspecialchars($_POST['sahis1_tc_no'] ?? '') ?>">
        </label>

        <label>Rol*:
            <select name="sahis1_rol" required>
                <option value="">Seçiniz</option>
                <option value="Mağdur" <?= (isset($_POST['sahis1_rol']) && $_POST['sahis1_rol'] == 'Mağdur') ? 'selected' : '' ?>>Mağdur</option>
                <option value="Şüpheli" <?= (isset($_POST['sahis1_rol']) && $_POST['sahis1_rol'] == 'Şüpheli') ? 'selected' : '' ?>>Şüpheli</option>
                <option value="Tanık" <?= (isset($_POST['sahis1_rol']) && $_POST['sahis1_rol'] == 'Tanık') ? 'selected' : '' ?>>Tanık</option>
            </select>
        </label>

        <label>İfade:
            <textarea name="sahis1_ifade"><?= htmlspecialchars($_POST['sahis1_ifade'] ?? '') ?></textarea>
        </label>

        <h3>Şahıs 2 (Örn: Şüpheli)</h3>
        <label>Adı*:
            <input type="text" name="sahis2_adi" required value="<?= htmlspecialchars($_POST['sahis2_adi'] ?? '') ?>">
        </label>

        <label>Soyadı*:
            <input type="text" name="sahis2_soyadi" required value="<?= htmlspecialchars($_POST['sahis2_soyadi'] ?? '') ?>">
        </label>

        <label>TC No*:
            <input type="text" name="sahis2_tc_no" required value="<?= htmlspecialchars($_POST['sahis2_tc_no'] ?? '') ?>">
        </label>

        <label>Rol*:
            <select name="sahis2_rol" required>
                <option value="">Seçiniz</option>
                <option value="Mağdur" <?= (isset($_POST['sahis2_rol']) && $_POST['sahis2_rol'] == 'Mağdur') ? 'selected' : '' ?>>Mağdur</option>
                <option value="Şüpheli" <?= (isset($_POST['sahis2_rol']) && $_POST['sahis2_rol'] == 'Şüpheli') ? 'selected' : '' ?>>Şüpheli</option>
                <option value="Tanık" <?= (isset($_POST['sahis2_rol']) && $_POST['sahis2_rol'] == 'Tanık') ? 'selected' : '' ?>>Tanık</option>
            </select>
        </label>

        <label>İfade:
            <textarea name="sahis2_ifade"><?= htmlspecialchars($_POST['sahis2_ifade'] ?? '') ?></textarea>
        </label>

        <button type="submit">Ekle</button>
    </form>

    <a href="ekleme_secimi.php" style="
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #0074D9;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    ">
        ← Ekleme Seçimine Dön
    </a>
</body>
</html>
