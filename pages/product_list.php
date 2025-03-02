<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="../css/style.css" />
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
  <h2>Fahrzeugübersicht</h2>

<!-- Container für Fahrzeuge -->
<div class="container" id="carContainer">
    <!-- Muster-Frame -->
    <div class="car_frame frame_template" id="frameTemplate">
        <h3 class="car_title">Titel</h3>
        <p class="car_info">Details</p>
        <p class="car_price">Preis</p>
        <img class="car_img" src="" alt="Auto">
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
                    frame.classList.remove("frame_template");

                    frame.querySelector(".car_title").innerText = car.vendor_name + " " + car.name;
                    frame.querySelector(".car_info").innerText = `Sitze: ${car.seats} | Türen: ${car.doors}`;
                    frame.querySelector(".car_price").innerText = `Preis: ${car.price}€ pro Tag`;

                    if (car.img_file_name) {
                        frame.querySelector(".car_img").src = "../images/" + car.img_file_name;
                    } else {
                        frame.querySelector(".car_img").style.display = "none";
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
    
    <?php include '../components/header.php'; ?>
    <?php include '../components/filterform_list.php'; ?>
    <?php include '../components/load_database.php'; ?>
    <?php include '../components/footer.php'; ?>
  </body>
</html>



