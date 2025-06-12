<?php
session_start(); 


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "polis_merkezi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}


$kullanici_adi = $_POST['kullanici_adi'] ?? '';
$sifre = $_POST['sifre'] ?? '';

if ($kullanici_adi == '' || $sifre == '') {
    echo "Kullanıcı adı ve şifre zorunludur.";
    exit;
}


$stmt = $conn->prepare("SELECT * FROM polisler WHERE kullanici_adi = ? AND sifre = ?");
$stmt->bind_param("ss", $kullanici_adi, $sifre);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    
    $_SESSION['kullanici_adi'] = $kullanici_adi;
    header("Location: dashboard.php"); 
    exit;
} else {
    echo "Kullanıcı adı veya şifre yanlış.";
}

$stmt->close();
$conn->close();
?>
