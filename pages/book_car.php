<!DOCTYPE html>
<?php
session_start();
require_once "../components/db_connect.php"; 
include '../components/header.php';

// Prüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION["user_id"])) {
    die("Fehler: Sie müssen eingeloggt sein, um eine Buchung vorzunehmen.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user_id"];
    $type_id = intval($_POST["type_id"]);
    $pickup_date = $_POST["pickup_date"];
    $return_date = $_POST["return_date"];
    $location = $_POST["car_location"];

    // Validierung der Datumsangaben
    if (strtotime($pickup_date) >= strtotime($return_date)) {
        die("Fehler: Das Rückgabedatum muss nach dem Abholdatum liegen.");
    }

    // Anzahl der Miettage berechnen
    $pickup = new DateTime($pickup_date);
    $return = new DateTime($return_date);
    $days = $pickup->diff($return)->days;

    if ($days < 1) {
        die("Fehler: Ungültiger Mietzeitraum.");
    }

    // Verfügbare `car_id` für die gegebene `type_id` an der Location suchen
    $stmt = $conn->prepare("
        SELECT car_id, price FROM car_rental_data 
        WHERE type_id = ? AND loc_name = ? 
        AND car_id NOT IN (
            SELECT car_id FROM bookings WHERE (
                (pickup_date <= ? AND return_date >= ?) OR
                (pickup_date <= ? AND return_date >= ?) OR
                (pickup_date >= ? AND return_date <= ?)
            )
        ) LIMIT 1
    ");
    $stmt->bind_param("isssssss", $type_id, $location, $pickup_date, $pickup_date, $return_date, $return_date, $pickup_date, $return_date);
    $stmt->execute();
    $stmt->bind_result($car_id, $price_per_day);
    $stmt->fetch();
    $stmt->close();

    // Falls keine freie `car_id` gefunden wurde
    if (!$car_id) {
        die("Fehler: Kein verfügbares Fahrzeug dieses Typs im gewünschten Zeitraum an diesem Standort.");
    }

    $total_price = $price_per_day * $days;
    $booked_date = date("Y-m-d H:i:s"); // Aktuelle Zeit für die Buchung

    // Buchung in die Datenbank einfügen
    $stmt = $conn->prepare("
        INSERT INTO bookings (user_id, car_id, type_id, pickup_date, return_date, car_location, total_price, booked_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiisssis", $user_id, $car_id, $type_id, $pickup_date, $return_date, $location, $total_price, $booked_date);

    if ($stmt->execute()) {
        // Erfolgreiche Buchung → Weiterleitung zur Buchungsseite mit Erfolgsmeldung
        header("Location: bookings.php?success=true");
        exit();
    } else {
        echo "Fehler bei der Buchung: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
