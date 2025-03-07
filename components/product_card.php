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

$doors = !empty($_GET['doors']) ? $_GET['doors'] : null;
$seats = !empty($_GET['seats']) ? $_GET['seats'] : null;
$min_age = !empty($_GET['min_age']) ? $_GET['min_age'] : null;
$ac = isset($_GET['ac']) ? 1 : null;
$gps = isset($_GET['gps']) ? 1 : null;
$category = !empty($_GET['category']) ? $_GET['category'] : null;
$brand = !empty($_GET['brand']) ? $_GET['brand'] : null;
$max_price = (!empty($_GET['max_price']) && $_GET['max_price'] != "0") ? $_GET['max_price'] : null;
$drivetrain = !empty($_GET['drivetrain']) ? $_GET['drivetrain'] : null;
$transmission = !empty($_GET['transmission']) ? $_GET['transmission'] : null;
$order = $_GET['order'] ?? 'asc';



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
<?php 

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

// Anzahl der Fahrzeuge pro Seite
$vehicles_per_page = 10;

// Aktuelle Seite aus der URL holen (Standard: 1)
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Berechnung des Offsets für die SQL-Abfrage
$offset = ($current_page - 1) * $vehicles_per_page;

// Gesamtanzahl der Fahrzeuge ermitteln
$total_vehicles = count($grouped_cars);
$total_pages = ceil($total_vehicles / $vehicles_per_page);

// Fahrzeuge für die aktuelle Seite extrahieren
$displayed_cars = array_slice($grouped_cars, $offset, $vehicles_per_page, true);
?>

<?php if (isset($_GET['error']) && $_GET['error'] === "unavailable"): ?>
    <p class="error_message">⚠️ Leider ist kein Fahrzeug für den gewählten Zeitraum verfügbar. Bitte wähle ein anderes Datum oder einen anderen Standort.</p>
<?php endif; ?>

<!-- Fahrzeugkarten -->
<div class="product_card_container">
    <?php if (!empty($displayed_cars)): ?>
        <?php foreach ($displayed_cars as $car): ?>
            <?php if (
            ($doors === null || ($car["doors"] ?? null) == $doors) &&
            ($seats === null || ($car["seats"] ?? null) == $seats) &&
            ($min_age === null || ($car["min_age"] ?? null) <= $min_age) &&
            ($ac === null || ($car["air_condition"] ?? null) == 1) &&  
            ($gps === null || ($car["gps"] ?? null) == 1) &&  
            ($category === null || stripos(strtolower($car["type"] ?? ''), strtolower($category)) !== false) &&
            ($brand === null || stripos(strtolower($car["vendor_name"] ?? ''), strtolower($brand)) !== false) &&
            ($max_price === null || ($car["price"] ?? null) <= $max_price) &&
            ($drivetrain === null || stripos(strtolower($car["drive"] ?? ''), strtolower($drivetrain)) !== false) &&
            ($transmission === null || stripos(strtolower($car["gear"] ?? ''), strtolower($transmission)) !== false)
            ): ?>
            <div class="car_frame fade-in">
                <div class="car_image">
                <?php include '../components/load_image.php'; ?>
                </div>
                <div class="car_info">
                    <h3><?php echo htmlspecialchars($car["vendor_name"]) . " " . htmlspecialchars($car["name"]); ?></h3>
                    <p class="car_price"><strong><?php echo number_format($car["price"], 2, ',', '.'); ?>€ / Tag</strong></p>
                    
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
                    <a href="product_detail.php?type_id=<?php echo urlencode($car["type_id"]); ?>&pickup_date=<?php echo urlencode($pickup_date); ?>&return_date=<?php echo urlencode($return_date); ?>&count=<?php echo urlencode($car['count']); ?>" class="more_button">Fahrzeug anzeigen</a>
                    </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Keine Fahrzeuge am gewählten Standort verfügbar.</p>
    <?php endif; ?>
</div>
<!-- Paging-Navigation -->
<?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" class="prev_button">« Zurück</a>
        <?php endif; ?>

        <span>Seite <?php echo $current_page; ?> von <?php echo $total_pages; ?></span>

        <?php if ($current_page < $total_pages): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" class="next_button">Weiter »</a>
        <?php endif; ?>
    </div>
<?php endif; ?>


</body>
</html>
