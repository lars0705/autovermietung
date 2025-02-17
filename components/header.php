<?php
session_start(); // Session starten, um auf den Anmeldestatus zuzugreifen

$istAngemeldet = isset($_SESSION['user']); // Prüfen, ob der Nutzer angemeldet ist
?>

<header class="header">
    <div class="logo">
        <img src="../assets/images/sigmacars_logo.png" alt="SigmaCars Logo">
    </div>
    <nav class="nav-buttons">
      <?php if (!$istAngemeldet): ?>
        <!-- Wenn der Nutzer abgemeldet ist -->
        <a href="../pages/login.php" class="button">Anmelden</a>
        <a href="../pages/register.php" class="button">Registrieren</a>
      <?php else: ?>
        <!-- Wenn der Nutzer angemeldet ist -->
        <a href="../pages/bookings.php" class="button">Meine Buchungen</a>
        <a href="../pages/account.php" class="button">Accountdetails</a>
        <a href="../pages/logout.php" class="button">Abmelden</a>
      <?php endif; ?>
    </nav>
</header>
