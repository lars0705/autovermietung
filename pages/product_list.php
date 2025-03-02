<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="../css/style.css" />
    <title>Fahrzeugübersicht</title>
</head>
<body>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

  <?php include '../components/header.php'; ?>
  <?php include '../components/filterform_list.php'; ?>
  <h2>Fahrzeugübersicht</h2> 
  <?php include '../components/load_database.php'; ?>
  <?php include '../components/footer.php'; ?>
  </body>
</html>



