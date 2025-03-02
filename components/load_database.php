<?php
// Verhindert unerwünschte Ausgabe vor dem JSON-Header
ob_start();

// Datenbankverbindung
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "car_rental_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    ob_end_clean();
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Filterwerte abrufen
$conditions = [];
if (!empty($_GET['location'])) $conditions[] = "loc_name = '" . $conn->real_escape_string($_GET['location']) . "'";
if (!empty($_GET['category'])) $conditions[] = "type = '" . $conn->real_escape_string($_GET['category']) . "'";
if (!empty($_GET['brand'])) $conditions[] = "vendor_name_abbr = '" . $conn->real_escape_string($_GET['brand']) . "'";
if (!empty($_GET['drivetrain'])) $conditions[] = "drive = '" . $conn->real_escape_string($_GET['drivetrain']) . "'";
if (!empty($_GET['transmission'])) $conditions[] = "gear = '" . $conn->real_escape_string($_GET['transmission']) . "'";
if (!empty($_GET['seats'])) $conditions[] = "seats = " . intval($_GET['seats']);
if (!empty($_GET['doors'])) $conditions[] = "doors = " . intval($_GET['doors']);
if (!empty($_GET['ac'])) $conditions[] = "air_condition = 1";
if (!empty($_GET['gps'])) $conditions[] = "gps = 1";
if (!empty($_GET['min_age'])) $conditions[] = "min_age <= " . intval($_GET['min_age']);
if (!empty($_GET['max_price'])) $conditions[] = "price <= " . intval($_GET['max_price']);

// SQL-Query zusammenbauen
$sql = "SELECT * FROM car_rental_data";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$result = $conn->query($sql);

$cars = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
}

$conn->close();
ob_end_clean();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Fahrzeugübersicht</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }
        .car-frame {
            width: 300px;
            padding: 15px;
            background-color: #222;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
            text-align: center;
        }
        img {
            width: 100%;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<h2>Fahrzeugübersicht</h2>

<div class="container">
    <?php if (!empty($cars)): ?>
        <?php foreach ($cars as $car): ?>
            <div class="car-frame">
                <h3><?php echo htmlspecialchars($car["vendor_name"]) . " " . htmlspecialchars($car["name"]); ?></h3>
                <p>Sitze: <?php echo htmlspecialchars($car["seats"]); ?> | Türen: <?php echo htmlspecialchars($car["doors"]); ?></p>
                <p>Preis: <?php echo htmlspecialchars($car["price"]); ?>€ pro Tag</p>
                <?php if (!empty($car["img_file_name"])): ?>
                    <img src="../images/<?php echo htmlspecialchars($car["img_file_name"]); ?>" alt="Auto">
                <?php else: ?>
                    <p>[Kein Bild verfügbar]</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Keine Fahrzeuge gefunden.</p>
    <?php endif; ?>
</div>

</body>
</html>

