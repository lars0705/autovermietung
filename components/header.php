<?php
// start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// auto-login via cookies if session is not set
if (!isset($_SESSION["user_id"]) && isset($_COOKIE["user_id"])) {
    $_SESSION["user_id"] = $_COOKIE["user_id"];
    $_SESSION["username"] = $_COOKIE["username"];
}

//check if user is logged in
$isLoggedIn = isset($_SESSION["user_id"]);

?>

<header class="header">
    <div class="logo">
        <a href="../pages/index.php"><img src="../assets/images/sigmacars_logo.png" alt="SigmaCars Logo"></a>
    </div>
    <nav class="nav_buttons">

    <!-- show nav-buttons for logged in -->
    <?php if ($isLoggedIn): ?>
        <a href="../pages/profile.php" class="nav_button">Profil</a>
        <a href="../pages/bookings.php" class="nav_button">Buchungen</a>
        <a href="../pages/logout.php" class="nav_button">Abmelden</a>
    
    <!-- show nav-buttons for not logged in -->
        <?php else: ?>
        <a href="../pages/login.php" class="nav_button">Anmelden</a>
        <a href="../pages/register.php" class="nav_button">Registrieren</a>
    <?php endif; ?>
    </nav>
</header>
