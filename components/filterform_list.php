<?php
$sort_order = $_GET['order'] ?? 'asc';
?>

<div class="filter_container product_list">
  <form class="filter_form" id="filter_form" action="product_list.php" method="GET">
    <div class="filter_spalte">
      <div class="filter_group">
        <label for="location">Standorte</label>
        <select id="location" name="location">
          <?php
          $locations = ["berlin", "bielefeld", "bochum", "bremen", "dortmund", "dresden", "freiburg", "hamburg", "köln", "leipzig", "münchen", "nürnberg", "paderborn", "rostock"];
          foreach ($locations as $loc) {
            $selected = isset($_GET['location']) && $_GET['location'] == $loc ? 'selected' : '';
            echo "<option value='$loc' $selected>" . ucfirst($loc) . "</option>";
          }
          ?>
        </select>
      </div>

      <div class="filter_group">
        <label for="doors">Türen</label>
        <select id="doors" name="doors">
          <option value="" <?php echo (!isset($_GET['doors']) || $_GET['doors'] == '') ? 'selected' : ''; ?>>Beliebig</option>
          <?php
          $doors = ["2", "4", "5"];
          foreach ($doors as $door) {
              $selected = (isset($_GET['doors']) && $_GET['doors'] == $door) ? 'selected' : '';
              echo "<option value='$door' $selected>$door</option>";
          }
          ?>
        </select>
      </div>
    </div>

    <div class="filter_spalte">
      <div class="filter_group">
        <label for="pickup_date">Abholdatum</label>
        <input type="date" id="pickup_date" name="pickup_date" value="<?php echo isset($_GET['pickup_date']) ? $_GET['pickup_date'] : ''; ?>">
      </div>

      <div class="filter_group">
        <label for="min_age">Jüngster Fahrer</label>
        <select id="min_age" name="min_age">
          <option value="" <?php echo (!isset($_GET['min_age']) || $_GET['min_age'] == '') ? 'selected' : ''; ?>>Beliebig</option>
          <?php
          $ages = ["18", "21", "25"];
          foreach ($ages as $age) {
              $selected = (isset($_GET['min_age']) && $_GET['min_age'] == $age) ? 'selected' : '';
              echo "<option value='$age' $selected>$age</option>";
          }
          ?>
        </select>
      </div>
    </div>

    <div class="filter_spalte">
      <div class="filter_group">
        <label for="return_date">Rückgabedatum</label>
        <input type="date" id="return_date" name="return_date" value="<?php echo isset($_GET['return_date']) ? $_GET['return_date'] : ''; ?>">
      </div>

      <div class="filter_group">
        <label for="ac">Klimaanlage</label>
        <input type="checkbox" id="ac" name="ac" value="true" <?php echo isset($_GET['ac']) ? 'checked' : ''; ?>>
      </div>
    </div>
    
    <div class="filter_spalte">
      <div class="filter_group">
        <label for="category">Fahrzeugkategorie</label>
        <select id="category" name="category">
        <option value="" <?php echo (!isset($_GET['category']) || $_GET['category'] == '') ? 'selected' : ''; ?>>Beliebig</option>
          <?php
          $categories = ["limousine", "suv", "cabrio", "coupé", "kombi", "mehrsitzer"];
          foreach ($categories as $cat) {
            $selected = isset($_GET['category']) && $_GET['category'] == $cat ? 'selected' : '';
            echo "<option value='$cat' $selected>" . ucfirst($cat) . "</option>";
          }
          ?>
        </select>
      </div>

      <div class="filter_group">
        <label for="gps">GPS</label>
        <input type="checkbox" id="gps" name="gps" value="true" >
      </div>
    </div>

    <div class="filter_spalte">
      <div class="filter_group">
        <label for="brand">Marke</label>
        <select id="brand" name="brand">
          <option value="" <?php echo (!isset($_GET['brand']) || $_GET['brand'] == '') ? 'selected' : ''; ?>>Beliebig</option>
          <?php
          $brands = [
              "bmw" => "BMW",
              "mercedes-benz" => "Mercedes-Benz",
              "mercedes-amg" => "Mercedes-Benz AMG",
              "audi" => "Audi",
              "vw" => "Volkswagen",
              "jaguar" => "Jaguar",
              "range rover" => "Range Rover",
              "maserati" => "Maserati",
              "opel" => "Opel",
              "ford" => "Ford",
              "skoda" => "Skoda"
          ];
          foreach ($brands as $key => $label) {
              $selected = (isset($_GET['brand']) && $_GET['brand'] == $key) ? 'selected' : '';
              echo "<option value='$key' $selected>$label</option>";
          }
          ?>
        </select>
      </div>

      <div class="filter_group">
        <label for="max_price">Max. Preis: <span id="price_value">1000</span>€/Tag</label>
        <input type="range" id="max_price" name="max_price" min="0" max="900" step="10" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : '900'; ?>">
      </div>
    </div>

    <div class="filter_spalte">
      <div class="filter_group">
        <label for="drivetrain">Antrieb</label>
        <select id="drivetrain" name="drivetrain">
          <option value="" <?php echo (!isset($_GET['drivetrain']) || $_GET['drivetrain'] == '') ? 'selected' : ''; ?>>Beliebig</option>
          <?php
          $drivetrains = [
              "combuster" => "Verbrenner",
              "electric" => "Elektro"
          ];
          foreach ($drivetrains as $key => $label) {
              $selected = (isset($_GET['drivetrain']) && $_GET['drivetrain'] == $key) ? 'selected' : '';
              echo "<option value='$key' $selected>$label</option>";
          }
          ?>
        </select>
      </div>

      <div class="filter_group">
        <button type="button" id="custom_reset" class="reset_button">Filter zurücksetzen</button>
      </div>
    </div>

    <div class="filter_spalte">
      <div class="filter_group">
        <label for="transmission">Getriebe</label>
        <select id="transmission" name="transmission">
          <option value="" <?php echo (!isset($_GET['transmission']) || $_GET['transmission'] == '') ? 'selected' : ''; ?>>Beliebig</option>
          <?php
          $transmissions = [
              "automatic" => "Automatik",
              "manually" => "Schaltung"
          ];
          foreach ($transmissions as $key => $label) {
              $selected = (isset($_GET['transmission']) && $_GET['transmission'] == $key) ? 'selected' : '';
              echo "<option value='$key' $selected>$label</option>";
          }
          ?>
        </select>
      </div>

      <div class="filter_group">
        <button type="submit" id="submit_button" class="submit_button">Filter anwenden</button>
      </div>
    </div>

    <div class="filter_spalte">
      <div class="filter_group">
        <label for="seats">Sitzplätze</label>
        <select id="seats" name="seats">
            <option value="" <?php echo (!isset($_GET['seats']) || $_GET['seats'] == '') ? 'selected' : ''; ?>>Beliebig</option>
            <?php
            $seats = ["2", "4", "5", "7", "8", "9"];
            foreach ($seats as $seat) {
                $selected = (isset($_GET['seats']) && $_GET['seats'] == $seat) ? 'selected' : '';
                echo "<option value='$seat' $selected>$seat</option>";
            }
            ?>
          </select>
      </div>
      
      <div class="filter_group">
        <label for="sort">Sortierung</label>
        <select id="sort" name="order">
          <option value="asc" <?php echo ($sort_order === 'asc') ? 'selected' : ''; ?>>Preis: Aufsteigend</option>
          <option value="desc" <?php echo ($sort_order === 'desc') ? 'selected' : ''; ?>>Preis: Absteigend</option>
        </select>   
      </div>
    </div>

  </form>
  <div id="error_message" class="error_message"></div>
</div>
    

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.filter_form');
    const pickupDateInput = document.getElementById('pickup_date');
    const returnDateInput = document.getElementById('return_date');
    const errorMessageDiv = document.getElementById('error_message');
    const submitButton = document.getElementById('submit_button');
    const priceSlider = document.getElementById('max_price');
    const priceValue = document.getElementById('price_value');
    const resetButton = document.getElementById('custom_reset');

    // Funktion zur Validierung der Datumswerte
    function validateDates() {
        let errorMessage = '';
        const pickupDate = new Date(pickupDateInput.value);
        const returnDate = new Date(returnDateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (!pickupDateInput.value || !returnDateInput.value) {
            errorMessage = 'Bitte Abhol- und Rückgabedatum eingeben.';
        } else if (pickupDate < today) {
            errorMessage = 'Das Abholdatum darf nicht in der Vergangenheit liegen.';
        } else if (returnDate <= pickupDate) {
            errorMessage = 'Das Rückgabedatum muss nach dem Abholdatum liegen.';
        }

        if (errorMessage) {
            errorMessageDiv.textContent = errorMessage;
            errorMessageDiv.style.display = 'block';
            return false;
        } else {
            errorMessageDiv.style.display = 'none';
            return true;
        }
    }

    // Event-Listener für das Absenden des Formulars
    form.addEventListener('submit', function(event) {
        if (!validateDates()) {
            event.preventDefault(); // Verhindert das Absenden des Formulars, falls Fehler vorliegen
        }
    });

    // Preis-Slider aktualisiert den Wert in der Anzeige
    priceValue.textContent = priceSlider.value;
    priceSlider.addEventListener("input", function() {
        priceValue.textContent = this.value;
    });

    // Filter zurücksetzen, aber Abhol-/Rückgabedatum und Standort beibehalten
    resetButton.addEventListener("click", function() {
      const filterFields = form.querySelectorAll("select, input:not([type='date']):not([type='hidden'])");

    filterFields.forEach(field => {
        if (field.tagName === "SELECT" && field.id !== "location") {
            field.value = ""; // Setzt Select-Felder auf "Beliebig"
        } else if (field.type === "checkbox") {
            field.checked = false; // Entfernt Haken bei Checkboxen
        } else if (field.type === "range") {
            field.value = 900; // Setzt Max-Preis auf 900€
            priceValue.textContent = 900;
        }
    });

    document.getElementById("sort").value = "asc";

    form.submit();
    });
});
</script>

