<?php
require_once "../components/db_connect.php";

$car_id = intval($_GET["car_id"]);
$pickup_date = $_GET["pickup_date"];
$return_date = $_GET["return_date"];

$sql = "SELECT COUNT(*) FROM bookings WHERE car_id = ? AND (
    (pickup_date <= ? AND return_date >= ?) OR
    (pickup_date <= ? AND return_date >= ?) OR
    (pickup_date >= ? AND return_date <= ?)
)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssss", $car_id, $pickup_date, $pickup_date, $return_date, $return_date, $pickup_date, $return_date);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();
$conn->close();

echo json_encode(["available" => $count == 0]);
?>
