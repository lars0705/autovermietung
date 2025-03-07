<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"]) && isset($_COOKIE["user_id"])) {
    $_SESSION["user_id"] = $_COOKIE["user_id"];
    $_SESSION["username"] = $_COOKIE["username"];
}

$istAngemeldet = isset($_SESSION["user_id"]);

?>

<header class="header">
    <div class="logo">
        <a href="../pages/index.php"><img src="../assets/images/sigmacars_logo.png" alt="SigmaCars Logo"></a>
    </div>
    <nav class="nav_buttons">
    <?php if (isset($_SESSION["user_id"]) || isset($_COOKIE["user_id"])): ?>
        <a href="../pages/profile.php" class="nav_button">Profil</a>
        <a href="../pages/bookings.php" class="nav_button">Bestellungen</a>
        <a href="../pages/logout.php" class="nav_button">Abmelden</a>
    <?php else: ?>
        <a href="../pages/login.php" class="nav_button">Anmelden</a>
        <a href="../pages/register.php" class="nav_button">Registrieren</a>
    <?php endif; ?>
    </nav>
</header>
