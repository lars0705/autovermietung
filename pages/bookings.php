<?php
session_start();
if (!isset($_SESSION["user_id"]) && !isset($_COOKIE["user_id"])) {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "car_rental_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

$user_id = $_SESSION["user_id"] ?? $_COOKIE["user_id"];
$sql = "SELECT * FROM orders WHERE user_id = $user_id";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Meine Bestellungen</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../components/header.php'; ?>

<div class="orders-container">
    <h2>Meine Bestellungen</h2>
    <?php if ($result->num_rows > 0): ?>
        <ul>
            <?php while ($order = $result->fetch_assoc()): ?>
                <li>Bestellung #<?php echo $order["id"]; ?> - <?php echo $order["status"]; ?></li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Keine Bestellungen gefunden.</p>
    <?php endif; ?>
</div>
<?php include '../components/footer.php'; ?>
</body>
</html>
