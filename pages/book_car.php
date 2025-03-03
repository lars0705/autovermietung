<!DOCTYPE html>
<?php
include '../components/header.php';
session_start();
require_once "../components/db_connect.php"; 

// Prüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION["user_id"])) {
    echo "Session-ID: " . session_id();
    print_r($_SESSION);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user_id"];
    $car_id = intval($_POST["car_id"]);
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

    // Verfügbarkeitsprüfung für das Fahrzeug
    $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE car_id = ? AND (
        (pickup_date <= ? AND return_date >= ?) OR
        (pickup_date <= ? AND return_date >= ?) OR
        (pickup_date >= ? AND return_date <= ?)
    )");
    $stmt->bind_param("issssss", $car_id, $pickup_date, $pickup_date, $return_date, $return_date, $pickup_date, $return_date);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        die("Fehler: Das Fahrzeug ist im gewählten Zeitraum nicht verfügbar.");
    }

    // Preis pro Tag abrufen
    $stmt = $conn->prepare("SELECT price FROM car_rental_data WHERE type_id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $stmt->bind_result($price_per_day);
    $stmt->fetch();
    $stmt->close();

    $total_price = $price_per_day * $days;

    // Buchung in die Datenbank einfügen
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, car_id, pickup_date, return_date, car_location, total_price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssi", $user_id, $car_id, $pickup_date, $return_date, $location, $total_price);

    if ($stmt->execute()) {
        // Erfolgreiche Buchung → Weiterleitung zur Buchungsseite mit Erfolgsmeldung
        header("Location: bookings.php?success=true");
        exit();
    } else {
        echo "Fehler bei der Buchung.";
    }
    $stmt->close();
}

$conn->close();
?>
