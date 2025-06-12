<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "polis_merkezi";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

echo "Bağlantı başarılı!";
?>
