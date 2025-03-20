<?php
session_start();
require_once "../components/db_connect.php"; 
include '../components/header.php';

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: product_list.php?error=unavailable&pickup_date=" . urlencode($pickup_date) . "&return_date=" . urlencode($return_date) . "&location=" . urlencode($location));
    exit();
    // Error: You must be logged in to make a booking
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
        // Error: Return date must be after pickup date
    }

    $pickup = new DateTime($pickup_date);
    $return = new DateTime($return_date);
    $days = $pickup->diff($return)->days;

    if ($days < 1) {
        header("Location: product_list.php?error=unavailable&pickup_date=" . urlencode($pickup_date) . "&return_date=" . urlencode($return_date) . "&location=" . urlencode($location));
        exit();
        // Error: Invalid rental period
    }

    // Find available `car_id` for the given `type_id`
    $stmt = $conn->prepare("
        SELECT car_id, price FROM car_rental_data 
        WHERE type_id = ? AND loc_name = ?
        AND car_id NOT IN (
            SELECT car_id FROM bookings WHERE (
                (pickup_date <= ? AND return_date >= ?) OR
                (pickup_date <= ? AND return_date >= ?) OR
                (pickup_date >= ? AND return_date <= ?)
            ) AND is_cancelled = 0
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
        // Error: No available vehicle
    }

    $total_price = $price_per_day * $days;
    
    // Retrieve loyalty points
    $stmt = $conn->prepare("SELECT points FROM loyalty_program WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($loyalty_points);
    $stmt->fetch();
    $stmt->close();

    $loyalty_points = $loyalty_points ?? 0;
    $loyalty_discount = 0;
    $points_used = 0;

    // Use loyalty points if selected
    if ($use_loyalty && $loyalty_points > 0) {
        $max_discount = floor($loyalty_points / 10) * 10; // Points in steps of 10
        $allowed_discount = floor($total_price / 10) * 10; // Max discount in steps of 10 of total price
        $loyalty_discount = min($max_discount, $allowed_discount); // Final discount is the lower of the two
        $points_used = $loyalty_discount; // Points used match the discount
        $total_price -= $loyalty_discount;
    }
    


    // Calculate new points: 10 points per 100 € revenue
    $points_earned = floor($total_price / 100) * 10;

    // Save booking
    $booked_date = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("
        INSERT INTO bookings (user_id, car_id, type_id, pickup_date, return_date, car_location, total_price, booked_date, loyalty_points_earned, loyalty_points_used) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiisssdsii", $user_id, $car_id, $type_id, $pickup_date, $return_date, $location, $total_price, $booked_date, $points_earned, $points_used);
    $stmt->execute();
    $stmt->close();

    // Update loyalty points
    $stmt = $conn->prepare("UPDATE loyalty_program SET points = points - ? + ? WHERE user_id = ?");
    $stmt->bind_param("iii", $points_used, $points_earned, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: bookings.php?success=true");
    exit();
}

$conn->close();
?>
