<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Polis Giriş</title>
    <style>
        
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('police-background.jpg'); /* Bu dosyayı projenin içine koyacağız */
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        
        .login-box {
            background-color: rgba(0,0,0,0.7);
            padding: 30px;
            border-radius: 10px;
            width: 320px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
            color: white;
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: normal;
        }

        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 12px 10px;
            margin: 8px 0 20px 0;
            border: none;
            border-radius: 5px;
        }

        .login-box input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #0074D9;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .login-box input[type="submit"]:hover {
            background-color: #005fa3;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Polis Giriş</h2>
        <form action="login.php" method="post">
            <input type="text" name="kullanici_adi" placeholder="Kullanıcı Adı" required />
            <input type="password" name="sifre" placeholder="Şifre" required />
            <input type="submit" value="Giriş Yap" />
        </form>
    </div>
</body>
</html>
