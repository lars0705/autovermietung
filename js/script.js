//damit das Filterfenster beim Scrollen an den Header andockt
document.addEventListener("DOMContentLoaded", function() {
    const filter = document.querySelector(".filter_container");
    const header = document.querySelector(".header");
    
    window.addEventListener("scroll", function() {
        if (window.scrollY >= header.offsetHeight) {
            filter.classList.add("sticky");
        } else {
            filter.classList.remove("sticky");
        }
    });
});
