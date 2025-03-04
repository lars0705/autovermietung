<?php
// Verhindert unerwünschte Ausgabe vor dem JSON-Header
ob_start();

require_once "../components/db_connect.php";

// Standard-Sortierung
$sort_order = $_GET['order'] ?? 'asc';

// Filterwerte abrufen
$conditions = ["type_id NOT IN (SELECT car_id FROM bookings WHERE CURDATE() BETWEEN pickup_date AND return_date)"];

if (!empty($_GET['location'])) {
    $conditions[] = "loc_name = ?";
}
if (!empty($_GET['category'])) {
    $conditions[] = "type = ?";
}
if (!empty($_GET['brand'])) {
    $conditions[] = "vendor_name_abbr = ?";
}
if (!empty($_GET['drivetrain'])) {
    $conditions[] = "drive = ?";
}
if (!empty($_GET['transmission'])) {
    $conditions[] = "gear = ?";
}
if (!empty($_GET['seats'])) {
    $conditions[] = "seats = ?";
}
if (!empty($_GET['doors'])) {
    $conditions[] = "doors = ?";
}
if (!empty($_GET['ac'])) {
    $conditions[] = "air_condition = true";
}
if (!empty($_GET['gps'])) {
    $conditions[] = "gps = true";
}
if (!empty($_GET['min_age'])) {
    $conditions[] = "min_age <= ?";
}
if (!empty($_GET['max_price'])) {
    $conditions[] = "price <= ?";
}

// SQL-Abfrage mit sicheren Parametern aufbauen
$sql = "SELECT * FROM car_rental_data";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Nur ein Auto pro Typ anzeigen
$sql .= " GROUP BY type_id";

// Sortierung nach Preis
$sql .= ($sort_order === 'asc') ? " ORDER BY price ASC" : " ORDER BY price DESC";

$stmt = $conn->prepare($sql);

// Parameter dynamisch binden
$params = [];
if (!empty($_GET['location'])) $params[] = $_GET['location'];
if (!empty($_GET['category'])) $params[] = $_GET['category'];
if (!empty($_GET['brand'])) $params[] = $_GET['brand'];
if (!empty($_GET['drivetrain'])) $params[] = $_GET['drivetrain'];
if (!empty($_GET['transmission'])) $params[] = $_GET['transmission'];
if (!empty($_GET['seats'])) $params[] = $_GET['seats'];
if (!empty($_GET['doors'])) $params[] = $_GET['doors'];
if (!empty($_GET['min_age'])) $params[] = $_GET['min_age'];
if (!empty($_GET['max_price'])) $params[] = $_GET['max_price'];

if (!empty($params)) {
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$cars = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
}

$stmt->close();
$conn->close();
ob_end_clean();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Fahrzeugübersicht</title>
    <link rel="stylesheet" href="../css/style_product_list.css">
</head>
<body>

<!-- Sortieroptionen -->
<div class="sort-container">
    <label for="sort">Sortieren nach:</label>
    <select id="sort" onchange="sortCars()">
        <option value="asc" <?php echo ($sort_order === 'asc') ? 'selected' : ''; ?>>Preis: Aufsteigend</option>
        <option value="desc" <?php echo ($sort_order === 'desc') ? 'selected' : ''; ?>>Preis: Absteigend</option>
    </select>
</div>

<div class="product_card_container">
    <?php if (!empty($cars)): ?>
        <?php foreach ($cars as $index => $car): ?>
            <div class="car_frame fade-in" style="animation-delay: <?php echo ($index * 0.2); ?>s">
                <div class="car_image">
                    <?php 
                    $imagePath = "../assets/images/cars/" . $car["type_id_" . $car["type_id"]];
                    if (file_exists($imagePath)): ?>
                        <img src="<?php echo htmlspecialchars($imagePath); ?>">
                    <?php else: ?>
                        <img src="../assets/images/Placeholder_car.png">
                    <?php endif; ?>
                </div>
                <div class="car_info">
                    <h3><?php echo htmlspecialchars($car["vendor_name"]) . " " . htmlspecialchars($car["name"]); ?></h3>
                    <p class="car_price"><strong><?php echo htmlspecialchars($car["price"]); ?>€ / Tag</strong></p>
                    
                    <ul class="car_details">
                        <li><strong>Kategorie:</strong> <?php echo htmlspecialchars($car["type"]); ?></li>
                        <li><strong>Antrieb:</strong> <?php echo htmlspecialchars($car["drive"]); ?></li>
                        <li><strong>Getriebe:</strong> <?php echo htmlspecialchars($car["gear"]); ?></li>
                        <li><strong>Sitzplätze:</strong> <?php echo htmlspecialchars($car["seats"]); ?></li>
                        <li><strong>Türen:</strong> <?php echo htmlspecialchars($car["doors"]); ?></li>
                        <li><strong>Klimaanlage:</strong> <?php echo $car["air_condition"] ? "Ja" : "Nein"; ?></li>
                        <li><strong>GPS:</strong> <?php echo $car["gps"] ? "Ja" : "Nein"; ?></li>
                    </ul>

                    <a href="product_detail.php?id=<?php echo urlencode($car['type_id']); ?>&pickup_date=<?php echo urlencode($_GET['pickup_date'] ?? ''); ?>&return_date=<?php echo urlencode($_GET['return_date'] ?? ''); ?>" class="more_button">Mehr erfahren</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Keine Fahrzeuge verfügbar.</p>
    <?php endif; ?>
</div>

<script>
function sortCars() {
    let sortOrder = document.getElementById("sort").value;
    let urlParams = new URLSearchParams(window.location.search);
    urlParams.set('order', sortOrder);
    window.location.search = urlParams.toString();
}
</script>

</body>
</html>
