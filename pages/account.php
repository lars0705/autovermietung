<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Sigmacars | Mein Account</title>
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
    <p>meine kontodetails</p>
    <?php include '../components/footer.php'; ?>
  </body>
</html>
