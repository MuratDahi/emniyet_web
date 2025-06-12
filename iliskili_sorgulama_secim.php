<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $secim = $_POST['secim'] ?? '';

    if ($secim === 'sahis_ismi_ile_ara') {
        header("Location: sahis_olaylari_ara.php");
        exit;
    } elseif ($secim === 'malzeme_turune_gore') {
        header("Location: malzeme_turune_gore.php");
        exit;
    } else {
        $error = "Lütfen geçerli bir seçenek seçin.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>İlişkili Sorgulama Seçimi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 50px;
            text-align: center;
        }
        h1 {
            color: #0074D9;
            margin-bottom: 30px;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: inline-block;
        }
        select {
            padding: 10px 15px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 320px;
            margin-bottom: 20px;
        }
        button {
            padding: 12px 30px;
            font-size: 16px;
            background: #0074D9;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
        }
        button:hover {
            background: #005fa3;
        }
        .error {
            color: red;
            font-weight: 600;
            margin-bottom: 15px;
        }
        a.back {
            display: block;
            margin-top: 25px;
            color: #0074D9;
            text-decoration: none;
            font-weight: 600;
        }
        a.back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>İlişkili Sorgulama Seçimi</h1>

    <form method="post" action="">
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <select name="secim" required>
            <option value="">-- Lütfen Sorgulama Türünü Seçin --</option>
            <option value="sahis_ismi_ile_ara">Şahıs İsmiyle Olay Ara</option>
            <option value="malzeme_turune_gore">Malzeme Türüne Göre Miktarlar</option>
        </select>

        <button type="submit">Devam Et</button>
    </form>

    <a href="dashboard.php" class="back">← Ana Panele Dön</a>
</body>
</html>
