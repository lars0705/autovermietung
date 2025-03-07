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

// 🔹 **Aktive Buchungen abrufen (Nicht stornierte)**  
$sql = "
    SELECT b.booking_id, b.pickup_date, b.return_date, b.booked_date, b.total_price, b.type_id, 
           b.loyalty_points_earned, b.loyalty_points_used, 
           c.vendor_name, c.name, c.type, c.price, c.img_file_name, c.loc_name
    FROM bookings b
    JOIN car_rental_data c ON b.car_id = c.car_id
    WHERE b.user_id = ? AND b.is_cancelled = FALSE
    ORDER BY b.booked_date DESC
    LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $bookings_per_page, $offset);
$stmt->execute();
$active_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 🔹 **Stornierte Buchungen abrufen**  
$sql = "
    SELECT booking_id, pickup_date, return_date, booked_date, total_price, refund_amount, 
           loyalty_points_earned, loyalty_points_used
    FROM bookings
    WHERE user_id = ? AND is_cancelled = TRUE
    ORDER BY booked_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cancelled_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Gesamtzahl der aktiven Buchungen für Paging berechnen
$sql = "SELECT COUNT(*) AS total FROM bookings WHERE user_id = ? AND is_cancelled = FALSE";
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
    <link rel="stylesheet" href="../css/style_bookings.css">
</head>
<body>

<?php include '../components/header.php'; ?>
<?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
    <div class="booking-animation-container">
        <div class="flash-effect"></div>
        <div class="smoke" style="left: 35%;"></div>
        <div class="smoke" style="right: 35%;"></div>
        <p class="booking-success-text">✅ Glückwunsch! Deine Buchung war erfolgreich!</p>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Erstelle Konfetti Explosion
            for (let i = 0; i < 100; i++) {
                let confetti = document.createElement("div");
                confetti.classList.add("confetti");
                confetti.style.left = Math.random() * 100 + "vw";
                confetti.style.animationDuration = Math.random() * 3 + 2 + "s";
                confetti.style.backgroundColor = ["red", "blue", "yellow", "green", "purple", "orange"][Math.floor(Math.random() * 6)];
                confetti.style.animationDelay = Math.random() * 2 + "s";
                document.querySelector(".booking-animation-container").appendChild(confetti);
            }

            // Erstelle Funkenpartikel
            for (let i = 0; i < 50; i++) {
                let spark = document.createElement("div");
                spark.classList.add("spark");
                spark.style.left = Math.random() * 100 + "vw";
                spark.style.animationDuration = Math.random() * 2 + 1 + "s";
                spark.style.animationDelay = Math.random() * 2 + "s";
                document.querySelector(".booking-animation-container").appendChild(spark);
            }
        });
    </script>
<?php endif; ?>
<div class="content_container">
    <div class="table_container">
        <h2>Meine Bestellungen</h2>
        
        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <p class="success_message">✅ Vielen Dank für Ihre Buchung! Ihre Reservierung wurde erfolgreich gespeichert.</p>
        <?php endif; ?>

        <?php if (isset($_GET['cancelled']) && $_GET['cancelled'] == 'true'): ?>
            <p class="success_message">⚠️ Ihre Buchung wurde storniert.</p>
        <?php endif; ?>

        <p>Gesamtbuchungen: <?php echo $total_bookings; ?> | Seiten: <?php echo $total_pages; ?></p>

        <h3>📌 Aktive Buchungen</h3>
        <table class="bookings_table">
            <tr>
                <th>Booking ID</th>
                <th>Von</th>
                <th>Bis</th>
                <th>Hersteller</th>
                <th>Name</th>
                <th>Buchung vom</th>
                <th>Gesamtpreis</th>
                <th>Treuepunkte erhalten</th>
                <th>Treuepunkte genutzt</th>
                <th>Aktion</th>
            </tr>
            <?php if (!empty($active_bookings)): ?>
                <?php foreach ($active_bookings as $order): ?>
                    <tr>
                        <td><?php echo $order["booking_id"]; ?></td>
                        <td><?php echo $order["pickup_date"]; ?></td>
                        <td><?php echo $order["return_date"]; ?></td>
                        <td><?php echo $order["vendor_name"]; ?></td>
                        <td><?php echo $order["name"]; ?></td>
                        <td><?php echo $order["booked_date"]; ?></td>
                        <td><?php echo number_format($order["total_price"], 2, ',', '.'); ?>€</td>
                        <td><?php echo $order["loyalty_points_earned"]; ?> ⭐</td>
                        <td><?php echo $order["loyalty_points_used"]; ?> ⭐</td>
                        <td>
                            <button class="cancel_button" onclick="cancelBooking(<?php echo $order['booking_id']; ?>)">Stornieren</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="10">Keine aktiven Buchungen gefunden.</td></tr>
            <?php endif; ?>
        </table>

        <h3>❌ Stornierte Buchungen</h3>
        <table class="bookings_table">
            <tr>
                <th>Booking ID</th>
                <th>Von</th>
                <th>Bis</th>
                <th>Buchung vom</th>
                <th>Gesamtpreis</th>
                <th>Rückerstattung</th>
                <th>Treuepunkte erhalten</th>
                <th>Treuepunkte genutzt</th>
                <th>Status</th>
            </tr>
            <?php if (!empty($cancelled_bookings)): ?>
                <?php foreach ($cancelled_bookings as $order): ?>
                    <tr>
                        <td><?php echo $order["booking_id"]; ?></td>
                        <td><?php echo $order["pickup_date"]; ?></td>
                        <td><?php echo $order["return_date"]; ?></td>
                        <td><?php echo $order["booked_date"]; ?></td>
                        <td><?php echo number_format($order["total_price"], 2, ',', '.'); ?>€</td>
                        <td><?php echo number_format($order["refund_amount"], 2, ',', '.'); ?>€</td>
                        <td><?php echo $order["loyalty_points_earned"]; ?> ⭐</td>
                        <td><?php echo $order["loyalty_points_used"]; ?> ⭐</td>
                        <td>Storniert</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9">Keine stornierten Buchungen gefunden.</td></tr>
            <?php endif; ?>
        </table>

        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>">« Zurück</a>
            <?php endif; ?>
            <span>Seite <?php echo $current_page; ?> von <?php echo $total_pages; ?></span>
            <?php if ($current_page < $total_pages): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>">Weiter »</a>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
function cancelBooking(bookingId) {
    if (confirm("⚠️ Möchten Sie diese Buchung wirklich stornieren?")) {
        fetch('cancel_booking.php?id=' + bookingId, { method: 'GET' })
        .then(response => response.ok ? location.reload() : alert("Fehler beim Stornieren der Buchung."));
    }
}
</script>

<?php include '../components/footer.php'; ?>
</body>
</html>
