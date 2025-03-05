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
        <option value="" >Beliebig</option>
          <option value='limousine' >Limousine</option><option value='suv' >Suv</option><option value='cabrio' >Cabrio</option><option value='coupé' selected>Coupé</option><option value='kombi' >Kombi</option><option value='mehrsitzer' >Mehrsitzer</option>        </select>
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
          <option value="" selected>Beliebig</option>
          <option value='2' >2</option><option value='4' >4</option><option value='5' >5</option><option value='7' >7</option><option value='8' >8</option><option value='9' >9</option>        </select>
      </div>
      
      <div class="filter_group">
        <label for="sort">Sortierung</label>
        <select id="sort" name="order">
          <option value="asc" selected>Preis: Aufsteigend</option>
          <option value="desc" >Preis: Absteigend</option>
        </select>   
      </div>
    </div>

  </form>
  <div id="error_message" class="error_message"></div>
</div>
    

<script> // Javascript Code, um das richtige Einsetzen von Abhol- und Rückgabedatum zu erzwingen
document.addEventListener("DOMContentLoaded", function () {
    const priceSlider = document.getElementById("max_price");
    const priceValue = document.getElementById("price_value");
    const pickupDate = document.getElementById("pickup_date");
    const returnDate = document.getElementById("return_date");
    const submitButton = document.getElementById("submit_button");
    const resetButton = document.getElementById("custom_reset");
    const filterForm = document.querySelector(".filter_form");
    const errorMessage = document.getElementById("error_message");

    function checkDates() {
        errorMessage.style.display = "none";
        errorMessage.textContent = "";

        if (!pickupDate.value || !returnDate.value) {
            errorMessage.textContent = "Bitte Abhol- und Rückgabedatum eingeben.";
            errorMessage.style.display = "block";
            submitButton.setAttribute("disabled", "disabled");
            return false;
        }

        const pickupValue = new Date(pickupDate.value);
        const returnValue = new Date(returnDate.value);

        if (returnValue <= pickupValue) {
            errorMessage.textContent = "Das Rückgabedatum muss nach dem Abholdatum liegen.";
            errorMessage.style.display = "block";
            submitButton.setAttribute("disabled", "disabled");
            return false;
        }
        
        submitButton.removeAttribute("disabled");
        return true;
    }

    pickupDate.addEventListener("input", checkDates);
    returnDate.addEventListener("input", checkDates);

    
    if (pickupDate.value && returnDate.value) {
        checkDates();
    }
    // Java Script Code, um über dem Preis-Slider den aktuellen Wert anzuzeigen
    priceValue.textContent = priceSlider.value;
    priceSlider.addEventListener("input", function () {
        priceValue.textContent = this.value;
    });

    // Java Script Code, um die Filter zurückzusetzen, aber das Abhol- und Rückgabedatum und den Standort beizubehalten
    resetButton.addEventListener("click", function () {
        const filterFields = filterForm.querySelectorAll("select, input:not([type='date']):not([type='hidden'])");
        
        filterFields.forEach(field => {
            if (field.tagName === "SELECT") {
                field.value = "";
            } else if (field.type === "checkbox" || field.type === "radio") {
                field.checked = false;
            } else if (field.type === "range") {
                field.value = field.min;
                priceValue.textContent = field.min;
            }
        });
        
        filterForm.submit();
    });


    filterForm.addEventListener("submit", function (event) {
        if (!checkDates()) {
            event.preventDefault();
        }
    });
});
</script>
