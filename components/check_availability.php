<?php
// Include the database connection file to enable interaction with the database
require_once "../components/db_connect.php";

// Get the car ID from the URL and make sure it's treated as an integer
$car_id = intval($_GET["car_id"]);

// Get the pickup and return dates from the URL parameters
$pickup_date = $_GET["pickup_date"];
$return_date = $_GET["return_date"];

// SQL query to check if the selected car is already booked for the given time period
// Booking overlaps with the start date
// Booking overlaps with the end date
// Booking is fully within the selected period
$sql = "SELECT COUNT(*) FROM bookings WHERE car_id = ? AND (
    (pickup_date <= ? AND return_date >= ?) OR  
    (pickup_date <= ? AND return_date >= ?) OR  
    (pickup_date >= ? AND return_date <= ?)    
)";

// Prepare the SQL statement to safely insert the variables and prevent SQL injection
$stmt = $conn->prepare($sql);

// Bind the variables to the SQL query placeholders ("i" = integer, "s" = string)
$stmt->bind_param("issssss", $car_id, $pickup_date, $pickup_date, $return_date, $return_date, $pickup_date, $return_date);

// Execute the query
$stmt->execute();

// Get the result of the query (number of conflicting bookings)
$stmt->bind_result($count);
$stmt->fetch();

// Close the statement and database connection to free resources
$stmt->close();
$conn->close();

// Return a JSON response indicating if the car is available (true if no conflicts found)
echo json_encode(["available" => $count == 0]);
?>
