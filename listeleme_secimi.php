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
    <title>Listeleme Seçimi</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f0f2f5; color: #333; }
        h1 { margin-bottom: 30px; color: #0074D9; text-align: center; }
        form {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        label, select, button {
            font-size: 18px;
        }
        select {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            outline: none;
        }
        button {
            padding: 12px 30px;
            background: #0074D9;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #005fa3;
        }
        a.back {
            display: block;
            margin-top: 20px;
            color: #0074D9;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
        }
        a.back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Listeleme Seçimi</h1>
    <form action="listele.php" method="get">
        <label for="tablo">Listelemek istediğiniz tabloyu seçin:</label>
        <select name="tablo" id="tablo" required>
            <option value="polisler">Polisler</option>
            <option value="olaylar">Olaylar</option>
            <option value="sahislar">Şahıslar</option>
            <option value="sahis_olay_rol">Şahıs-Olay Rolleri</option>
            <option value="araclar">Araçlar</option>
            <option value="malzemeler">Malzemeler</option>
            <option value="gorevler">Görevler</option>
        </select>
        <button type="submit">Listele</button>
    </form>
    <a href="dashboard.php" class="back">← Ana Panele Dön</a>
</body>
</html>
