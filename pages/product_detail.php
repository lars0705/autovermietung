<?php
session_start();
require_once "../components/db_connect.php"; 

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Standardwerte für Datumsübernahme aus product_list.php
$default_pickup = isset($_GET['pickup_date']) ? $_GET['pickup_date'] : date('Y-m-d', strtotime('+1 day'));
$default_return = isset($_GET['return_date']) ? $_GET['return_date'] : date('Y-m-d', strtotime('+2 days'));

$stmt = $conn->prepare("SELECT * FROM car_rental_data WHERE type_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();
$stmt->close();

if (!$car) {
    die("Fahrzeug nicht gefunden.");
}

// Abhol- und Rückgabeort aus Datenbank
$location = htmlspecialchars($car["loc_name"]);

$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Sigmacars | <?php echo htmlspecialchars($car["vendor_name"]) . " " . htmlspecialchars($car["name"]); ?> - Details</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include '../components/header.php'; ?>

<div class="product_detail_container">
    <!-- Fahrzeugbild & Preisübersicht -->
    <div class="top_section">
        <h2><?php echo htmlspecialchars($car["vendor_name"]) . " " . htmlspecialchars($car["name"]); ?></h2>
        <div class="car_image">
            <?php include '../components/load_image.php'; ?>
        </div>
        <p class="car_price_large"><?php echo htmlspecialchars($car["price"]); ?>€ / Tag <span class="km_info">300km / Tag</span></p>
    </div>

    <!-- Technische Daten & Buchungsinformationen -->
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

    <!-- Buchungsdetails -->
    <div class="details_section">
        <h3>Buchungsdetails</h3>
        <form action="book_car.php" method="POST" id="booking-form">
            <input type="hidden" name="car_id" value="<?php echo $id; ?>">
            <input type="hidden" name="car_location" value="<?php echo $location; ?>">

            <div class="details_grid">
                <label for="pickup_date"><strong>Abholdatum:</strong></label>
                <input type="date" id="pickup_date" name="pickup_date" value="<?php echo $default_pickup; ?>" required>

                <label for="return_date"><strong>Rückgabedatum:</strong></label>
                <input type="date" id="return_date" name="return_date" value="<?php echo $default_return; ?>" required>

                <p><strong>Abhol- & Rückgabeort:</strong> <?php echo $location; ?></p>
                <p><strong>Gesamtpreis:</strong> <span id="total_price">0</span>€</p>
            </div>

            <p id="date_error" class="error" style="display: none;">Das Rückgabedatum muss mindestens einen Tag nach dem Abholdatum liegen.</p>
            <p id="booking_error" class="error" style="display: none;">Dieses Fahrzeug ist im gewählten Zeitraum nicht verfügbar.</p>

            <button type="submit" id="book_button" class="book_button">Jetzt buchen</button>
        </form>
    </div>

    <!-- Zurück zur Produktübersicht -->
    <button id="back_button" class="back_button">Zurück zur Fahrzeugübersicht</button>
</div>

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
    const bookingForm = document.getElementById("booking-form");

    function calculatePrice() {
        let startDate = new Date(pickupDate.value);
        let endDate = new Date(returnDate.value);
        let days = (endDate - startDate) / (1000 * 60 * 60 * 24);

        if (days >= 1) {
            totalPriceElement.innerText = (days * pricePerDay).toFixed(2);
            dateError.style.display = "none";
            checkAvailability();
        } else {
            totalPriceElement.innerText = "0";
            dateError.style.display = "block";
            bookButton.disabled = true;
        }
    }

    function checkAvailability() {
        fetch(`check_availability.php?car_id=${carId}&pickup_date=${pickupDate.value}&return_date=${returnDate.value}`)
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    bookingError.style.display = "none";
                    bookButton.disabled = false;
                } else {
                    bookingError.style.display = "block";
                    bookButton.disabled = true;
                }
            });
    }

    bookingForm.addEventListener("submit", function (event) {
        event.preventDefault();
        if (!bookButton.disabled) {
            bookingForm.submit();
        }
    });

    pickupDate.addEventListener("input", calculatePrice);
    returnDate.addEventListener("input", calculatePrice);

    document.getElementById("back_button").addEventListener("click", function () {
        window.history.back();
    });

    calculatePrice();
});
</script>

<?php include '../components/footer.php'; ?>

</body>
</html>
