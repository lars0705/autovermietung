<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="../css/style.css" />
    <title>Sigmacars | Startseite</title>
    <script defer src="js/script.js"></script> <!-- JavaScript für das Scroll-Verhalten -->
  </head>
  <body>
    <?php include '../components/header.php'; ?>
    <!-- Filter-Fenster -->
    <div class="filter-container">
        <form class="filter-form" action="pages/produktuebersicht.php" method="GET">
            <label for="location">Standort:</label>
            <input type="text" id="location" name="location" placeholder="Ort eingeben...">

            <label for="date">Datum:</label>
            <input type="date" id="date" name="date">

            <label for="car-type">Fahrzeugtyp:</label>
            <select id="car-type" name="car-type">
                <option value="alle">Alle</option>
                <option value="kleinwagen">Kleinwagen</option>
                <option value="suv">SUV</option>
                <option value="luxus">Luxus</option>
            </select>

            <button type="submit" class="button">Verfügbarkeit prüfen</button>
        </form>
    </div>

    <!-- Platzhalter für den weiteren Inhalt -->
    <main class="main-content">
        <p>Hier kommen die anderen drei Abschnitte hin...</p>
        <div style="height: 1500px;"></div> <!-- Zum Testen des Scrollens -->
    </main>
    <?php include '../components/footer.php'; ?>
  </body>
</html>
