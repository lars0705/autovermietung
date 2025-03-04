<div class="filter_container index">
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

        <button type="submit" id="submit_button" class="submit_button">Autos anzeigen</button>
    </form> 
    <div id="error_message" class="error_message"></div>
</div>

<script> // Javascript Code, um das richtige Einsetzen von Abhol- und Rückgabedatum zu erzwingen
document.addEventListener("DOMContentLoaded", function () {
    const pickupDate = document.getElementById("pickup_date");
    const returnDate = document.getElementById("return_date");
    const submitButton = document.getElementById("submit_button");
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

    document.querySelector("form").addEventListener("submit", function (event) {
        if (!checkDates()) {
            event.preventDefault();
        }
    });
});
</script>