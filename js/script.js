// JavaScript zum Fixieren des Filterfensters
window.onscroll = function() {
    stickyFilter();
  };
  
  // Wähle den Filter-Container und den Header aus
  var filter = document.querySelector(".filter-container");
  var header = document.querySelector(".header");
  
  // Berechne die Position des Filterfensters
  var sticky = filter.offsetTop;
  
  // Funktion, um das Filterfenster beim Scrollen zu fixieren
  function stickyFilter() {
    if (window.pageYOffset > sticky) {
      filter.classList.add("fixed-filter");  // Füge die Klasse für das Fixieren hinzu
    } else {
      filter.classList.remove("fixed-filter");  // Entferne die Klasse, wenn nach oben gescrollt wird
    }
  }
  
  