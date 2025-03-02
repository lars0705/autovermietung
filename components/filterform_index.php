<div class="filter_container">
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
        <button type="submit" class="submit_button" disabled>Fahrzeuge anzeigen</button>
    </form> 
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const pickupDate = document.getElementById("pickup_date");
    const returnDate = document.getElementById("return_date");
    const submitButton = document.querySelector(".submit_button");

    function checkDates() {
        if (!pickupDate.value || !returnDate.value) {
            submitButton.setAttribute("disabled", "disabled");
            returnDate.setCustomValidity("");
            return;
        }

        const pickupValue = new Date(pickupDate.value);
        const returnValue = new Date(returnDate.value);

        if (returnValue > pickupValue) {
            submitButton.removeAttribute("disabled");
            returnDate.setCustomValidity("");
        } else {
            submitButton.setAttribute("disabled", "disabled");
            returnDate.setCustomValidity("Das Rückgabedatum muss nach dem Abholdatum liegen.");
        }

        returnDate.reportValidity();
    }

    pickupDate.addEventListener("input", checkDates);
    returnDate.addEventListener("input", checkDates);

    if (pickupDate.value && returnDate.value) {
        checkDates();
    }
});

</script>