<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Sigmacars | Fahrzeugübersicht</title>
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
  <?php include '../components/filterform_list.php'; ?>
  <h2>Fahrzeugübersicht</h2> 
  <?php include '../components/product_card.php'; ?>
  <?php include '../components/footer.php'; ?>
  </body>
</html>



