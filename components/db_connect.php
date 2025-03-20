<?php
// Define database connection parameters: host, username, password, and database name
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "car_rental_db";

// Create a new connection to the MySQL database using the provided parameters
$conn = new mysqli($host, $user, $pass, $dbname);

// Check if the connection was successful; if not, stop the script and show an error message
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
