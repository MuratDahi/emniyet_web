<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}


$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) die("Bağlantı hatası: " . $conn->connect_error);


$result_olay = $conn->query("SELECT COUNT(*) AS toplam_olay FROM olaylar");
$toplam_olay = $result_olay->fetch_assoc()['toplam_olay'] ?? 0;


$result_polis = $conn->query("SELECT COUNT(*) AS toplam_polis FROM polisler");
$toplam_polis = $result_polis->fetch_assoc()['toplam_polis'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Ana Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: linear-gradient(135deg, #002147, #003366);
            color: #ffffff;
            min-height: 100vh;
            margin: 0;
            padding-bottom: 70px; /* Alt bar için boşluk */
        }
        h1 {
            color: #ffffff;
            margin-bottom: 30px;
            text-align: center;
        }
        .container {
            display: flex;
            justify-content: center;
            gap: 80px;
            flex-wrap: wrap;
        }
        .block {
            background-color: rgba(255, 255, 255, 0.95);
            color: #003366;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            width: 280px;
            text-align: center;
        }
        .block h2 {
            margin-bottom: 20px;
            color: #003366;
        }
        .block a {
            display: block;
            padding: 12px 0;
            margin: 8px 0;
            background-color: #00509e;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .block a:hover {
            background-color: #003d7a;
        }
        .logout {
            display: block;
            margin: 40px auto 0;
            width: 150px;
            text-align: center;
            padding: 12px;
            background-color: #d9534f;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .logout:hover {
            background-color: #c9302c;
        }
        @media(max-width: 700px) {
            .container {
                flex-direction: column;
                align-items: center;
            }
            .block {
                width: 90%;
                margin-bottom: 20px;
            }
        }

        /* Alt sabit bilgi barı */
        .footer-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #003366;
            color: white;
            font-weight: 600;
            text-align: center;
            padding: 12px 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.3);
            font-size: 1.1rem;
            z-index: 999;
        }
    </style>
</head>
<body>
    <h1>Hoş geldin, <?= htmlspecialchars($_SESSION['kullanici_adi']) ?>!</h1>
    <div class="container">
        <div class="block">
            <h2>Listeleme İşlemleri</h2>
            <a href="listeleme_secimi.php">Tabloları Listele</a>
        </div>
        <div class="block">
            <h2>Ekleme İşlemleri</h2>
            <a href="ekleme_secimi.php">Yeni Kayıt Ekle</a>
        </div>
        <div class="block">
            <h2>Güncelleme İşlemleri</h2>
            <a href="guncelleme_secimi.php">Tablo Seçimi</a>
        </div>
        <div class="block">
            <h2>Silme İşlemleri</h2>
            <a href="silme_secimi.php">Tablo Seçimi</a>
        </div>
        <div class="block">
            <h2>Sorgulama İşlemleri</h2>
            <a href="sorgulama_secimi.php">Tablo Seçimi</a>
        </div>
        <div class="block">
            <h2>İlişkili Sorgulama</h2>
            <a href="iliskili_sorgulama_secim.php">Sorgulama Seçimi</a>
        </div>
    </div>

    <a href="logout.php" class="logout">Çıkış Yap</a>

    <div class="footer-bar">
        Toplam Olay Sayısı: <?= htmlspecialchars($toplam_olay) ?> | Toplam Polis Sayısı: <?= htmlspecialchars($toplam_polis) ?>
    </div>
</body>
</html>
