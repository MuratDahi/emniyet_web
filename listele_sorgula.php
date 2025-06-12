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
if ($conn->connect_error) die("Bağlantı hatası: " . $conn->connect_error);

$tablo = $_GET['tablo'] ?? 'polisler';

$baslik = '';
$sutunlar = [];
$whereClauses = [];
$params = [];
$paramTypes = '';
$formHtml = '';

switch ($tablo) {
    case 'polisler':
        $baslik = "Polisler";
        $sutunlar = ['polis_id', 'adi', 'soyadi', 'sicil_no', 'rutbe', 'telefon', 'kullanici_adi'];

        $adi = $_GET['adi'] ?? '';
        $soyadi = $_GET['soyadi'] ?? '';
        $tc_no = $_GET['tc_no'] ?? '';

        if ($adi !== '') {
            $whereClauses[] = "adi LIKE ?";
            $params[] = "%$adi%";
            $paramTypes .= 's';
        }
        if ($soyadi !== '') {
            $whereClauses[] = "soyadi LIKE ?";
            $params[] = "%$soyadi%";
            $paramTypes .= 's';
        }
        if ($tc_no !== '') {
            $whereClauses[] = "sicil_no LIKE ?";
            $params[] = "%$tc_no%";
            $paramTypes .= 's';
        }

        $formHtml = '
        <form method="get" action="">
            <input type="hidden" name="tablo" value="polisler" />
            <input type="text" name="adi" placeholder="Adı" value="'.htmlspecialchars($adi).'" />
            <input type="text" name="soyadi" placeholder="Soyadı" value="'.htmlspecialchars($soyadi).'" />
            <input type="text" name="tc_no" placeholder="Sicil No" value="'.htmlspecialchars($tc_no).'" />
            <button type="submit">Ara</button>
        </form>';
        break;

    case 'araclar':
        $baslik = "Araçlar";
        $sutunlar = ['arac_id', 'plaka', 'marka', 'model', 'tur', 'durum'];

        $plaka = $_GET['plaka'] ?? '';

        if ($plaka !== '') {
            $whereClauses[] = "plaka LIKE ?";
            $params[] = "%$plaka%";
            $paramTypes .= 's';
        }

        $formHtml = '
        <form method="get" action="">
            <input type="hidden" name="tablo" value="araclar" />
            <input type="text" name="plaka" placeholder="Plaka" value="'.htmlspecialchars($plaka).'" />
            <button type="submit">Ara</button>
        </form>';
        break;

    case 'malzemeler':
        $baslik = "Malzemeler";
        $sutunlar = ['malzeme_id', 'malzeme_adi', 'tur', 'miktar', 'kayit_tarihi', 'durum'];

        $malzeme_adi = $_GET['malzeme_adi'] ?? '';

        if ($malzeme_adi !== '') {
            $whereClauses[] = "malzeme_adi LIKE ?";
            $params[] = "%$malzeme_adi%";
            $paramTypes .= 's';
        }

        $formHtml = '
        <form method="get" action="">
            <input type="hidden" name="tablo" value="malzemeler" />
            <input type="text" name="malzeme_adi" placeholder="Malzeme Adı" value="'.htmlspecialchars($malzeme_adi).'" />
            <button type="submit">Ara</button>
        </form>';
        break;

    case 'olaylar':
        $baslik = "Olaylar";
        $sutunlar = ['olay_id', 'raporlayan_polis_id', 'olay_tarihi', 'saat', 'olay_yeri', 'olay_tipi', 'aciklama'];

        $olay_tarihi = $_GET['olay_tarihi'] ?? '';

        if ($olay_tarihi !== '') {
            $whereClauses[] = "olay_tarihi = ?";
            $params[] = $olay_tarihi;
            $paramTypes .= 's';
        }

        $formHtml = '
        <form method="get" action="">
            <input type="hidden" name="tablo" value="olaylar" />
            <input type="date" name="olay_tarihi" placeholder="Olay Tarihi" value="'.htmlspecialchars($olay_tarihi).'" />
            <button type="submit">Ara</button>
        </form>';
        break;

    case 'sahislar':
        $baslik = "Şahıslar";
        $sutunlar = ['sahis_id', 'adi', 'soyadi', 'tc_no', 'dogum_tarihi', 'adres', 'telefon', 'cinsiyet'];

        $adi = $_GET['adi'] ?? '';
        $soyadi = $_GET['soyadi'] ?? '';

        if ($adi !== '') {
            $whereClauses[] = "adi LIKE ?";
            $params[] = "%$adi%";
            $paramTypes .= 's';
        }
        if ($soyadi !== '') {
            $whereClauses[] = "soyadi LIKE ?";
            $params[] = "%$soyadi%";
            $paramTypes .= 's';
        }

        $formHtml = '
        <form method="get" action="">
            <input type="hidden" name="tablo" value="sahislar" />
            <input type="text" name="adi" placeholder="Adı" value="'.htmlspecialchars($adi).'" />
            <input type="text" name="soyadi" placeholder="Soyadı" value="'.htmlspecialchars($soyadi).'" />
            <button type="submit">Ara</button>
        </form>';
        break;

    default:
        die("Geçersiz tablo seçimi.");
}

// SQL oluştur
$sql = "SELECT " . implode(", ", $sutunlar) . " FROM $tablo";
if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Sorgu hazırlanamadı: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title><?php echo $baslik; ?> - Sorgulama</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; padding: 20px;
            background: #f0f2f5;
            color: #333;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #28a745;
        }
        form {
            max-width: 600px;
            margin: 0 auto 30px auto;
            text-align: center;
        }
        input[type="text"], input[type="date"] {
            width: 30%;
            padding: 8px 12px;
            font-size: 16px;
            border: 2px solid #28a745;
            border-radius: 8px;
            outline: none;
            margin: 0 8px 10px 0;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus, input[type="date"]:focus {
            border-color: #1e7e34;
        }
        button {
            padding: 9px 18px;
            font-size: 16px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #1e7e34;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #28a745;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background-color: #e6f9e6;
        }
        a.back {
            display: inline-block;
            margin: 20px auto 0 auto;
            text-align: center;
            text-decoration: none;
            color: #28a745;
            font-weight: 600;
        }
        a.back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1><?php echo $baslik; ?> Tablosunda Sorgulama</h1>

    <?php echo $formHtml; ?>

    <table>
        <thead>
            <tr>
                <?php foreach ($sutunlar as $sutun): ?>
                    <th><?php echo ucfirst(str_replace('_', ' ', $sutun)); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <?php foreach ($sutunlar as $sutun): ?>
                            <td><?php echo htmlspecialchars($row[$sutun]); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="<?php echo count($sutunlar); ?>">Kayıt bulunamadı.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="text-align:center;">
        <a href="sorgulama_secimi.php" class="back">← Tablo Seçimine Dön</a><br>
        <a href="dashboard.php" class="back">← Ana Panele Dön</a>
    </div>
</body>
</html>
