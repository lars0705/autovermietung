<?php
session_start();
if (!isset($_SESSION["user_id"]) && !isset($_COOKIE["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../components/db_connect.php";

$user_id = $_SESSION["user_id"] ?? $_COOKIE["user_id"];

// Paging-Variablen
$bookings_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $bookings_per_page;

// Buchungen mit Fahrzeugdetails abrufen (nach `booked_date` absteigend sortiert)
$sql = "
    SELECT b.booking_id, b.pickup_date, b.return_date, b.booked_date, b.total_price,
           c.vendor_name, c.name, c.type, c.price, c.img_file_name, c.loc_name
    FROM bookings b
    JOIN car_rental_data c ON b.car_id = c.car_id
    WHERE b.user_id = ?
    ORDER BY b.booked_date DESC
    LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $bookings_per_page, $offset);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Gesamtzahl der Buchungen für Paging berechnen
$sql = "SELECT COUNT(*) AS total FROM bookings WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_result = $stmt->get_result()->fetch_assoc();
$total_bookings = $total_result["total"];
$total_pages = ceil($total_bookings / $bookings_per_page);
$stmt->close();

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

<div class="content_container">
    <!-- Linke Seite: Tabelle mit Buchungen -->
    <div class="table_container">
        <h2>Meine Bestellungen</h2>
        <?php if (!empty($bookings)): ?>
            <a href="feedback.php" class="feedback_button">Feedback geben</a>
        <?php endif; ?>
        <?php if (isset($_GET['feedback_success'])): ?>
            <p class="success_message">Danke für Ihr Feedback! Ihre Bewertung wurde erfolgreich gespeichert.</p>
        <?php endif; ?>

        <p>Gesamtbuchungen: <?php echo $total_bookings; ?> | Seiten: <?php echo $total_pages; ?></p>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <p class="success_message">Vielen Dank für Ihre Buchung! Ihre Reservierung wurde erfolgreich gespeichert.</p>
        <?php endif; ?>

        <table class="bookings_table">
            <tr>
                <th>Booking ID</th>
                <th>Von</th>
                <th>Bis</th>
                <th>Hersteller</th>
                <th>Name</th>
                <th>Buchung vom</th>
                <th>Gesamtpreis</th>
                <th>Aktion</th>
            </tr>
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $order): ?>
                    <tr>
                        <td><?php echo $order["booking_id"]; ?></td>
                        <td><?php echo $order["pickup_date"]; ?></td>
                        <td><?php echo $order["return_date"]; ?></td>
                        <td><?php echo $order["vendor_name"]; ?></td>
                        <td><?php echo $order["name"]; ?></td>
                        <td><?php echo $order["booked_date"]; ?></td>
                        <td><?php echo number_format($order["total_price"], 2, ',', '.'); ?>€</td>
                        <td>
                            <button class="cancel_button" onclick="cancelBooking(<?php echo $order['booking_id']; ?>)">Stornieren</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">Keine Bestellungen gefunden.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Rechte Seite: Fahrzeugdetails -->
    <div class="frame_container">
        <?php foreach ($bookings as $order): ?>
            <?php 
                $now = new DateTime();
                $pickup_time = new DateTime($order["pickup_date"]);
                $interval = $now->diff($pickup_time);
                $remaining_time = ($interval->invert) ? "Bereits begonnen" : $interval->format('%d Tage %h Stunden %i Minuten');
            ?>
            <div class="booking_frame">
                <h3><?php echo htmlspecialchars($order["vendor_name"]) . " " . htmlspecialchars($order["name"]); ?></h3>
                <p><strong>Fahrzeugtyp:</strong> <?php echo htmlspecialchars($order["type"]); ?></p>
                <img src="../images/<?php echo htmlspecialchars($order["img_file_name"]); ?>" alt="Fahrzeugbild">
                <p><strong>Mietbeginn:</strong> <?php echo htmlspecialchars($order["pickup_date"]); ?></p>
                <p><strong>Mietende:</strong> <?php echo htmlspecialchars($order["return_date"]); ?></p>
                <p><strong>Verbleibende Zeit bis Mietbeginn:</strong> <?php echo $remaining_time; ?></p>
                <p><strong>Standort:</strong> <?php echo htmlspecialchars($order["loc_name"]); ?></p>
                <p><strong>Preis pro Tag:</strong> <?php echo number_format($order["price"], 2, ',', '.'); ?>€</p>
                <p><strong>Gesamtsumme:</strong> <?php echo number_format($order["total_price"], 2, ',', '.'); ?>€</p>
                <button class="cancel_button" onclick="cancelBooking(<?php echo $order['booking_id']; ?>)">Stornieren</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function cancelBooking(bookingId) {
    if (confirm("Möchten Sie diese Buchung wirklich stornieren?")) {
        fetch('cancel_booking.php?id=' + bookingId, { method: 'GET' })
        .then(response => response.ok ? location.reload() : alert("Fehler beim Stornieren der Buchung."));
    }
}
</script>
<?php include '../components/footer.php'; ?>
</body>
</html>
