<h1 class="section_title">Kategorien</h1>
<div class="categorie_container">
    <button class="item category_card" data-category="limousine"><img src="../assets/images/limousine_cat.png" alt="Limousine"></button>
    <button class="item category_card" data-category="suv"><img src="../assets/images/suv_cat.png" alt="SUV"></button>
    <button class="item category_card" data-category="cabrio"><img src="../assets/images/cabrio_cat.png" alt="Cabrio"></button>
    <button class="item category_card" data-category="coupé"><img src="../assets/images/coupe_cat.png" alt="Coupé"></button>
    <button class="item category_card" data-category="kombi"><img src="../assets/images/kombi_cat.png" alt="Kombi"></button>
    <button class="item category_card" data-category="mehrsitzer"><img src="../assets/images/mehrsitzer_cat.png" alt="Mehrsitzer"></button>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const pickupDate = document.getElementById("pickup_date");
    const returnDate = document.getElementById("return_date");
    const locationInput = document.getElementById("location");
    const errorMessage = document.getElementById("error_message");
    const categoryCards = document.querySelectorAll(".category_card");

    function checkDates() {
        errorMessage.style.display = "none";
        errorMessage.textContent = "";

        if (!pickupDate.value || !returnDate.value) {
            errorMessage.textContent = "Bitte Abhol- und Rückgabedatum eingeben.";
            errorMessage.style.display = "block";
            categoryCards.forEach(card => card.setAttribute("disabled", "disabled"));
            return false;
        }

        const pickupValue = new Date(pickupDate.value);
        const returnValue = new Date(returnDate.value);

        if (returnValue <= pickupValue) {
            errorMessage.textContent = "Das Rückgabedatum muss nach dem Abholdatum liegen.";
            errorMessage.style.display = "block";
            categoryCards.forEach(card => card.setAttribute("disabled", "disabled"));
            return false;
        }

        categoryCards.forEach(card => card.removeAttribute("disabled"));
        return true;
    }

    pickupDate.addEventListener("input", checkDates);
    returnDate.addEventListener("input", checkDates);

    if (pickupDate.value && returnDate.value) {
        checkDates();
    }

    function selectCategory(category) {
        if (!checkDates()) return;

        let url = `product_list.php?category=${category}&pickup_date=${pickupDate.value}&return_date=${returnDate.value}`;

        if (locationInput && locationInput.value) {
            url += `&location=${encodeURIComponent(locationInput.value)}`;
        }

        window.location.href = url;
    }

    document.querySelectorAll(".categorie_container .item").forEach(item => {
        item.addEventListener("click", function () {
            const category = this.getAttribute("data-category"); 
            selectCategory(category);
        });
    });
});

</script>
