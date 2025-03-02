<div class="filter_container fest">
  <form class="filter_form" action="product_list.php" method="GET">
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
      <label for="pickup_date">Abholdatum</label>
      <input type="date" id="pickup_date" name="pickup_date" value="<?php echo isset($_GET['pickup_date']) ? $_GET['pickup_date'] : ''; ?>">
    </div>

    <div class="filter_group">
      <label for="return_date">Rückgabedatum</label>
      <input type="date" id="return_date" name="return_date" value="<?php echo isset($_GET['return_date']) ? $_GET['return_date'] : ''; ?>">
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
        <label for="min_age">Mindestalter</label>
        <select id="min_age" name="min_age">
          <option value="" selected hidden>Beliebig</option>
          <option value="18">18</option>
          <option value="21">21</option>
          <option value="25">25</option>
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
      <label for="category">Fahrzeugkategorie</label>
      <select id="category" name="category">
        <option value="" hidden>Beliebig</option>
        <?php
        $categories = ["limousine", "suv", "cabrio", "coupe", "kombi", "van"];
        foreach ($categories as $cat) {
          $selected = isset($_GET['category']) && $_GET['category'] == $cat ? 'selected' : '';
          echo "<option value='$cat' $selected>" . ucfirst($cat) . "</option>";
        }
        ?>
      </select>
    </div>

    <div class="filter_group">
      <label for="max_price">Preisgrenze: <span id="price_value">0</span> €</label>
      <input type="range" id="max_price" name="max_price" min="0" max="900" step="10" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : '0'; ?>">
    </div>

    <button type="reset" class="reset_button">Filter zurücksetzen</button>
    <button type="submit" class="submit_button">Filter anwenden</button>
  </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const priceSlider = document.getElementById("max_price");
    const priceValue = document.getElementById("price_value");
    
    priceValue.textContent = priceSlider.value;

    priceSlider.addEventListener("input", function () {
        priceValue.textContent = this.value;
    });
});
</script>
