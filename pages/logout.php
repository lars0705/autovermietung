<?php
session_start();
session_destroy();

// Cookies löschen
setcookie("user_id", "", time() - 3600, "/");
setcookie("username", "", time() - 3600, "/");

header("Location: login.php");
exit();
?>
