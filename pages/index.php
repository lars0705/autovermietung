<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Sigmacars | Startseite</title>
    <link rel="stylesheet" href="../css/style.css" />
    <style>
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

      </style>
  </head>
  <body>
    <?php include '../components/header.php'; ?>
    <div class="main_content">
      <?php include '../components/filterform_index.php'; ?> 
      <?php include '../components/index_content.php'; ?>
      <?php include '../components/index_feedback.php'; ?>
    </div>
    <?php include '../components/footer.php'; ?>
    <script src="../js/script_categories_animation.js"></script>
  </body>
</html>
