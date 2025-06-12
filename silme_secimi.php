<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Silme İşlemi Seçimi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 30px;
            color: #333;
        }
        h1 {
            text-align: center;
            margin-bottom: 40px;
            color: #d9534f;
        }
        .liste {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .liste a {
            display: block;
            padding: 15px 20px;
            margin: 10px 0;
            background: #d9534f;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 18px;
            transition: background 0.3s ease;
        }
        .liste a:hover {
            background: #b52b27;
        }
        a.back {
            display: block;
            margin: 30px auto 0;
            text-align: center;
            text-decoration: none;
            color: #d9534f;
            font-weight: 600;
        }
        a.back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Silmek İstediğiniz Tabloyu Seçin</h1>
    <div class="liste">
        <a href="listele_sil.php?tablo=polisler">Polisler</a>
        <a href="listele_sil.php?tablo=araclar">Araçlar</a>
        <a href="listele_sil.php?tablo=malzemeler">Malzemeler</a>
        <a href="listele_sil.php?tablo=olaylar">Olaylar</a>
        <a href="listele_sil.php?tablo=sahislar">Şahıslar</a>
        <a href="listele_sil.php?tablo=gorevler">Görevler</a>
    </div>
    <a href="dashboard.php" class="back">← Ana Panele Dön</a>
</body>
</html>
