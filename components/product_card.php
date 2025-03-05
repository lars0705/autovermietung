<?php
// 1️⃣ Session nur starten, wenn noch keine existiert
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../components/db_connect.php";

// 2️⃣ Werte aus der URL abrufen
$location = $_GET['location'] ?? '';
$pickup_date = $_GET['pickup_date'] ?? date('Y-m-d', strtotime('+1 day'));
$return_date = $_GET['return_date'] ?? date('Y-m-d', strtotime('+2 days'));

// 3️⃣ Alle Fahrzeuge aus `car_rental_data` abrufen
$sql = "SELECT * FROM car_rental_data WHERE loc_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $location);
$stmt->execute();
$result = $stmt->get_result();

$cars = [];
while ($row = $result->fetch_assoc()) {
    $cars[$row["car_id"]] = $row;
}
$stmt->close();

// 4️⃣ Alle Buchungen aus `bookings` abrufen
$sql = "SELECT car_id, type_id, pickup_date, return_date, car_location FROM bookings WHERE car_location = ? 
        AND (
            (? BETWEEN pickup_date AND return_date) OR
            (? BETWEEN pickup_date AND return_date) OR
            (pickup_date BETWEEN ? AND ?) OR
            (return_date BETWEEN ? AND ?)
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $location, $pickup_date, $return_date, $pickup_date, $return_date, $pickup_date, $return_date);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
$booked_cars = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
    $booked_cars[$row["car_id"]] = $row; // Speichert `car_id` für späteren Vergleich
}
$stmt->close();

// 5️⃣ Berechnung: Verfügbare Fahrzeuge
$available_cars = array_filter($cars, function ($car) use ($booked_cars) {
    return !isset($booked_cars[$car["car_id"]]); // Entfernt gebuchte Autos
});

// 6️⃣ Berechnung: Nicht verfügbare Fahrzeuge (Alle aus `bookings`)
$unavailable_cars = array_intersect_key($cars, $booked_cars);

// 7️⃣ Frames pro `type_id` & `location` erstellen
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

<!-- 1️⃣ Tabelle: Alle verfügbaren Autos -->
<h2>Verfügbare Fahrzeuge</h2>
<table border="1">
    <tr>
        <th>Car ID</th>
        <th>Type ID</th>
        <th>Standort</th>
        <th>Preis</th>
        <th>Name</th>
    </tr>
    <?php foreach ($available_cars as $car): ?>
    <tr>
        <td><?php echo htmlspecialchars($car["car_id"]); ?></td>
        <td><?php echo htmlspecialchars($car["type_id"]); ?></td>
        <td><?php echo htmlspecialchars($car["loc_name"]); ?></td>
        <td><?php echo htmlspecialchars($car["price"]); ?> €</td>
        <td><?php echo htmlspecialchars($car["name"]); ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- 2️⃣ Tabelle: Alle Buchungen -->
<h2>Bestehende Buchungen</h2>
<table border="1">
    <tr>
        <th>Car ID</th>
        <th>Type ID</th>
        <th>Pickup Date</th>
        <th>Return Date</th>
        <th>Standort</th>
    </tr>
    <?php foreach ($bookings as $booking): ?>
    <tr>
        <td><?php echo htmlspecialchars($booking["car_id"]); ?></td>
        <td><?php echo htmlspecialchars($booking["type_id"]); ?></td>
        <td><?php echo htmlspecialchars($booking["pickup_date"]); ?></td>
        <td><?php echo htmlspecialchars($booking["return_date"]); ?></td>
        <td><?php echo htmlspecialchars($booking["car_location"]); ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- 3️⃣ Tabelle: Nicht verfügbare Fahrzeuge -->
<h2>Nicht verfügbare Fahrzeuge</h2>
<table border="1">
    <tr>
        <th>Car ID</th>
        <th>Type ID</th>
        <th>Standort</th>
        <th>Preis</th>
    </tr>
    <?php foreach ($unavailable_cars as $car): ?>
    <tr>
        <td><?php echo htmlspecialchars($car["car_id"]); ?></td>
        <td><?php echo htmlspecialchars($car["type_id"]); ?></td>
        <td><?php echo htmlspecialchars($car["loc_name"]); ?></td>
        <td><?php echo htmlspecialchars($car["price"]); ?> €</td>
    </tr>
    <?php endforeach; ?>
</table>

<?php 
$grouped_cars = [];

foreach ($available_cars as $car) {
    $key = $car['type_id'] . '|' . $car['loc_name'] . '|' . $car['price']; // Eindeutiger Schlüssel

    if (!isset($grouped_cars[$key])) {
        $grouped_cars[$key] = [
            'car_ids' => [],
            'count' => 0,
            'type_id' => $car['type_id'],
            'location' => $car['loc_name'],
            'price' => $car['price'],
            'vendor_name' => $car['vendor_name'],
            'vendor_name_abbr' => $car['vendor_name_abbr'],
            'name' => $car['name'],
            'name_extension' => $car['name_extension'],
            'seats' => $car['seats'],
            'doors' => $car['doors'],
            'gear' => $car['gear'],
            'trunk' => $car['trunk'],
            'air_condition' => $car['air_condition'],
            'gps' => $car['gps'],
            'min_age' => $car['min_age'],
            'type' => $car['type'],
            'drive' => $car['drive'],
        ];
    }

    $grouped_cars[$key]['car_ids'][] = $car['car_id'];
    $grouped_cars[$key]['count'] = count($grouped_cars[$key]['car_ids']);

}
?>

<!-- Fahrzeugkarten -->
<div class="product_card_container">
    <?php if (!empty($grouped_cars)): ?>
        <?php foreach ($grouped_cars as $car): ?>
            <div class="car_frame fade-in">
                <div class="car_image">
                <?php include '../components/load_image.php'; ?>
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
                        <li><strong>Standort:</strong> <?php echo $_GET['location']; ?></li>
                        <li><strong>verfügbar:</strong> <?php echo $car['count']; ?></li>
                        <li><strong>ID:</strong> <?php echo $car['type_id']; ?></li>
                    </ul>

                    <a href="product_detail.php?type_id=<?php echo urlencode($car["type_id"]); ?>&pickup_date=<?php echo urlencode($pickup_date); ?>&return_date=<?php echo urlencode($return_date); ?>" class="more_button">Fahrzeug anzeigen</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Keine Fahrzeuge am gewählten Standort verfügbar.</p>
    <?php endif; ?>
</div>
</body>
</html>
