<?php
session_start();
require_once "../components/db_connect.php"; 
include '../components/header.php';

// Prüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION["user_id"])) {
    header("Location: product_list.php?error=unavailable&pickup_date=" . urlencode($pickup_date) . "&return_date=" . urlencode($return_date) . "&location=" . urlencode($location));
    exit();
    //Fehler: Sie müssen eingeloggt sein, um eine Buchung vorzunehmen
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user_id"];
    $type_id = intval($_POST["type_id"]);
    $pickup_date = $_POST["pickup_date"];
    $return_date = $_POST["return_date"];
    $location = $_POST["car_location"];
    $use_loyalty = isset($_POST["use_loyalty"]) ? 1 : 0;

    if (strtotime($pickup_date) >= strtotime($return_date)) {
        header("Location: product_list.php?error=unavailable&pickup_date=" . urlencode($pickup_date) . "&return_date=" . urlencode($return_date) . "&location=" . urlencode($location));
        exit();
        //Fehler: Das Rückgabedatum muss nach dem Abholdatum liegen
    }

    $pickup = new DateTime($pickup_date);
    $return = new DateTime($return_date);
    $days = $pickup->diff($return)->days;

    if ($days < 1) {
        header("Location: product_list.php?error=unavailable&pickup_date=" . urlencode($pickup_date) . "&return_date=" . urlencode($return_date) . "&location=" . urlencode($location));
        exit();
        //Fehler: Ungültiger Mietzeitraum
    }

    // Verfügbare `car_id` für die gegebene `type_id` suchen
    $stmt = $conn->prepare("
        SELECT car_id, price FROM car_rental_data 
        WHERE type_id = ? AND loc_name = ? 
        AND car_id NOT IN (
            SELECT car_id FROM bookings WHERE (
                (pickup_date <= ? AND return_date >= ?) OR
                (pickup_date <= ? AND return_date >= ?) OR
                (pickup_date >= ? AND return_date <= ?)
            )
        )
        ORDER BY RAND()
        LIMIT 1
    ");
    $stmt->bind_param("isssssss", $type_id, $location, $pickup_date, $pickup_date, $return_date, $return_date, $pickup_date, $return_date);
    $stmt->execute();
    $stmt->bind_result($car_id, $price_per_day);
    $stmt->fetch();
    $stmt->close();

    if (!$car_id) {
        header("Location: product_list.php?error=unavailable&pickup_date=" . urlencode($pickup_date) . "&return_date=" . urlencode($return_date) . "&location=" . urlencode($location));
        exit();
        //Fehler: Kein verfügbares Fahrzeug
    }

    $total_price = $price_per_day * $days;
    
    // **Loyalty-Punkte abrufen**
    $stmt = $conn->prepare("SELECT points FROM loyalty_program WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($loyalty_points);
    $stmt->fetch();
    $stmt->close();

    $loyalty_points = $loyalty_points ?? 0;
    $loyalty_discount = 0;
    $points_used = 0;

    // **Punkte verwenden, falls gewünscht**
    if ($use_loyalty && $loyalty_points > 0) {
        $max_discount = floor($loyalty_points / 10) * 10;  // Punkte in 10er-Schritten
        $loyalty_discount = min($max_discount, $total_price);  // Rabatt nicht mehr als Gesamtpreis
        $points_used = $loyalty_discount;  // Gleiche Anzahl Punkte wie Rabatt
        $total_price -= $loyalty_discount;
    }


    // **Neue Punkte berechnen: 10 Punkte pro 100 € Umsatz**
    $points_earned = floor($total_price / 100) * 10;

    // **Buchung speichern**
    $booked_date = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("
        INSERT INTO bookings (user_id, car_id, type_id, pickup_date, return_date, car_location, total_price, booked_date, loyalty_points_earned, loyalty_points_used) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiisssisii", $user_id, $car_id, $type_id, $pickup_date, $return_date, $location, $total_price, $booked_date, $points_earned, $points_used);
    $stmt->execute();
    $stmt->close();

    // **Treuepunkte aktualisieren**
    $stmt = $conn->prepare("UPDATE loyalty_program SET points = points - ? + ? WHERE user_id = ?");
    $stmt->bind_param("iii", $points_used, $points_earned, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: bookings.php?success=true");
    exit();
}

$conn->close();
?>
