<?php
session_start();
require_once "../components/db_connect.php";

if (!isset($_SESSION["user_id"]) || !isset($_GET["id"])) {
    http_response_code(400);
    exit();
}

$booking_id = intval($_GET["id"]);

$stmt = $conn->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $_SESSION["user_id"]);
$stmt->execute();
$stmt->close();
$conn->close();

http_response_code(200);
?>
