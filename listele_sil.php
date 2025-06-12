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

switch ($tablo) {
    case 'polisler':
        $prosedur = "TumPolisleriListele";
        $baslik = "Polisler Listesi";
        $sutunlar = ['polis_id', 'adi', 'soyadi', 'sicil_no', 'rutbe', 'telefon', 'kullanici_adi'];
        $idAlan = 'polis_id';
        $silSayfa = 'polis_sil.php';
        break;
    case 'olaylar':
        $prosedur = "TumOlaylariListele";
        $baslik = "Olaylar Listesi";
        $sutunlar = ['olay_id', 'raporlayan_polis_id', 'olay_tarihi', 'saat', 'olay_yeri', 'olay_tipi', 'aciklama'];
        $idAlan = 'olay_id';
        $silSayfa = 'olay_sil.php';
        break;
    case 'sahislar':
        $prosedur = "TumSahislariListele";
        $baslik = "Şahıslar Listesi";
        $sutunlar = ['sahis_id', 'adi', 'soyadi', 'tc_no', 'dogum_tarihi', 'adres', 'telefon', 'cinsiyet'];
        $idAlan = 'sahis_id';
        $silSayfa = 'sahis_sil.php';
        break;
    case 'araclar':
        $prosedur = "TumAraclariListele";
        $baslik = "Araçlar Listesi";
        $sutunlar = ['arac_id', 'plaka', 'marka', 'model', 'tur', 'durum'];
        $idAlan = 'arac_id';
        $silSayfa = 'arac_sil.php';
        break;
    case 'malzemeler':
        $prosedur = "TumMalzemeleriListele";
        $baslik = "Malzemeler Listesi";
        $sutunlar = ['malzeme_id', 'malzeme_adi', 'tur', 'miktar', 'kayit_tarihi', 'durum'];
        $idAlan = 'malzeme_id';
        $silSayfa = 'malzeme_sil.php';
        break;
    case 'gorevler':
        $prosedur = "TumGorevleriListele";
        $baslik = "Görevler Listesi";
        $sutunlar = ['gorev_id', 'polis_id', 'olay_id', 'arac_id', 'malzeme_id', 'tarih', 'saat'];
        $idAlan = 'gorev_id';
        $silSayfa = 'gorev_sil.php';
        break;
    default:
        $prosedur = "TumPolisleriListele";
        $baslik = "Polisler Listesi";
        $sutunlar = ['polis_id', 'adi', 'soyadi', 'sicil_no', 'rutbe', 'telefon', 'kullanici_adi'];
        $idAlan = 'polis_id';
        $silSayfa = 'polis_sil.php';
}

$sql = "CALL $prosedur()";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title><?php echo $baslik; ?> - Sil</title>
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
            color: #d9534f;
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
            background-color: #d9534f;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background-color: #fef0f0;
        }
        a.back {
            display: inline-block;
            margin: 10px auto;
            text-align: center;
            text-decoration: none;
            color: #d9534f;
            font-weight: 600;
            font-size: 16px;
        }
        a.back:hover {
            text-decoration: underline;
        }
        a.sil-btn {
            color: white;
            background-color: #d9534f;
            padding: 6px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        a.sil-btn:hover {
            background-color: #b52b27;
        }
    </style>
</head>
<body>
    <h1><?php echo $baslik; ?> - Sil</h1>
    <table>
        <thead>
            <tr>
                <?php foreach ($sutunlar as $sutun) {
                    echo "<th>" . ucfirst(str_replace('_', ' ', $sutun)) . "</th>";
                } ?>
                <th>İşlem</th>
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
                    echo "<td><a href='{$silSayfa}?id={$row[$idAlan]}' class='sil-btn' onclick=\"return confirm('Silmek istediğine emin misin?');\">Sil</a></td>";
                    echo "</tr>";
                }
                $result->close();
                $conn->next_result();
            } else {
                echo "<tr><td colspan='" . (count($sutunlar)+1) . "'>Kayıt bulunamadı.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <div style="text-align:center;">
        <a href="silme_secimi.php" class="back">← Silme Tablosu Seçimine Dön</a><br>
        <a href="dashboard.php" class="back">← Ana Panele Dön</a>
    </div>
</body>
</html>
