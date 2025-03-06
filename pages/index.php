<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Sigmacars | Startseite</title>
    <link rel="stylesheet" href="../css/style.css" />
    <style>
      /* Feedback-Slider */
      .feedback-slider {
        max-width: 500px;
        margin: 20px auto;
        text-align: center;
        position: relative;
      }

      .feedback-slide {
        display: none;
      }

      .feedback-slide.active {
        display: block;
      }

      .feedback-text {
        font-style: italic;
      }

      .feedback-rating {
        color: gold;
      }

      /* Intro-Animation */
      #intro-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: black;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 1;
        transition: opacity 0.6s ease-in-out;
      }

      #intro-logo {
        width: 180px; /* Logo-Größe leicht verkleinert */
        opacity: 0;
        transition: opacity 0.8s ease-in-out;
      }
    </style>
  </head>
  <body>
    
    <!-- Intro-Animation -->
    <div id="intro-overlay">
      <img src="../assets/images/sigmacars_logo.png" alt="Sigmacars Logo" id="intro-logo">
    </div>

    <?php include '../components/header.php'; ?>

    <div class="main_content">
      <?php include '../components/filterform_index.php'; ?> 
      <?php include '../components/index_content.php'; ?>
      <?php include '../components/index_feedback.php'; ?>
    </div>

    <?php include '../components/footer.php'; ?>

    <script src="../js/script_categories_animation.js"></script>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
          const overlay = document.getElementById("intro-overlay");
          const logo = document.getElementById("intro-logo");

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
    
  </body>
</html>
