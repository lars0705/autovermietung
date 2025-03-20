<?php
session_start();
require_once "../components/db_connect.php";

// Redirect to homepage if user is not logged in or no booking ID is provided
if (!isset($_SESSION["user_id"]) || !isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$booking_id = intval($_GET["id"]);

// Retrieve booking details
$stmt = $conn->prepare("SELECT total_price, loyalty_points_earned, loyalty_points_used, is_cancelled FROM bookings WHERE booking_id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$stmt->bind_result($total_price, $points_earned, $points_used, $is_cancelled);
$stmt->fetch();
$stmt->close();

// If booking is already cancelled → exit early
if ($is_cancelled) {
    http_response_code(400);
    exit("❌ Diese Buchung wurde bereits storniert.");
}

// Calculate loyalty points adjustment
$refund_amount = $total_price;  // Default: full refund
$points_to_deduct = $points_earned - $points_used; // Adjust loyalty points

// Get current loyalty points of the user
$stmt = $conn->prepare("SELECT points FROM loyalty_program WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_points);
$stmt->fetch();
$stmt->close();

// If points were used, restore them; otherwise deduct earned points
if ($points_used > 0) {
    $current_points += $points_used; // Restore used points
} else {
    if ($current_points < $points_to_deduct) {
        // Not enough points → deduct the difference from the refund amount
        $missing_points_value = ($points_to_deduct - $current_points) * 1.0; // 10 points = €10
        $refund_amount -= $missing_points_value;
        $current_points = 0;  // All points used up
    } else {
        $current_points -= $points_to_deduct;  // Deduct points
    }
}

// Mark booking as cancelled & save refund amount
$stmt = $conn->prepare("UPDATE bookings SET is_cancelled = TRUE, refund_amount = ?, loyalty_points_earned = 0 WHERE booking_id = ?");
$stmt->bind_param("di", $refund_amount, $booking_id);
$stmt->execute();
$stmt->close();

// Update user's loyalty points
$stmt = $conn->prepare("UPDATE loyalty_program SET points = ? WHERE user_id = ?");
$stmt->bind_param("ii", $current_points, $user_id);
$stmt->execute();
$stmt->close();

$conn->close();
http_response_code(200);
exit();
?>
