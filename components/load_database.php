<?php
$host = "localhost"; // Standard-Host in XAMPP
$user = "root"; // Standard-Benutzername
$pass = ""; // Standard ist kein Passwort
$dbname = "deine_datenbank"; // Ersetze mit deinem Datenbanknamen

// Verbindung erstellen
$conn = new mysqli($host, $user, $pass, $dbname);

// Verbindung prüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}
?>