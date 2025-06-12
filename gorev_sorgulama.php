<?php
session_start();
if (!isset($_SESSION['kullanici_adi'])) {
    header("Location: index.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "polis_merkezi");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$filtre = trim($_GET['polis_adi'] ?? '');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Görev Sorgulama</title>
  <style>
    
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', Tahoma, sans-serif;
      background: #eef2f5;
      color: #333;
      padding: 40px 20px;
    }
    a { color: #007bff; text-decoration: none; }
    a:hover { text-decoration: underline; }

    
    .container {
      max-width: 900px;
      margin: 0 auto;
    }

    
    h1 {
      text-align: center;
      margin-bottom: 30px;
      color: #2c3e50;
    }

    
    .search-card {
      background: #fff;
      padding: 20px 25px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
    }
    .search-card input {
      flex: 1 1 250px;
      padding: 10px 15px;
      border: 1px solid #ccd0d5;
      border-radius: 4px;
      font-size: 16px;
      margin-right: 15px;
    }
    .search-card button {
      padding: 10px 20px;
      background: #28a745;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.2s ease;
    }
    .search-card button:hover {
      background: #218838;
    }

    /* Tablo stili */
    .table-wrapper {
      overflow-x: auto;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 700px;
    }
    th, td {
      padding: 12px 15px;
      text-align: left;
    }
    thead {
      background: #007bff;
      color: #fff;
    }
    tbody tr:nth-child(even) {
      background: #f8f9fa;
    }
    tbody tr:hover {
      background: #e2e6ea;
    }
    th {
      position: sticky;
      top: 0;
    }

    /* Mesaj */
    .message {
      margin: 20px;
      font-size: 16px;
      color: #555;
      text-align: center;
    }

    /* Geri Dön */
    .back-link {
      display: inline-block;
      margin-top: 30px;
      padding: 10px 20px;
      background: #6c757d;
      color: #fff;
      border-radius: 4px;
      transition: background 0.2s ease;
    }
    .back-link:hover {
      background: #5a6268;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Görev Sorgulama</h1>

    <form method="get" class="search-card">
      <input 
        type="text" 
        name="polis_adi" 
        placeholder="Polis adı gir (örn: Murat)" 
        value="<?php echo htmlspecialchars($filtre); ?>">
      <button type="submit">Sorgula</button>
    </form>

<?php
if ($filtre !== '') {
    $sql = "
      SELECT 
        g.gorev_id,
        CONCAT(p.adi, ' ', p.soyadi) AS polis,
        g.olay_id,
        g.tarih,
        g.saat,
        g.arac_id,
        g.malzeme_id
      FROM gorevler AS g
      JOIN polisler AS p ON g.polis_id = p.polis_id
      WHERE LOWER(p.adi) LIKE ? OR LOWER(p.soyadi) LIKE ?
      ORDER BY g.tarih DESC, g.saat DESC
    ";
    $stmt = $conn->prepare($sql);
    $param = '%'.mb_strtolower($filtre,'UTF-8').'%';
    $stmt->bind_param('ss', $param, $param);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        echo '<div class="table-wrapper"><table>';
        echo '<thead><tr>
                <th>Görev ID</th>
                <th>Polis</th>
                <th>Olay ID</th>
                <th>Tarih</th>
                <th>Saat</th>
                <th>Araç ID</th>
                <th>Malzeme ID</th>
              </tr></thead><tbody>';
        while ($row = $res->fetch_assoc()) {
            echo '<tr>';
            echo '<td>'.$row['gorev_id'].'</td>';
            echo '<td>'.htmlspecialchars($row['polis']).'</td>';
            echo '<td>'.$row['olay_id'].'</td>';
            echo '<td>'.$row['tarih'].'</td>';
            echo '<td>'.$row['saat'].'</td>';
            echo '<td>'.$row['arac_id'].'</td>';
            echo '<td>'.$row['malzeme_id'].'</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    } else {
        echo '<p class="message">Hiç görev bulunamadı.</p>';
    }

    $stmt->close();
}
$conn->close();
?>

    <a href="dashboard.php" class="back-link">← Ana Panele Dön</a>
  </div>
</body>
</html>
