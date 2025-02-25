<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="../css/style.css" />
    <title>Sigmacars | Startseite</title>
  </head>
  <body>
    <?php include '../components/header.php'; ?>
    <!-- Filter-Fenster -->
     <main class="main-content">
        <div class="filter-container">
        <form class="filter-form" action="pages/produktuebersicht.php" method="GET">
            <div class="filter-group">
                <label for="location">Standorte</label>
                <select id="location" name="location">
                    <option value="berlin">Berlin</option>
                    <option value="bielefeld">Bielefeld</option>
                    <option value="bochum">Bochum</option>
                    <option value="bremen">Bremen</option>
                    <option value="dortmund">Dortmund</option>
                    <option value="dresden">Dresden</option>
                    <option value="freiburg">Freiburg</option>
                    <option value="hamburg" selected>Hamburg</option>
                    <option value="köln">Köln</option>
                    <option value="leipzig">Leipzig</option>
                    <option value="münchen">München</option>
                    <option value="nürnberg">Nürnberg</option>
                    <option value="paderborn">Paderborn</option>
                    <option value="rostock">Rostock</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="pickup-date">Abholdatum</label>
                <input type="date" id="pickup-date" name="pickup-date">
            </div>

            <div class="filter-group">
                <label for="return-date">Rückgabedatum</label>
                <input type="date" id="return-date" name="return-date">
            </div>

            <button type="submit" class="button">Fahrzeuge anzeigen</button>
        </form>
    </div>


      <!-- Platzhalter für den weiteren Inhalt -->
      <div class="content-section">
          <p>Hier kommen die anderen drei Abschnitte hin...</p>
          <div style="height: 1500px;"></div> <!-- Zum Testen des Scrollens -->
      </div>
    </main>
    <?php include '../components/footer.php'; ?>
    <script src="../js/script.js"></script>
  </body>
</html>
