<h1 class="section_title">Kategorien</h1>
<div class="categorie_container">
    <button class="item category_card" data-category="limousine"><img src="../assets/images/limousine_cat.png" alt="Limousine"><p>Limousine</p></button>
    <button class="item category_card" data-category="suv"><img src="../assets/images/suv_cat.png" alt="SUV"><p>SUV</p></button>
    <button class="item category_card" data-category="cabrio"><img src="../assets/images/cabrio_cat.png" alt="Cabrio"><p>Cabrio</p</button>
    <button class="item category_card" data-category="coupé"><img src="../assets/images/coupe_cat.png" alt="Coupé"><p>Coupé</p</button>
    <button class="item category_card" data-category="kombi"><img src="../assets/images/kombi_cat.png" alt="Kombi"><p>Kombi</p</button>
    <button class="item category_card" data-category="mehrsitzer"><img src="../assets/images/mehrsitzer_cat.png" alt="Mehrsitzer"><p>Mehrsitzer</p</button>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const pickupDate = document.getElementById("pickup_date");
    const returnDate = document.getElementById("return_date");
    const locationInput = document.getElementById("location");
    const errorMessage = document.getElementById("error_message");
    const categoryCards = document.querySelectorAll(".category_card");

    function checkDates(showError = false) {
        errorMessage.style.display = "none";
        errorMessage.textContent = "";

        if (!pickupDate.value || !returnDate.value) {
            if (showError) {
                errorMessage.textContent = "Bitte Abhol- und Rückgabedatum eingeben.";
                errorMessage.style.display = "block";
            }
            return false;
        }

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const pickupValue = new Date(pickupDate.value);
        const returnValue = new Date(returnDate.value);

        if (pickupValue < today) {
            if (showError) {
                errorMessage.textContent = "Das Abholdatum darf nicht in der Vergangenheit liegen.";
                errorMessage.style.display = "block";
            }
            return false;
        }

        if (returnValue <= pickupValue) {
            if (showError) {
                errorMessage.textContent = "Das Rückgabedatum muss nach dem Abholdatum liegen.";
                errorMessage.style.display = "block";
            }
            return false;
        }

        return true;
    }

    function selectCategory(category) {
        if (!checkDates(true)) return;

        let url = `product_list.php?category=${category}&pickup_date=${pickupDate.value}&return_date=${returnDate.value}`;

        if (locationInput && locationInput.value) {
            url += `&location=${encodeURIComponent(locationInput.value)}`;
        }

        window.location.href = url;
    }

    categoryCards.forEach(card => {
        card.addEventListener("click", function () {
            const category = this.getAttribute("data-category");
            selectCategory(category);
        });
    });
});
</script>
