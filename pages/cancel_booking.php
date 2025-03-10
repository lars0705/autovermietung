<?php
session_start();
require_once "../components/db_connect.php";

if (!isset($_SESSION["user_id"]) || !isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$booking_id = intval($_GET["id"]);

// 🔹 **Buchungsdetails abrufen**
$stmt = $conn->prepare("SELECT total_price, loyalty_points_earned, loyalty_points_used, is_cancelled FROM bookings WHERE booking_id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$stmt->bind_result($total_price, $points_earned, $points_used, $is_cancelled);
$stmt->fetch();
$stmt->close();

// 🔹 **Falls Buchung bereits storniert wurde → Abbrechen**
if ($is_cancelled) {
    http_response_code(400);
    exit("❌ Diese Buchung wurde bereits storniert.");
}

// 🔹 **Treuepunkte berechnen**
$refund_amount = $total_price;  // Standard: Volle Rückerstattung
$points_to_deduct = $points_earned - $points_used; // Treuepunkte-Änderung

// 🔹 **Aktuelle Treuepunkte abrufen**
$stmt = $conn->prepare("SELECT points FROM loyalty_program WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_points);
$stmt->fetch();
$stmt->close();

// 🔹 **Falls Nutzer keine Punkte hat, wird der Betrag reduziert**
if ($points_used > 0) {
    $current_points += $points_used; // Rückgabe der verwendeten Punkte
} else {
    if ($current_points < $points_to_deduct) {
        // Nutzer hat nicht genug Punkte → Differenz vom Geld abziehen
        $missing_points_value = ($points_to_deduct - $current_points) * 1.0; // 10 Punkte = 10€
        $refund_amount -= $missing_points_value;
        $current_points = 0;  // Alle Punkte aufgebraucht
    } else {
        $current_points -= $points_to_deduct;  // Punkte abziehen
    }
}

// 🔹 **Buchung als storniert markieren & Rückerstattung speichern**
$stmt = $conn->prepare("UPDATE bookings SET is_cancelled = TRUE, refund_amount = ?, loyalty_points_earned = 0 WHERE booking_id = ?");
$stmt->bind_param("di", $refund_amount, $booking_id);
$stmt->execute();
$stmt->close();

// 🔹 **Treuepunkte aktualisieren**
$stmt = $conn->prepare("UPDATE loyalty_program SET points = ? WHERE user_id = ?");
$stmt->bind_param("ii", $current_points, $user_id);
$stmt->execute();
$stmt->close();

$conn->close();
http_response_code(200);
exit();
?>
