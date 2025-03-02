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
if (!empty($_GET['ac'])) $conditions[] = "air_condition = true";
if (!empty($_GET['gps'])) $conditions[] = "gps = true";
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
    <link rel="stylesheet" href="../css/style.css"> <!-- Externe CSS-Datei -->
</head>
<body>
    <div class="product_card_container">
        <?php if (!empty($cars)): ?>
            <?php foreach ($cars as $index => $car): ?>
                <a href="product_detail.php?
                    id=<?php echo urlencode($car['type_id']); ?>&
                    name=<?php echo urlencode($car['name']); ?>&
                    vendor=<?php echo urlencode($car['vendor_name']); ?>&
                    seats=<?php echo urlencode($car['seats']); ?>&
                    doors=<?php echo urlencode($car['doors']); ?>&
                    price=<?php echo urlencode($car['price']); ?>&
                    image=<?php echo urlencode($car['img_file_name']); ?>"
                   class="car_link fade-in"
                   style="animation-delay: <?php echo ($index * 0.2); ?>s">
                    <div class="car_frame">
                        <div class="car_image">
                        <?php 
                        $imagePath = "../images/" . $car["img_file_name"];
                        if (!empty($car["img_file_name"]) && file_exists($imagePath)): ?>
                            <img src="<?php echo htmlspecialchars($imagePath); ?>">
                        <?php else: ?>
                            <img src="...\images\Placeholder_car.png">
                        <?php endif; ?>
                        </div>
                        <div class="car_info">
                            <h3><?php echo htmlspecialchars($car["vendor_name"]) . " " . htmlspecialchars($car["name"]); ?></h3>
                            <div class="car_details">
                                <p><strong>Type:</strong> <?php echo htmlspecialchars($car["type"]); ?></p>
                                <p><strong>Getriebe:</strong> <?php echo htmlspecialchars($car["gear"]); ?></p>
                            </div>
                            <div class="car_details">
                                <p><strong>Anzahl Sitze:</strong> <?php echo htmlspecialchars($car["seats"]); ?></p>
                                <p><strong>Anzahl Türen:</strong> <?php echo htmlspecialchars($car["doors"]); ?></p>
                            </div>
                            <div class="car_details">
                                <p><strong>Klimaanlage:</strong> <?php echo ($car["air_condition"] == 1) ? "Ja" : "Nein"; ?></p>
                                <p><strong>GPS:</strong> <?php echo ($car["gps"] == 1) ? "Ja" : "Nein"; ?></p>
                            </div>
                            <p class="car_price"><strong><?php echo htmlspecialchars($car["price"]); ?>€ / Tag</strong></p>
                            <button class="book_button">Jetzt buchen</button>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Keine Fahrzeuge gefunden.</p>
        <?php endif; ?>
    </div>
</body>
</html>
