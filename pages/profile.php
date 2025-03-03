<?php
session_start();
if (!isset($_SESSION["user_id"]) && !isset($_COOKIE["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../components/db_connect.php"; 

$user_id = $_SESSION["user_id"] ?? $_COOKIE["user_id"];
$stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $created_at);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Mein Profil</title>
    <link rel="stylesheet" href="../css/style_auth.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include '../components/header.php'; ?>

<div class="profile-container">
    <div class="profile-avatar">
        <?php echo strtoupper(substr($username, 0, 1)); ?>
    </div>
    <h2>Willkommen, <?php echo htmlspecialchars($username); ?></h2>
    <div class="profile-info">
        <p><strong>Benutzername:</strong> <?php echo htmlspecialchars($username); ?></p>
        <p><strong>E-Mail:</strong> <?php echo htmlspecialchars($email); ?></p>
        <p><strong>Mitglied seit:</strong> <?php echo date("d.m.Y", strtotime($created_at)); ?></p>
    </div>
    <a href="bookings.php" class="button orders">Meine Bestellungen</a>
    <a href="logout.php" class="button logout">Abmelden</a>
</div>

<?php include '../components/footer.php'; ?>

</body>
</html>
