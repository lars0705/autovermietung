<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Sigmacars | Startseite</title>
    <link rel="stylesheet" href="../css/style.css" />
  </head>
  <body>
    <div id="intro_overlay" class="intro_overlay">
      <img src="../assets/images/sigmacars_logo.png" alt="Sigmacars Logo" id="intro_logo" class="intro_logo" />
    </div>

    <?php include '../components/header.php'; ?>

    <div class="main_content">
      <?php include '../components/filterform_index.php'; ?> 
      <?php include '../components/index_categories.php'; ?>
      <?php include '../components/index_treuepunkte.php'; ?>
      <?php include '../components/index_feedback.php'; ?>
      <?php include '../components/index_about_us.php'; ?>
    </div>

    <?php include '../components/footer.php'; ?>
  </body>
</html>

<script>
      document.addEventListener("DOMContentLoaded", function () {
          const overlay = document.getElementById("intro_overlay");
          const logo = document.getElementById("intro_logo");

          // Logo fade-in (0.3s Verzögerung)
          setTimeout(() => {
              logo.style.opacity = "1";
          }, 300);

          // Logo fade-out  (1.2s nach Start)
          setTimeout(() => {
              logo.style.opacity = "0";
          }, 1200);

          // Schwarzer Bildschirm ausblenden  (1.8s nach Start)
          setTimeout(() => {
              overlay.style.opacity = "0";
              setTimeout(() => {
                  overlay.style.display = "none";
              }, 600);
          }, 1800);
      });
    </script>