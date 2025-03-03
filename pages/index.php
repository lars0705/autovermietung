<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
<<<<<<< HEAD
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/style_index.css" />
=======
>>>>>>> 487dcff27d13fbee67cbde2c57ced873809e5bc3
    <title>Sigmacars | Startseite</title>
    <link rel="stylesheet" href="../css/style.css" />
    <?php
        $currentPage = basename($_SERVER['PHP_SELF'], ".php"); // Holt den Dateinamen ohne .php
        $cssFile = "../css/style_" . $currentPage . ".css"; // Baut den Pfad zur CSS-Datei
        if (file_exists($cssFile)) { // Prüft, ob die Datei existiert
            echo '<link rel="stylesheet" href="' . $cssFile . '">';
        }
    ?>
  </head>
  <body>
    <?php include '../components/header.php'; ?>
    <?php include '../components/filterform_index.php'; ?> 
    <?php include '../components/index_content.php'; ?>

    <!-- <main class="main_content">
       Platzhalter für den weiteren Inhalt 
      <div class="content_section"> 
           <div style="height: 1500px;"></div> Zum Testen des Scrollens
      </div>
    </main> -->
    <?php include '../components/footer.php'; ?>
    <script src="../js/script.js"></script>
  </body>
</html>
