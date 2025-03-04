<h1 class="section-title">Kategorien</h1>
<div class="categorie_container">
    <button class="item" onclick="selectCategory('limousine')"><img src="../assets/images/limousine_cat.png" alt="Limousine"></button>
    <button class="item" onclick="selectCategory('suv')"><img src="../assets/images/suv_cat.png" alt="SUV"></button>
    <button class="item" onclick="selectCategory('cabrio')"><img src="../assets/images/cabrio_cat.png" alt="Cabrio"></button>
    <button class="item" onclick="selectCategory('coupé')"><img src="../assets/images/coupe_cat.png" alt="Coupé"></button>
    <button class="item" onclick="selectCategory('kombi')"><img src="../assets/images/kombi_cat.png" alt="Kombi"></button>
    <button class="item" onclick="selectCategory('mehrsitzer')"><img src="../assets/images/mehrsitzer_cat.png" alt="Mehrsitzer"></button>
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

<script>
function selectCategory(category) {
    console.log("Kategorie gewählt:", category); // Debugging

    const location = document.getElementById("location").value;
    console.log("Standort gewählt:", location); // Debugging

    if (!location) {
        alert("Bitte wählen Sie zuerst einen Standort aus!");
        return;
    }

    // Überprüfung: Wurde die URL korrekt gesetzt?
    const url = `product_list.php?location=${location}&category=${category}`;
    console.log("Weiterleitung zu:", url); // Debugging

    // Weiterleitung zur Produktliste mit den Filtern
    window.location.href = url;
}
</script>