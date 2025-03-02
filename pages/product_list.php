<!DOCTYPE html>
<html>
<?php include '../components/header.php'; ?>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="../css/style.css" />
    

    <!-- Main -->
    <title>Fahrzeugübersicht</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }
        .frame-template {
            display: none;
        }
        .car-frame {
            width: 300px;
            padding: 15px;
            background-color: #222;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
            text-align: center;
        }
        img {
            width: 100%;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<?php include 'filterform_list.php'; ?> <!-- Filterformular wird eingebunden -->

<h2>Fahrzeugübersicht</h2>

<!-- Container für Fahrzeuge -->
<div class="container" id="carContainer">
    <!-- Muster-Frame -->
    <div class="car-frame frame-template" id="frameTemplate">
        <h3 class="car-title">Titel</h3>
        <p class="car-info">Details</p>
        <p class="car-price">Preis</p>
        <img class="car-img" src="" alt="Auto">
    </div>
</div>

<script>
    // Funktion zum Laden der Fahrzeuge mit Filter
    function loadFilteredCars() {
        let params = new URLSearchParams(new FormData(document.querySelector(".filter_form")));
        
        fetch('../components/load_database.php?' + params.toString()) // Holt Daten von der PHP-Datei
            .then(response => response.json()) // Antwort als JSON verarbeiten
            .then(cars => {
                let container = document.getElementById("carContainer");
                container.innerHTML = ""; // Vorherige Fahrzeuge entfernen
                let template = document.getElementById("frameTemplate");

                cars.forEach(car => {
                    let frame = template.cloneNode(true);
                    frame.style.display = "block";
                    frame.classList.remove("frame-template");

                    frame.querySelector(".car-title").innerText = car.vendor_name + " " + car.name;
                    frame.querySelector(".car-info").innerText = `Sitze: ${car.seats} | Türen: ${car.doors}`;
                    frame.querySelector(".car-price").innerText = `Preis: ${car.price}€ pro Tag`;

                    if (car.img_file_name) {
                        frame.querySelector(".car-img").src = "../images/" + car.img_file_name;
                    } else {
                        frame.querySelector(".car-img").style.display = "none";
                    }

                    container.appendChild(frame);
                });
            })
            .catch(error => console.error("Fehler beim Laden der Daten:", error));
    }

    // Event-Listener für das Filterformular
    document.querySelector(".filter_form").addEventListener("submit", function(event) {
        event.preventDefault(); // Verhindert das Neuladen der Seite
        loadFilteredCars();
    });

    // Initiale Ladung der Fahrzeuge beim Seitenstart
    loadFilteredCars();
</script>

<!-- Main Ende -->


    </main>
    <?php include '../components/load_database.php'; ?>
    <?php include '../components/footer.php'; ?>
  </body>
</html>



