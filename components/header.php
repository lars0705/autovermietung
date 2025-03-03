<?php
session_start(); // Session starten, um auf den Anmeldestatus zuzugreifen

$istAngemeldet = isset($_SESSION['user']); // Prüfen, ob der Nutzer angemeldet ist
?>

<header class="header">
    <div class="logo">
        <a href="../pages/index.php"><img src="../assets/images/sigmacars_logo.png" alt="SigmaCars Logo"></a>
    </div>
    <nav class="nav_buttons">
    <?php if (isset($_SESSION["user_id"]) || isset($_COOKIE["user_id"])): ?>
        <a href="../pages/profile.php" class="button">Profil</a>
        <a href="../pages/bookings.php" class="button">Bestellungen</a>
        <a href="../pages/logout.php" class="button">Abmelden</a>
    <?php else: ?>
        <a href="../pages/login.php" class="button">Anmelden</a>
        <a href="../pages/register.php" class="button">Registrieren</a>
    <?php endif; ?>
    </nav>
</header>
