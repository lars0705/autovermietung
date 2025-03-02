<?php
// Verhindert unerwünschte Ausgabe vor dem JSON-Header
ob_start();
header('Content-Type: application/json');

// Datenbankverbindung
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "car_rental_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    ob_end_clean();
    die(json_encode(["error" => "Verbindung fehlgeschlagen: " . $conn->connect_error]));
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

// Stellt sicher, dass nichts außer JSON gesendet wird
ob_end_clean();
echo json_encode($cars);
?>
