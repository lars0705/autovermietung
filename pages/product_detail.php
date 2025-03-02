<<<<<<< HEAD
<?php
// Verbindung zur Datenbank
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "car_rental_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Fahrzeug-ID aus URL abrufen
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fahrzeugdetails aus der Datenbank abrufen
$sql = "SELECT type, drive, gear, seats, doors, air_condition, gps, min_age, vendor_name, name, img_file_name, price FROM car_rental_data WHERE type_id = $id";
$result = $conn->query($sql);
$car = $result->fetch_assoc();

$conn->close();
=======
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="../css/style.css" />
    <title>Sigmacars | Fahrzeugdetails</title>
  </head>
  <body>
    <?php include '../components/header.php'; ?>
    <?php
// Fahrzeugdaten aus URL abrufen
$id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : "Unbekannt";
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : "Unbekannt";
$vendor = isset($_GET['vendor']) ? htmlspecialchars($_GET['vendor']) : "Unbekannt";
$seats = isset($_GET['seats']) ? htmlspecialchars($_GET['seats']) : "Unbekannt";
$doors = isset($_GET['doors']) ? htmlspecialchars($_GET['doors']) : "Unbekannt";
$price = isset($_GET['price']) ? htmlspecialchars($_GET['price']) : "Unbekannt";
$image = isset($_GET['image']) ? htmlspecialchars($_GET['image']) : "";
>>>>>>> 443b898ab59dce8bcbd8b7bb93d66b6274d1e538
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($car["vendor_name"]) . " " . htmlspecialchars($car["name"]); ?> - Details</title>
    <link rel="stylesheet" href="../css/style_product_detail.css"> <!-- Neue CSS-Datei -->
</head>
<body>

<div class="product_detail_container">
    
    <!-- Fahrzeugbild & Preisübersicht -->
    <div class="top_section">
        <h2><?php echo htmlspecialchars($car["vendor_name"]) . " " . htmlspecialchars($car["name"]); ?></h2>
        <div class="car_image">
        <?php 
        $imagePath = "../assets/images/" . $car["img_file_name"];
        if (!empty($car["img_file_name"]) && file_exists($imagePath)): ?>
          <img src="<?php echo htmlspecialchars($imagePath); ?>">
        <?php else: ?>
          <img src="../assets/images/Placeholder_car.png">
        <?php endif; ?>
        </div>
        <p class="car_price_large"><?php echo htmlspecialchars($car["price"]); ?>€ / Tag <span class="km_info">300km / Tag</span></p>
        <button class="book_button">Jetzt buchen</button>
    </div>

    <!-- Technische Daten aus der Datenbank -->
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

<!-- Zurück zur Produktübersicht -->
<button id="back_button" class="back_button">Zurück zur Fahrzeugübersicht</button>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Scrollposition beim Laden der Seite speichern
        localStorage.setItem("scrollPosition", window.scrollY);

        document.getElementById("back_button").addEventListener("click", function () {
            let filters = localStorage.getItem("filters");
            let scrollPos = localStorage.getItem("scrollPosition");

            let url = "product_list.php";
            if (filters) {
                url += "?" + filters;
            }

            // Scrollposition direkt speichern, bevor die Seite verlassen wird
            localStorage.setItem("scrollPosition", window.scrollY);

            if (scrollPos) {
                url += "#pos" + scrollPos;
            }

            window.location.href = url;
        });

        // Falls die Seite aus dem Verlauf geladen wird, zur gespeicherten Scrollposition springen
        let storedScrollPos = localStorage.getItem("scrollPosition");
        if (storedScrollPos) {
            window.scrollTo(0, storedScrollPos);
        }
    });
</script>

</body>
</html>
