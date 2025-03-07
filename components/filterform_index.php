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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.filter_form');
    const pickupDateInput = document.getElementById('pickup_date');
    const returnDateInput = document.getElementById('return_date');
    const errorMessageDiv = document.getElementById('error_message');
    const submitButton = document.getElementById('submit_button');

    form.addEventListener('submit', function(event) {
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
            event.preventDefault();
            errorMessageDiv.textContent = errorMessage;
            errorMessageDiv.style.display = 'block';
        } else {
            errorMessageDiv.style.display = 'none';
        }
    });
});
</script>