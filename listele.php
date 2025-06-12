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

$tablo = $_GET['tablo'] ?? 'polisler';

switch ($tablo) {
    case 'polisler':
        $prosedur = "TumPolisleriListele";
        $baslik = "Polisler Listesi";
        $sutunlar = ['polis_id', 'adi', 'soyadi', 'sicil_no', 'rutbe', 'telefon', 'kullanici_adi'];
        break;
    case 'olaylar':
        $prosedur = "TumOlaylariListele";
        $baslik = "Olaylar Listesi";
        $sutunlar = ['olay_id', 'raporlayan_polis_id', 'olay_tarihi', 'saat', 'olay_yeri', 'olay_tipi', 'aciklama'];
        break;
    case 'sahislar':
        $prosedur = "TumSahislariListele";
        $baslik = "Şahıslar Listesi";
        $sutunlar = ['sahis_id', 'adi', 'soyadi', 'tc_no', 'dogum_tarihi', 'adres', 'telefon', 'cinsiyet'];
        break;
    case 'sahis_olay_rol':
        $prosedur = "TumSahisOlayRolleriListele";
        $baslik = "Şahıs-Olay Rolleri Listesi";
        $sutunlar = ['id', 'sahis_id', 'olay_id', 'rol', 'ifade'];
        break;
    case 'araclar':
        $prosedur = "TumAraclariListele";
        $baslik = "Araçlar Listesi";
        $sutunlar = ['arac_id', 'plaka', 'marka', 'model', 'tur', 'durum'];
        break;
    case 'malzemeler':
        $prosedur = "TumMalzemeleriListele";
        $baslik = "Malzemeler Listesi";
        $sutunlar = ['malzeme_id', 'malzeme_adi', 'tur', 'miktar', 'kayit_tarihi', 'durum'];
        break;
    case 'gorevler':
        $prosedur = "TumGorevleriListele";
        $baslik = "Görevler Listesi";
        $sutunlar = ['gorev_id', 'polis_id', 'olay_id', 'arac_id', 'malzeme_id', 'tarih', 'saat'];
        break;
    default:
        $prosedur = "TumPolisleriListele";
        $baslik = "Polisler Listesi";
        $sutunlar = ['polis_id', 'adi', 'soyadi', 'sicil_no', 'rutbe', 'telefon', 'kullanici_adi'];
}

$sql = "CALL $prosedur()";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title><?php echo $baslik; ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; padding: 20px;
            background: #f0f2f5;
            color: #333;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #0074D9;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 1000px;
            margin: 0 auto 20px auto;
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
            background-color: #0074D9;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f1f9ff;
        }
        a.back {
            display: inline-block;
            margin: 10px auto;
            text-align: center;
            text-decoration: none;
            color: #0074D9;
            font-weight: 600;
            font-size: 16px;
        }
        a.back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1><?php echo $baslik; ?></h1>
    <table>
        <thead>
            <tr>
                <?php foreach ($sutunlar as $sutun) {
                    echo "<th>" . ucfirst(str_replace('_', ' ', $sutun)) . "</th>";
                } ?>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($sutunlar as $sutun) {
                        echo "<td>" . htmlspecialchars($row[$sutun]) . "</td>";
                    }
                    echo "</tr>";
                }
                $result->close();
                $conn->next_result();
            } else {
                echo "<tr><td colspan='" . count($sutunlar) . "'>Kayıt bulunamadı.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <div style="text-align:center;">
        <a href="listeleme_secimi.php" class="back">← Listeleme Seçimine Dön</a><br>
        <a href="dashboard.php" class="back">← Ana Panele Dön</a>
    </div>
</body>
</html>
