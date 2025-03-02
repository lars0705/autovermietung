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
<div class="filter_container sticky">
  <form class="filter_form" action="product_list.php" method="GET">
    <div class="filter_group">
      <label for="location">Standorte</label>
      <select id="location" name="location">
        <option value="berlin">Berlin</option>
        <option value="bielefeld">Bielefeld</option>
        <option value="bochum">Bochum</option>
        <option value="bremen">Bremen</option>
        <option value="dortmund">Dortmund</option>
        <option value="dresden">Dresden</option>
        <option value="freiburg">Freiburg</option>
        <option value="hamburg" selected>Hamburg</option>
        <option value="köln">Köln</option>
        <option value="leipzig">Leipzig</option>
        <option value="münchen">München</option>
        <option value="nürnberg">Nürnberg</option>
        <option value="paderborn">Paderborn</option>
        <option value="rostock">Rostock</option>
      </select>
    </div>

    <div class="filter_group">
        <label for="pickup_date">Abholdatum</label>
        <input type="date" id="pickup_date" name="pickup_date">
    </div>

      <div class="filter_group">
          <label for="return_date">Rückgabedatum</label>
          <input type="date" id="return_date" name="return_date">
      </div>

      <div class="filter_group">
          <label for="category">Fahrzeugkategorie</label>
          <select id="category" name="category">
            <option value="" selected hidden>Beliebig</option>
            <option value="limousine">Limousine</option>
            <option value="suv">SUV</option>
            <option value="cabrio">Cabrio</option>
            <option value="coupe">Coupe</option>
            <option value="kombi">Kombi</option>
            <option value="van">Van</option>
          </select>
      </div>

      <div class="filter_group">
        <label for="brand">Marke</label>
        <select id="brand" name="brand">
          <option value="" selected hidden>Beliebig</option>
          <option value="bmw">BMW</option>
          <option value="mercedes_benz">Mercedes-Benz</option>
          <option value="mercedes_benz_amg">Mercedes-Benz AMG</option>
          <option value="audi">Audi</option>
          <option value="volkswagen">Volkswagen</option>
          <option value="jaguar">Jaguar</option>
          <option value="range_rover">Range Rover</option>
          <option value="maserati">Maserati</option>
          <option value="opel">Opel</option>
          <option value="ford">Ford</option>
          <option value="skoda">Skoda</option>
        </select>
      </div>

      <div class="filter_group">
          <label for="drivetrain">Antrieb</label>
          <select id="drivetrain" name="drivetrain">
            <option value="" selected hidden>Beliebig</option>
            <option value="verbrenner">Verbrenner</option>
            <option value="elektro">Elektro</option>
          </select>
      </div>

      <div class="filter_group">
          <label for="transmission">Getriebe</label>
          <select id="transmission" name="transmission">
            <option value="" selected hidden>Beliebig</option>
            <option value="automatik">Automatik</option>
            <option value="schaltung">Schaltung</option>
          </select>
      </div>

      <div class="filter_group">
          <label for="seats">Sitzplätze</label>
          <select id="seats" name="seats">
            <option value="" selected hidden>Beliebig</option>
            <option value="2">2</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
          </select>
      </div>

      <div class="filter_group">
        <label for="doors">Türen</label>
        <select id="doors" name="doors">
          <option value="" selected hidden>Beliebig</option>
          <option value="2">2</option>
          <option value="4">4</option>
          <option value="5">5</option>
        </select>
      </div>

      <div class="filter_group">
        <label for="ac">Klimaanlage</label>
        <input type="checkbox" id="ac" name="ac" value="true">
      </div>

      <div class="filter_group">
        <label for="gps">GPS</label>
        <input type="checkbox" id="gps" name="gps" value="true">
      </div>

      <div class="filter_group">
        <label for="min_age">Mindestalter</label>
        <select id="min_age" name="min_age">
          <option value="" selected hidden>Beliebig</option>
          <option value="18">18</option>
          <option value="21">21</option>
          <option value="25">25</option>
        </select>
      </div>

      <div class="filter_group">
        <label for="max_price">Preisgrenze: <span id="price value">0</span> €</label>
        <input type="range" id="max_price" name="max_price" min="0" max="900" step="10" value="0">
      </div>
                  
      <button type="submit" class="button">Filter anwenden</button>
  </form>
</div>


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



