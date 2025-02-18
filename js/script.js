document.addEventListener("DOMContentLoaded", function () {
    const filterContainer = document.getElementById("filter-container");
    const header = document.querySelector(".header");
    const headerHeight = header.offsetHeight;

    window.addEventListener("scroll", function () {
        if (window.scrollY > headerHeight + 20) {
            filterContainer.classList.add("fixed-filter");
        } else {
            filterContainer.classList.remove("fixed-filter");
        }
    });
});
