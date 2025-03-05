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
    <?php
    $show_welcome = !isset($_GET['pickup_date']) || !isset($_GET['return_date']) || empty($_GET['pickup_date']) || empty($_GET['return_date']);
    ?>

    <?php if ($show_welcome): ?>
        <div class="welcome_section">
            <img src="../assets/images/welcome_picture.jpg" alt="Willkommen bei SigmaCars">
            <div class="welcome_text">
              <h2>Finde dein perfektes Auto!</h2>
              <p>Gib deine Reisedaten ein, um verfügbare Fahrzeuge anzuzeigen.</p>
            </div>
        </div>
    <?php else: ?>
        <?php include '../components/product_card.php'; ?>
    <?php endif; ?>
  </div>
  <?php include '../components/footer.php'; ?>
  </body>
</html>



