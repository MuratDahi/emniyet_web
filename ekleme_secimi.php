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
    <title>Sisteme Yeni Kayıt Ekle</title>
    <style>
        
        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            color: white;
            background: 
                linear-gradient(rgba(0,0,50,0.7), rgba(0,0,50,0.7)),
                url('images/police-background.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            margin-bottom: 40px;
            font-size: 3rem;
            font-weight: 700;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
        }

        .liste {
            background: rgba(0, 20, 70, 0.8);
            padding: 40px 60px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.8);
            width: 100%;
            max-width: 480px;
            text-align: center;
        }

        .liste a {
            display: block;
            padding: 18px 0;
            margin: 15px 0;
            background: #0074D9;
            color: white;
            font-size: 1.3rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 12px;
            transition: background 0.3s ease;
            box-shadow: 0 6px 15px rgba(0, 116, 217, 0.6);
        }

        .liste a:hover {
            background: #005fa3;
            box-shadow: 0 8px 20px rgba(0, 95, 163, 0.9);
        }

        a.back {
            margin-top: 35px;
            font-size: 1.1rem;
            color: #a9d1ff;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }

        a.back:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 2.2rem;
                margin-bottom: 30px;
            }
            .liste {
                padding: 30px 20px;
                max-width: 90%;
            }
            .liste a {
                font-size: 1.1rem;
                padding: 15px 0;
            }
        }
    </style>
</head>
<body>
    <h1>Sisteme Yeni Kayıt Ekle</h1>
    <div class="liste">
        <a href="polis_ekle.php">Polis Ekle</a>
        <a href="arac_ekle.php">Araç Ekle</a>
        <a href="malzeme_ekle.php">Malzeme Ekle</a>
        <a href="olay_ekle.php">Olay Ekle</a>
        <a href="sahis_ekle.php">Şahıs Ekle</a>
        <a href="gorev_ekle.php">Görev Ekle</a>  <!-- Yeni eklenen link burası -->
    </div>
    <a href="dashboard.php" class="back">← Ana Panele Dön</a>
</body>
</html>
