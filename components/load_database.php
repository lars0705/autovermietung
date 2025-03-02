<?php

// config für Datenbank
$host = "localhost"; // Standard-Host in XAMPP
$user = "root"; // Standard-Benutzername
$pass = "";
$dbname = "car_rental_db"; // Name der Datenbank

// Verbindung erstellen
$conn = new mysqli($host, $user, $pass, $dbname);

// Verbindung prüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

$sql = "SELECT * FROM car_rental_data"; // Tabellennamen
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row["type_id"] . " - Name: " . $row["vendor_name"] . "<br>";
    }
} else {
    echo "Keine Daten gefunden";
}
echo "tset <br>";
$conn->close(); // Verbindung schließen
?>