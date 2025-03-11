<?php
session_start();

// redirect to login if user is not logged in
if (!isset($_SESSION["user_id"]) && !isset($_COOKIE["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../components/db_connect.php";

// determine user ID from session or cookie
$user_id = $_SESSION["user_id"] ?? $_COOKIE["user_id"];

// pagination settings for active bookings 
$bookings_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $bookings_per_page;

// fetch active bookings
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

// count total active bookings
$sql = "SELECT COUNT(*) AS total FROM bookings WHERE user_id = ? AND is_cancelled = FALSE";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_result = $stmt->get_result()->fetch_assoc();
$total_bookings = $total_result["total"];
$total_pages = ceil($total_bookings / $bookings_per_page);
$stmt->close();

// pagination settings for cancelled bookings
$cancelled_bookings_per_page = 10;
$cancelled_current_page = isset($_GET['cancelled_page']) ? max(1, intval($_GET['cancelled_page'])) : 1;
$cancelled_offset = ($cancelled_current_page - 1) * $cancelled_bookings_per_page;

// fetch cancelled bookings
$sql = "
    SELECT booking_id, pickup_date, return_date, booked_date, total_price, refund_amount, 
           loyalty_points_earned, loyalty_points_used
    FROM bookings
    WHERE user_id = ? AND is_cancelled = TRUE
    ORDER BY booked_date DESC
    LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $cancelled_bookings_per_page, $cancelled_offset);
$stmt->execute();
$cancelled_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// count total cancelled bookings
$sql = "SELECT COUNT(*) AS total FROM bookings WHERE user_id = ? AND is_cancelled = TRUE";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_cancelled_result = $stmt->get_result()->fetch_assoc();
$total_cancelled_bookings = $total_cancelled_result["total"];
$total_cancelled_pages = ceil($total_cancelled_bookings / $cancelled_bookings_per_page);
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
    <div class="upcoming_bookings_container">
        <h3>📅 Anstehend</h3>
        <div class="scrolling_frame">
            <?php 

            // filter upcoming bookings
            $upcoming_bookings = array_filter($active_bookings, function($booking) {
                return strtotime($booking["pickup_date"]) >= strtotime(date("Y-m-d"));
            });

            // sort by pickup date
            usort($upcoming_bookings, function($a, $b) {
                return strtotime($a["pickup_date"]) - strtotime($b["pickup_date"]);
            });

            // calculate remaining days
            if (!empty($upcoming_bookings)): ?>
                <?php
                foreach ($upcoming_bookings as $booking): 
                $today = strtotime("today"); 
                $pickup = strtotime($booking["pickup_date"]); 

                $days_left = floor(($pickup - $today) / 86400);

                $days_text = $days_left == 1 ? "Tag" : "Tagen";
                ?>
                    <div class="upcoming_booking_frame">
                        <img src="../assets/images/cars/<?php echo htmlspecialchars($booking["img_file_name"]); ?>.png" alt="Fahrzeugbild">
                        <div class="upcoming_booking_info">
                            <p class="vehicle_name"><strong><?php echo htmlspecialchars($booking["vendor_name"] . " " . $booking["name"]); ?></strong></p>
                            <p class="start_date">Start in: <?php echo $days_left; ?> <?php echo $days_text; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Keine anstehenden Buchungen</p>
            <?php endif; ?>
        </div>
        <div class="feedback_button_container">
            <a href="feedback.php" class="feedback_button">Feedback abgeben</a>
        </div>
    </div>

    <div class="table_container">
        <h2>Meine Buchungen</h2>
        
        <!-- success message after booking -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <p class="success_message">✅ Vielen Dank für Ihre Buchung! Ihre Reservierung wurde erfolgreich gespeichert.</p>
        <?php endif; ?>

        <!-- success message after cancelling -->
        <?php if (isset($_GET['cancelled']) && $_GET['cancelled'] == 'true'): ?>
            <p class="success_message">⚠️ Ihre Buchung wurde storniert.</p>
        <?php endif; ?>

        <h3>📌 Aktive Buchungen</h3>
        <p>Einträge: <?php echo $total_bookings; ?> | Seiten: <?php echo $total_pages; ?></p>
        <table class="bookings_table">
            <tr>
                <th>Buchungs-<br>ID</th>
                <th>Abhol-<br>datum</th>
                <th>Rückgabe-<br>datum</th>
                <th>Marke</th>
                <th>Modell</th>
                <th>Buchungs-<br>datum</th>
                <th>Treuepunkte-<br>rabatt</th>
                <th>Rechnungs-<br>betrag</th>
                <th>Treuepunkte erhalten</th>
                <th>Aktion</th>
            </tr>
            <?php if (!empty($active_bookings)): ?>

                <!-- show active bookings -->
                <?php foreach ($active_bookings as $order): ?>
                    <tr>
                        <td><?php echo $order["booking_id"]; ?></td>
                        <td><?php echo $order["pickup_date"]; ?></td>
                        <td><?php echo $order["return_date"]; ?></td>
                        <td><?php echo $order["vendor_name"]; ?></td>
                        <td><?php echo $order["name"]; ?></td>
                        <td><?php echo date("d.m.Y", strtotime($order["booked_date"])); ?></td>
                        <td><?php echo number_format($order["loyalty_points_used"], 2, ',', '.'); ?>€</td>
                        <td><?php echo number_format($order["total_price"], 2, ',', '.'); ?>€</td>
                        <td><?php echo $order["loyalty_points_earned"]; ?> ⭐</td>
                        <td>
                            <button class="cancel_button" onclick="cancelBooking(<?php echo $order['booking_id']; ?>)">Stornieren</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>

                <!-- show no active bookings message -->
                <tr><td colspan="10">Keine aktiven Buchungen gefunden.</td></tr>
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

        <h3>❌ Stornierte Buchungen</h3>
        <p>Einträge: <?php echo $total_cancelled_bookings; ?> | Seiten: <?php echo $total_cancelled_pages; ?></p>
        <table class="bookings_table">
            <tr>
                <th>Buchungs-<br>ID</th>
                <th>Abhol-<br>datum</th>
                <th>Rückgabe-<br>datum</th>
                <th>Buchungs-<br>datum</th>
                <th>Treuepunkte-<br>rabatt</th>
                <th>Rechnungs-<br>betrag</th>
                <th>Rückerstattung<br>€</th>
                <th>Rückerstattung<br>Treuepunkte</th>
                <th>Status</th>
            </tr>
            <?php if (!empty($cancelled_bookings)): ?>

                <!-- show cancelled bookings -->
                <?php foreach ($cancelled_bookings as $order): ?>
                    <tr>
                        <td><?php echo $order["booking_id"]; ?></td>
                        <td><?php echo $order["pickup_date"]; ?></td>
                        <td><?php echo $order["return_date"]; ?></td>
                        <td><?php echo date("d.m.Y", strtotime($order["booked_date"])); ?></td>
                        <td><?php echo number_format($order["loyalty_points_used"], 2, ',', '.'); ?>€</td>
                        <td><?php echo number_format($order["total_price"], 2, ',', '.'); ?>€</td>
                        <td><?php echo number_format($order["refund_amount"], 2, ',', '.'); ?>€</td>
                        <td><?php echo $order["loyalty_points_used"]; ?> ⭐</td>
                        <td>Storniert</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>

                <!-- show no cancelled bookings message -->
                <tr><td colspan="9">Keine stornierten Buchungen gefunden.</td></tr>
            <?php endif; ?>
        </table>

        <div class="pagination">
            <?php if ($cancelled_current_page > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['cancelled_page' => $cancelled_current_page - 1])); ?>">« Zurück</a>
            <?php endif; ?>
            <span>Seite <?php echo $cancelled_current_page; ?> von <?php echo $total_cancelled_pages; ?></span>
            <?php if ($cancelled_current_page < $total_cancelled_pages): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['cancelled_page' => $cancelled_current_page + 1])); ?>">Weiter »</a>
            <?php endif; ?>
        </div>

    </div>
</div>
<?php include '../components/footer.php'; ?>
</body>
</html>

<script>

// cancel booking via AJAX request
function cancelBooking(bookingId) {
    if (confirm("⚠️ Möchten Sie diese Buchung wirklich stornieren?")) {
        fetch('cancel_booking.php?id=' + bookingId, { method: 'GET' })
        .then(response => response.ok ? location.reload() : alert("Fehler beim Stornieren der Buchung."));
    }
}
</script>