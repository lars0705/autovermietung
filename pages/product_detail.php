<?php
session_start();
require_once "../components/db_connect.php"; 

// get selected car type ID from URL, default to 0 if not set
$id = isset($_GET['type_id']) ? intval($_GET['type_id']) : 0;


// retrieve location and date parameters from URL or set defaults
$location = isset ($_GET['car_location']) ? $_GET['car_location'] : '';
$default_pickup = isset($_GET['pickup_date']) ? $_GET['pickup_date'] : date('Y-m-d', strtotime('+1 day'));
$default_return = isset($_GET['return_date']) ? $_GET['return_date'] : date('Y-m-d', strtotime('+2 days'));
$count = isset($_GET['count']) ? intval($_GET['count']) : 0;

// fetch car details from database based on type and location
$stmt = $conn->prepare("SELECT * FROM car_rental_data WHERE type_id = ? AND loc_name = ?");
$stmt->bind_param("is", $id, $location);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();
$stmt->close();

// show error message if no car is found
if (!$car) {
    die("Fahrzeug nicht gefunden.");
}

$location = htmlspecialchars($car["loc_name"]);

// retrieve user's loyalty points if logged in
$user_loyalty_points = 0;
if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
    $stmt = $conn->prepare("SELECT points FROM loyalty_program WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_loyalty_points);
    $stmt->fetch();
    $stmt->close();
}

$conn->close();

// translation mapping for vehicle attributes
$translations = [
    "Combuster" => "Verbrenner",
    "Electric" => "Elektro",
    "automatic" => "Automatik",
    "manually" => "Schaltung",
];

function translate($value, $translations) {
    return $translations[$value] ?? ucfirst($value);
}
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

    <div class="detail_layout">

        <!-- car image and price -->
        <div class="left_section" style="background-image: url('../assets/images/cars/<?php echo htmlspecialchars($car["img_file_name"]); ?>.png');">
            <h2><?php echo htmlspecialchars($car["vendor_name"]) . " " . htmlspecialchars($car["name"]); ?></h2>
            <p class="car_price_large"><?php echo number_format($car["price"], 2, ',', '.'); ?>€ / Tag</p>
            <p class="km_info">Inkl. 300km</p>
            <p class="availability_info">
                <?php echo $count; ?>
                <?php echo ($count == 1) ? ' Fahrzeug in' : ' Fahrzeuge in'; ?>
                <?php echo htmlspecialchars($location); ?>
                <?php echo ' verfügbar'; ?>
            </p>
        </div>

        <!-- technical details and booking information -->
        <div class="right_section">
            <div class="details_section">
                <h3>Technische Daten</h3>
                <div class="details_grid">
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($car["type"]); ?></p>
                    <p><strong>Antrieb:</strong> <?php echo translate($car["drive"], $translations); ?></p>
                    <p><strong>Getriebe:</strong> <?php echo translate($car["gear"], $translations); ?></p>
                    <p><strong>Sitze:</strong> <?php echo htmlspecialchars($car["seats"]); ?></p>
                    <p><strong>Türen:</strong> <?php echo htmlspecialchars($car["doors"]); ?></p>
                    <p><strong>Platz für Koffer:</strong> <?php echo htmlspecialchars($car["trunk"]); ?></p>
                    <p><strong>Klimaanlage:</strong> <?php echo $car["air_condition"] ? "Ja" : "Nein"; ?></p>
                    <p><strong>GPS:</strong> <?php echo $car["gps"] ? "Ja" : "Nein"; ?></p>
                    <p><strong>Mindestalter:</strong> <?php echo htmlspecialchars($car["min_age"]); ?> Jahre</p>
                </div>
            </div>

            <div class="details_section">
                <h3>Buchungsdetails</h3>
                <div class="details_grid">
                    <p><strong>Abholdatum:</strong> <?php echo date("d.m.Y", strtotime($default_pickup)); ?></p>
                    <p><strong>Rückgabedatum:</strong> <?php echo date("d.m.Y", strtotime($default_return)); ?></p>
                    <p><strong>Abhol- & Rückgabeort:</strong> <?php echo $location; ?></p>
                    <p><strong>Ihre aktuellen Treuepunkte:</strong> <?php echo $user_loyalty_points; ?> ⭐</p>
                    <p><strong>Basispreis:</strong> <span id="base_price">0,00€</span></p>
                    <p><strong>Treuepunkte-Rabatt:</strong> <span id="loyalty_discount">-0,00€</span></p>
                    <p><strong>Endsumme:</strong> <span id="final_price">0,00€</span></p>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="type_id" value="<?php echo $id; ?>">
    <input type="hidden" name="car_location" value="<?php echo $location; ?>">
    <input type="hidden" id="pickup_date" name="pickup_date" value="<?php echo $default_pickup; ?>">
    <input type="hidden" id="return_date" name="return_date" value="<?php echo $default_return; ?>">
    <input type="hidden" id="price_per_day" value="<?php echo $car["price"]; ?>">

    <div class="button_section">
        <button id="back_button" class="back_button">Zurück zur Fahrzeugübersicht</button>

        <?php if (isset($_SESSION["user_id"])): ?>
            
            <!-- booking form if logged in -->
            <form action="book_car.php" method="POST" id="booking-form">
            <input type="hidden" name="type_id" value="<?php echo $id; ?>">
            <input type="hidden" name="car_location" value="<?php echo $location; ?>">
            <input type="hidden" id="pickup_date" name="pickup_date" value="<?php echo $default_pickup; ?>">
            <input type="hidden" id="return_date" name="return_date" value="<?php echo $default_return; ?>">
            <input type="hidden" id="price_per_day" value="<?php echo $car["price"]; ?>">
        <label class="loyalty_button"><strong>Treuepunkte einlösen?</strong> <input type="checkbox" id="use_loyalty" name="use_loyalty" value="yes"></label>
        <button type="submit" id="book_button" class="book_button">Jetzt buchen</button>
        </form>
        <?php else: ?>

            <!-- login / register buttons if not logged in -->
            <p class="login_info">Bitte 
            <a href="../pages/login.php?type_id=<?php echo $id; ?>&car_location=<?php echo $location; ?>&pickup_date=<?php echo $default_pickup; ?>&return_date=<?php echo $default_return; ?>&count=<?php echo $count; ?>">melden Sie sich an</a> 
            oder 
            <a href="../pages/register.php?type_id=<?php echo $id; ?>&car_location=<?php echo $location; ?>&pickup_date=<?php echo $default_pickup; ?>&return_date=<?php echo $default_return; ?>&count=<?php echo $count; ?>">registrieren Sie sich</a>
            , um ein Fahrzeug zu buchen.</p>
        <?php endif; ?>
    </div>
</div>


<?php include '../components/footer.php'; ?>


</body>
</html>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const basePriceElement = document.getElementById("base_price");
    const loyaltyDiscountElement = document.getElementById("loyalty_discount");
    const finalPriceElement = document.getElementById("final_price");
    const useLoyaltyCheckbox = document.getElementById("use_loyalty");
    const pricePerDay = parseFloat(document.getElementById("price_per_day").value);
    const pickupDate = new Date(document.getElementById("pickup_date").value);
    const returnDate = new Date(document.getElementById("return_date").value);
    const userLoyaltyPoints = <?php echo $user_loyalty_points; ?>;

    function calculatePrice() {

        // calculate number of rental days
        let days = (returnDate - pickupDate) / (1000 * 60 * 60 * 24);
        if (days < 1) days = 1; // ensure at least 1 day is charged

        let originalTotalPrice = days * pricePerDay;
        let discount = 0;
        let totalPrice = originalTotalPrice;

        // apply loyalty points discount if checkbox is checked
        if (useLoyaltyCheckbox && useLoyaltyCheckbox.checked) {
            let maxDiscount = Math.floor(userLoyaltyPoints / 10) * 10;
            discount = Math.min(maxDiscount, Math.floor(originalTotalPrice / 10) * 10);
            totalPrice -= discount;
        }

        // update price elements
        basePriceElement.innerText = originalTotalPrice.toFixed(2).replace('.', ',') + "€";
        loyaltyDiscountElement.innerText = discount > 0 ? "-" + discount.toFixed(2).replace('.', ',') + "€" : "-0,00€";
        finalPriceElement.innerText = totalPrice.toFixed(2).replace('.', ',') + "€";
    }

    // initial price calculation on page load
    calculatePrice();

    // recalculate price when loyalty points checkbox is toggled
    if (useLoyaltyCheckbox) {
        useLoyaltyCheckbox.addEventListener("change", calculatePrice);
    }
});

// handle back button functionality
document.getElementById("back_button").addEventListener("click", function() {
    history.back();
});

</script>