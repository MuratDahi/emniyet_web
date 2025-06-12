<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) die("Bağlantı hatası: " . $conn->connect_error);

$olay_id = $_GET['olay_id'] ?? null;
$sahis_id = $_GET['sahis_id'] ?? null;

if ($olay_id) {
    $stmt = $conn->prepare("
        SELECT s.sahis_id, s.adi, s.soyadi, sor.rol, sor.ifade
        FROM sahislar s
        JOIN sahis_olay_rol sor ON s.sahis_id = sor.sahis_id
        WHERE sor.olay_id = ?
    ");
    $stmt->bind_param("i", $olay_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Olay ID $olay_id ile ilişkili şahıslar</h2>";
    if ($result->num_rows === 0) {
        echo "Kayıt bulunamadı.";
    } else {
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Adı</th><th>Soyadı</th><th>Rol</th><th>İfade</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['sahis_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['adi']) . "</td>";
            echo "<td>" . htmlspecialchars($row['soyadi']) . "</td>";
            echo "<td>" . htmlspecialchars($row['rol']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ifade']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    $stmt->close();

} elseif ($sahis_id) {
    $stmt = $conn->prepare("
        SELECT o.olay_id, o.olay_tarihi, o.saat, o.olay_yeri, o.olay_tipi, sor.rol, sor.ifade
        FROM olaylar o
        JOIN sahis_olay_rol sor ON o.olay_id = sor.olay_id
        WHERE sor.sahis_id = ?
    ");
    $stmt->bind_param("i", $sahis_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Şahıs ID $sahis_id ile ilişkili olaylar</h2>";
    if ($result->num_rows === 0) {
        echo "Kayıt bulunamadı.";
    } else {
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Tarih</th><th>Saat</th><th>Yer</th><th>Tür</th><th>Rol</th><th>İfade</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['olay_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['olay_tarihi']) . "</td>";
            echo "<td>" . htmlspecialchars($row['saat']) . "</td>";
            echo "<td>" . htmlspecialchars($row['olay_yeri']) . "</td>";
            echo "<td>" . htmlspecialchars($row['olay_tipi']) . "</td>";
            echo "<td>" . htmlspecialchars($row['rol']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ifade']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    $stmt->close();

} else {
    echo "Lütfen Olay ID veya Şahıs ID giriniz.";
}

$conn->close();
?>
