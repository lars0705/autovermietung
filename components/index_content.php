<h1 class="section-title">Kategorien</h1>
<div class="categorie_container">
    <button class="item category_card" data-category="limousine"><img src="../assets/images/limousine_cat.png" alt="Limousine"></button>
    <button class="item category_card" data-category="suv"><img src="../assets/images/suv_cat.png" alt="SUV"></button>
    <button class="item category_card" data-category="cabrio"><img src="../assets/images/cabrio_cat.png" alt="Cabrio"></button>
    <button class="item category_card" data-category="coupé"><img src="../assets/images/coupe_cat.png" alt="Coupé"></button>
    <button class="item category_card" data-category="kombi"><img src="../assets/images/kombi_cat.png" alt="Kombi"></button>
    <button class="item category_card" data-category="mehrsitzer"><img src="../assets/images/mehrsitzer_cat.png" alt="Mehrsitzer"></button>
</div>
<div class="about-container">
    <div class="about-text">
        <h2>Über Uns</h2>
        <p>
            Willkommen bei <strong>SigmaCars</strong> – Ihre Premium-Autovermietung für exklusive Fahrzeuge. 
            Egal ob Sportwagen, luxuriöse Limousine oder praktischer SUV – wir bieten Ihnen das perfekte 
            Fahrzeug für jeden Anlass.  
        </p>
        <p>
            Qualität, Komfort und exzellenter Service stehen bei uns an erster Stelle. 
            Entdecken Sie unser einzigartiges Angebot und erleben Sie pure Fahrfreude.
        </p>
    </div>
    <div class="about-image">
        <img src="../assets/images/about_us.png" alt="Über uns Bild">
    </div>
</div>

<h2 class="section-title">Unsere Empfehlungen für Sie</h2>

<div class="city-recommendations">
    <div class="city" data-video="../assets/videos/berlin.mp4">
        <img src="../assets/images/berlin.png" alt="Berlin">
        <video class="city-video" src="../assets/videos/berlin.mp4" muted loop></video>
        <div class="city-info">
            <h3>Berlin</h3>
            <p>Erleben Sie die pulsierende Hauptstadt mit einzigartiger Kultur und Geschichte.</p>
        </div>
    </div>

    <div class="city" data-video="../assets/videos/hamburg.mp4">
        <img src="../assets/images/hamburg.png" alt="Hamburg">
        <video class="city-video" src="../assets/videos/hamburg.mp4" muted loop></video>
        <div class="city-info">
            <h3>Hamburg</h3>
            <p>Genießen Sie die maritime Atmosphäre und das aufregende Nachtleben.</p>
        </div>
    </div>

    <div class="city" data-video="../assets/videos/muenchen.mp4">
        <img src="../assets/images/muenchen.png" alt="München">
        <video class="city-video" src="../assets/videos/muenchen.mp4" muted loop></video>
        <div class="city-info">
            <h3>München</h3>
            <p>Erleben Sie bayerische Traditionen und atemberaubende Architektur.</p>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const cityElements = document.querySelectorAll(".city");

    cityElements.forEach(city => {
        const video = city.querySelector(".city-video");
        const img = city.querySelector("img");

        city.addEventListener("mouseenter", () => {
            video.style.opacity = "1";  
            img.style.opacity = "0";   
            video.play();
        });

        city.addEventListener("mouseleave", () => {
            video.style.opacity = "0"; 
            img.style.opacity = "1";   
            video.pause();
            video.currentTime = 0;
        });
    });
});
</script>



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

        // Falls ein Standort-Input existiert und nicht leer ist, füge ihn zur URL hinzu
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
