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
