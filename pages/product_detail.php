<?php
session_start();
require_once "../components/db_connect.php"; 

$id = isset($_GET['type_id']) ? intval($_GET['type_id']) : 0;

// Standardwerte für Datumsübernahme aus product_list.php
$default_pickup = isset($_GET['pickup_date']) ? $_GET['pickup_date'] : date('Y-m-d', strtotime('+1 day'));
$default_return = isset($_GET['return_date']) ? $_GET['return_date'] : date('Y-m-d', strtotime('+2 days'));
$count = isset($_GET['count']) ? intval($_GET['count']) : 0;

$stmt = $conn->prepare("SELECT * FROM car_rental_data WHERE type_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();
$stmt->close();

if (!$car) {
    die("Fahrzeug nicht gefunden.");
}

$location = htmlspecialchars($car["loc_name"]);
$conn->close();

// Überprüfen, ob Benutzer sich gerade angemeldet hat
$just_logged_in = isset($_GET['logged_in']);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Sigmacars | <?php echo htmlspecialchars($car["vendor_name"]) . " " . htmlspecialchars($car["name"]); ?> - Details</title>
    <link rel="stylesheet" href="../css/style.css">
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (window.location.search.includes("logged_in=true")) {
                // Entferne den Parameter aus der URL nach dem Laden der Seite
                const url = new URL(window.location);
                url.searchParams.delete("logged_in");
                window.history.replaceState({}, document.title, url);
            }
        });
    </script>
</head>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const pickupDate = document.getElementById("pickup_date");
    const returnDate = document.getElementById("return_date");
    const totalPriceElement = document.getElementById("total_price");
    const bookButton = document.getElementById("book_button");
    const dateError = document.getElementById("date_error");
    const bookingError = document.getElementById("booking_error");

    const pricePerDay = <?php echo $car["price"]; ?>;
    const carId = <?php echo $id; ?>;
    
    function calculatePrice() {
        let startDate = new Date(pickupDate.value);
        let endDate = new Date(returnDate.value);
        let days = (endDate - startDate) / (1000 * 60 * 60 * 24);

        if (days >= 1) {
            totalPriceElement.innerText = (days * pricePerDay).toFixed(2);
            if (dateError) dateError.style.display = "none";
            checkAvailability();
        } else {
            totalPriceElement.innerText = "0";
            if (dateError) dateError.style.display = "block";
            if (bookButton) bookButton.disabled = true;
        }
    }

    function checkAvailability() {
        fetch(`check_availability.php?car_id=${carId}&pickup_date=${pickupDate.value}&return_date=${returnDate.value}`)
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    if (bookingError) bookingError.style.display = "none";
                    if (bookButton) bookButton.disabled = false;
                } else {
                    if (bookingError) bookingError.style.display = "block";
                    if (bookButton) bookButton.disabled = true;
                }
            });
    }

    if (pickupDate && returnDate) {
        pickupDate.addEventListener("input", calculatePrice);
        returnDate.addEventListener("input", calculatePrice);
        calculatePrice();
    }

    document.getElementById("back_button").addEventListener("click", function () {
        window.history.back();
    });
});
</script>

<body>

<?php include '../components/header.php'; ?>

<div class="product_detail_container">
    <?php if ($just_logged_in): ?>
        <div class="welcome_message">
            <p>✅ Vielen Dank fürs Anmelden! Sie können nun dieses Fahrzeug mieten.</p>
        </div>
    <?php endif; ?>

    <div class="top_section">
        <h2><?php echo htmlspecialchars($car["vendor_name"]) . " " . htmlspecialchars($car["name"]); ?></h2>
        <div class="car_image">
            <?php include '../components/load_image.php'; ?>
        </div>
        <p class="car_price_large"><?php echo number_format($car["price"], 2, ',', '.'); ?>€ / Tag <span class="km_info">300km / Tag</span></p>
        <p class="availabilty_large"><?php echo $count; ?><strong> verfügbares Fahrzeug<?php echo ($count == 1) ? '' : 'e'; ?> in </strong><?php echo $location; ?>
</p>
    </div>

    <div class="details_section">
        <h3>Technische Daten</h3>
        <div class="details_grid">
            <p><strong>Type:</strong> <?php echo htmlspecialchars($car["type"]); ?></p>
            <p><strong>Antrieb:</strong> <?php echo htmlspecialchars($car["drive"]); ?></p>
            <p><strong>Getriebe:</strong> <?php echo htmlspecialchars($car["gear"]); ?></p>
            <p><strong>Sitze:</strong> <?php echo htmlspecialchars($car["seats"]); ?></p>
            <p><strong>Türen:</strong> <?php echo htmlspecialchars($car["doors"]); ?></p>
            <p><strong>Klimaanlage:</strong> <?php echo $car["air_condition"] ? "Ja" : "Nein"; ?></p>
            <p><strong>GPS:</strong> <?php echo $car["gps"] ? "Ja" : "Nein"; ?></p>
            <p><strong>Mindestalter:</strong> <?php echo htmlspecialchars($car["min_age"]); ?> Jahre</p>
        </div>
    </div>

    <div class="details_section">
        <h3>Buchungsdetails</h3>

        <?php if (isset($_SESSION["user_id"])): ?>
            <form action="book_car.php" method="POST" id="booking-form">
                <input type="hidden" name="type_id" value="<?php echo $id; ?>">
                <input type="hidden" name="car_location" value="<?php echo $location; ?>">

                <div class="details_grid">
                    <label><strong>Abholdatum:</strong> <span id="pickup_date_label"><?php echo $default_pickup; ?></span></label>
                    <input type="hidden" id="pickup_date" name="pickup_date" value="<?php echo $default_pickup; ?>">


                    <label><strong>Rückgabedatum:</strong> <span id="return_date_label"><?php echo $default_return; ?></span></label>
                    <input type="hidden" id="return_date" name="return_date" value="<?php echo $default_return; ?>">


                    <p><strong>Abhol- & Rückgabeort:</strong> <?php echo $location; ?></p>
                    <p><strong>Gesamtpreis:</strong> <span id="total_price">0</span>€</p>
                </div>

                <button type="submit" id="book_button" class="book_button">Jetzt buchen</button>
            </form>
        <?php else: ?>
            <p class="warning">Bitte melden Sie sich an, um dieses Fahrzeug zu mieten.</p>
            <div class="auth_buttons">
                <a href="login.php?type_id=<?php echo $id; ?>&pickup_date=<?php echo $default_pickup; ?>&return_date=<?php echo $default_return; ?>" class="login_button">Anmelden</a>
                <a href="register.php?type_id=<?php echo $id; ?>&pickup_date=<?php echo $default_pickup; ?>&return_date=<?php echo $default_return; ?>" class="register_button">Registrieren</a>
            </div>
        <?php endif; ?>
    </div>

    <button id="back_button" class="back_button">Zurück zur Fahrzeugübersicht</button>
</div>

<?php include '../components/footer.php'; ?>

</body>
</html>
