<?php
session_start();
require_once "../components/db_connect.php"; 

$id = isset($_GET['type_id']) ? intval($_GET['type_id']) : 0;

// Standardwerte für Datumsübernahme aus product_list.php
$location = isset ($_GET['car_location']) ? $_GET['car_location'] : '';
$default_pickup = isset($_GET['pickup_date']) ? $_GET['pickup_date'] : date('Y-m-d', strtotime('+1 day'));
$default_return = isset($_GET['return_date']) ? $_GET['return_date'] : date('Y-m-d', strtotime('+2 days'));
$count = isset($_GET['count']) ? intval($_GET['count']) : 0;

$stmt = $conn->prepare("SELECT * FROM car_rental_data WHERE type_id = ? AND loc_name = ?");
$stmt->bind_param("is", $id, $location);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();
$stmt->close();

if (!$car) {
    die("Fahrzeug nicht gefunden.");
}

$location = htmlspecialchars($car["loc_name"]);

// 🔹 Treuepunkte abrufen
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
    <div class="top_section">
        <h2><?php echo htmlspecialchars($car["vendor_name"]) . " " . htmlspecialchars($car["name"]); ?></h2>
        <div class="car_image">
            <?php include '../components/load_image.php'; ?>
        </div>
        <p class="car_price_large"><?php echo number_format($car["price"], 2, ',', '.'); ?>€ / Tag <span class="km_info">300km / Tag</span></p>
        <p class="availability_large">
            <?php echo $count; ?>
            <strong>verfügbare<?php echo ($count == 1) ? 's Fahrzeug' : ' Fahrzeuge'; ?> in </strong>
            <?php echo htmlspecialchars($location); ?>
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
            <p><strong>Platz für Koffer:</strong> <?php echo htmlspecialchars($car["trunk"]); ?></p>
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
                    
                    <p><strong>Ihre aktuellen Treuepunkte:</strong> <?php echo $user_loyalty_points; ?> ⭐</p>
                    
                    <label><strong>Treuepunkte nutzen?</strong></label>
                    <input type="checkbox" id="use_loyalty" name="use_loyalty" value="yes">
                    
                    <div class="cost_summary">
                        <h3>Kostenübersicht</h3>            
                        <div class="cost_section">
                            <p class="cost_title">Basispreis:</p>
                            <p class="cost_value"><span id="base_price">0,00€</span></p>
                        </div>

                        <div class="cost_section">
                            <p>Treuepunkte-Rabatt:</p>
                            <p class="cost_value discount"><span id="loyalty_discount">-0,00€</span></p>
                        </div>

                        <div class="cost_section total">
                            <p>Gesamtsumme inkl. MwSt.:</p>
                            <p class="cost_value total_price"><span id="final_price">0,00€</span></p>
                        </div>
                    </div>
                </div>
                <button type="submit" id="book_button" class="book_button">Jetzt buchen</button>
                <button id="back_button" class="back_button">Zurück zur Fahrzeugübersicht</button>
            </form>
        <?php else: ?>
            <p class="warning">Bitte melden Sie sich an, um dieses Fahrzeug zu mieten.</p>
            <div class="auth_buttons">
                <a href="login.php?type_id=<?php echo $id; ?>&pickup_date=<?php echo $default_pickup; ?>&return_date=<?php echo $default_return; ?>&count=<?php echo $count; ?>" class="login_button">Anmelden</a>
                <a href="register.php?type_id=<?php echo $id; ?>&pickup_date=<?php echo $default_pickup; ?>&return_date=<?php echo $default_return; ?>&count=<?php echo $count; ?>" class="register_button">Registrieren</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../components/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const pickupDate = document.getElementById("pickup_date");
    const returnDate = document.getElementById("return_date");
    const basePriceElement = document.getElementById("base_price");
    const loyaltyDiscountElement = document.getElementById("loyalty_discount");
    const finalPriceElement = document.getElementById("final_price");
    const bookButton = document.getElementById("book_button");
    const pricePerDay = <?php echo $car["price"]; ?>;
    const useLoyaltyCheckbox = document.getElementById("use_loyalty");
    let userLoyaltyPoints = <?php echo $user_loyalty_points; ?>;

    function calculatePrice() {
        let startDate = new Date(pickupDate.value);
        let endDate = new Date(returnDate.value);
        let days = (endDate - startDate) / (1000 * 60 * 60 * 24);
        let totalPrice = days * pricePerDay;

        let discount = 0;
        if (useLoyaltyCheckbox.checked) {
            let maxDiscount = Math.floor(userLoyaltyPoints / 10) * 10;
            discount = Math.min(maxDiscount, Math.floor(totalPrice / 10) * 10);
            totalPrice -= discount;
        }

        basePriceElement.innerText = (days * pricePerDay).toFixed(2).replace('.', ',') + "€";
        loyaltyDiscountElement.innerText = discount > 0 ? "-" + discount.toFixed(2).replace('.', ',') + "€" : "-0,00€";
        finalPriceElement.innerText = totalPrice.toFixed(2).replace('.', ',') + "€";

        bookButton.disabled = (days < 1);
    }

    pickupDate.addEventListener("input", calculatePrice);
    returnDate.addEventListener("input", calculatePrice);
    useLoyaltyCheckbox.addEventListener("change", calculatePrice);

    calculatePrice();
});
</script>

</body>
</html>
