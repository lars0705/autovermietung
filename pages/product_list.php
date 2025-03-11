<?php
ob_start();
session_start();
require_once "../components/db_connect.php"; 

// determine whether to show welcome image
$show_welcome = !isset($_GET['pickup_date']) || !isset($_GET['return_date']) || 
                empty($_GET['pickup_date']) || empty($_GET['return_date']);

if (!$show_welcome) {

    // validate dates to make sure they are not in the past
    $current_date = date('Y-m-d');
    if ($_GET['pickup_date'] < $current_date || $_GET['return_date'] < $current_date) {
        
        // if dates are invalid, reload page wihout parameters
        $url = strtok($_SERVER["REQUEST_URI"], '?'); 
        header("Location: $url");
        exit();
    }
}

ob_end_flush();
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Sigmacars | Fahrzeugübersicht</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
  <?php include '../components/header.php'; ?>
  <div class="main_content">
    <?php include '../components/filterform_list.php'; ?>

    <?php if ($show_welcome): ?>

        <!-- show welcome section when dates are not set -->
        <div class="welcome_section">
          <img src="../assets/images/welcome_picture.jpg" alt="Willkommen bei SigmaCars">
          <div class="welcome_text">
            <h2>Finde dein perfektes Auto!</h2>
            <p>Gib deine Reisedaten ein, um verfügbare Fahrzeuge anzuzeigen.</p>
          </div>
        </div>
    <?php else: ?>

        <!-- show product cards when dates are set -->
        <?php include '../components/product_card.php'; ?>
    <?php endif; ?>
  </div>
  <?php include '../components/footer.php'; ?>
  </body>
</html>
