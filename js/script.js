//damit das Filterfenster beim Scrollen an den Header andockt
document.addEventListener("DOMContentLoaded", function() {
    const filter = document.querySelector(".filter-container");
    const header = document.querySelector(".header");
    
    window.addEventListener("scroll", function() {
        if (window.scrollY >= header.offsetHeight) {
            filter.classList.add("sticky");
        } else {
            filter.classList.remove("sticky");
        }
    });

//damit im Filterfenster das aktuelle Datum angezeigt wird
    let today = new Date().toISOString().split("T")[0]; // Heutiges Datum im Format YYYY-MM-DD

    document.getElementById("pickup-date").value = today; // Setzt das heutige Datum
    document.getElementById("return-date").value = today; // Standardmäßig auch heute
});
