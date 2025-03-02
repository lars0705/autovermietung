<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="../css/style.css" />
  </head>
  <body>
    <?php include '../components/header.php'; ?>
    <?php
// Fahrzeugdaten aus URL abrufen
$id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : "Unbekannt";
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : "Unbekannt";
$vendor = isset($_GET['vendor']) ? htmlspecialchars($_GET['vendor']) : "Unbekannt";
$seats = isset($_GET['seats']) ? htmlspecialchars($_GET['seats']) : "Unbekannt";
$doors = isset($_GET['doors']) ? htmlspecialchars($_GET['doors']) : "Unbekannt";
$price = isset($_GET['price']) ? htmlspecialchars($_GET['price']) : "Unbekannt";
$image = isset($_GET['image']) ? htmlspecialchars($_GET['image']) : "";
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Produktdetails</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .detail-container {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            background-color: #222;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
            text-align: center;
        }
        img {
            width: 100%;
            border-radius: 5px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #444;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-link:hover {
            background-color: #666;
        }
    </style>
</head>
<body>

<h2>Fahrzeugdetails</h2>

<div class="detail-container">
    <h3><?php echo $vendor . " " . $name; ?></h3>
    <p><strong>Sitze:</strong> <?php echo $seats; ?></p>
    <p><strong>Türen:</strong> <?php echo $doors; ?></p>
    <p><strong>Preis:</strong> <?php echo $price; ?>€ pro Tag</p>
    <?php if (!empty($image)): ?>
        <img src="../images/<?php echo $image; ?>" alt="Fahrzeugbild">
    <?php else: ?>
        <p>[Kein Bild verfügbar]</p>
    <?php endif; ?>
    
    <a href="product_list.php" class="back-link">Zurück zur Fahrzeugübersicht</a>
</div>

</body>
</html>

    <?php include '../components/footer.php'; ?>
  </body>
</html>
