<?php
session_start();
if (!isset($_SESSION["user_id"]) && !isset($_COOKIE["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../components/db_connect.php";

$user_id = $_SESSION["user_id"] ?? $_COOKIE["user_id"];

// Erst Buchungen des Users abrufen
$sql = "SELECT * FROM bookings WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Falls keine Buchungen vorhanden sind, leere Liste setzen
if (empty($bookings)) {
    $cars = [];
} else {
    // Alle Auto-IDs sammeln
    $car_ids = array_column($bookings, 'car_id');
    $placeholders = implode(',', array_fill(0, count($car_ids), '?'));

    // Alle Fahrzeugdetails für die gebuchten Autos abrufen
    $sql = "SELECT * FROM car_rental_data WHERE car_id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($car_ids)), ...$car_ids);
    $stmt->execute();
    $cars_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Autos nach `car_id` indizieren
    $cars = [];
    foreach ($cars_data as $car) {
        $cars[$car['car_id']] = $car;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Sigmacars | Meine Bestellungen</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include '../components/header.php'; ?>

<div class="orders-container">
    <h2>Meine Bestellungen</h2>

    <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        <p class="success_message">Vielen Dank für Ihre Buchung! Ihre Reservierung wurde erfolgreich gespeichert.</p>
    <?php endif; ?>

    <?php if (!empty($bookings)): ?>
        <?php foreach ($bookings as $order): ?>
            <?php
            $car = $cars[$order['car_id']] ?? null;
            if (!$car) continue;

            // Bild setzen
            include '../components/header.php';

            // Status bestimmen (falls `status` nicht existiert, auf "Unbekannt" setzen)
            $status = isset($order["status"]) ? strtolower($order["status"]) : "unbekannt";
            ?>

            <div class="order-card">
                <div class="order-details">
                    <h3><?php echo htmlspecialchars($car["vendor_name"]) . " " . htmlspecialchars($car["name"]); ?></h3>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($car["type"]); ?></p>
                    <p><strong>Abholdatum:</strong> <?php echo htmlspecialchars($order["pickup_date"]); ?></p>
                    <p><strong>Rückgabedatum:</strong> <?php echo htmlspecialchars($order["return_date"]); ?></p>
                    <p><strong>Abhol-/Rückgabeort:</strong> <?php echo htmlspecialchars($order["car_location"]); ?></p>
                    <p><strong>Gesamtpreis:</strong> <?php echo number_format($order["total_price"], 2); ?>€</p>
                    <p><strong>Status:</strong> <span class="status <?php echo $status; ?>">
                        <?php echo ucfirst($status); ?>
                    </span></p>
                    <button class="cancel-button" onclick="cancelBooking(<?php echo $order['id']; ?>)">Buchung stornieren</button>
                </div>
                <img src="<?php echo $imagePath; ?>" alt="Fahrzeugbild">
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-orders">Keine Bestellungen gefunden.</p>
    <?php endif; ?>
</div>

<?php include '../components/footer.php'; ?>

<script>
function cancelBooking(bookingId) {
    if (confirm("Möchten Sie diese Buchung wirklich stornieren?")) {
        fetch('cancel_booking.php?id=' + bookingId, {
            method: 'GET'
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert("Fehler beim Stornieren der Buchung.");
            }
        });
    }
}
</script>

</body>
</html>
