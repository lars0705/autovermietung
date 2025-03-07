<?php
session_start();
if (!isset($_SESSION["user_id"]) && !isset($_COOKIE["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../components/db_connect.php"; 

$user_id = $_SESSION["user_id"] ?? $_COOKIE["user_id"];

// 1️⃣ Nutzerdaten abrufen
$stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $created_at);
$stmt->fetch();
$stmt->close();

// 2️⃣ Loyalty-Punkte abrufen
$stmt = $conn->prepare("SELECT points FROM loyalty_program WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($points);
$stmt->fetch();
$stmt->close();

$points = $points ?? 0;

// 3️⃣ Feedbacks mit Sternebewertung abrufen
$stmt = $conn->prepare("SELECT feedback_id, feedback_text, rating, created_at FROM feedback WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$feedbacks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Sigmacars | Mein Profil</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/style_profile.css">
</head>
<body>

<?php include '../components/header.php'; ?>

<div class="main_content">
    <!-- Linke Seite: Profildaten -->
    <div class="profile-container">
        <div class="profile-avatar">
            <?php echo strtoupper(substr($username, 0, 1)); ?>
        </div>
        <h2>Willkommen, <?php echo htmlspecialchars($username); ?></h2>
        <div class="profile-info">
            <p><strong>Benutzername:</strong> <?php echo htmlspecialchars($username); ?></p>
            <p><strong>E-Mail:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Mitglied seit:</strong> <?php echo date("d.m.Y", strtotime($created_at)); ?></p>
            <p><strong>Loyalty-Punkte:</strong> <?php echo $points; ?> ⭐</p>
        </div>
        <a href="bookings.php" class="button orders">Meine Bestellungen</a>
        <a href="logout.php" class="button logout">Abmelden</a>
    </div>

    <!-- Rechte Seite: Feedbacks -->
    <div class="feedback-container">
        <h3>Meine Feedbacks</h3>
        <div class="feedback-list">
            <?php if (!empty($feedbacks)): ?>
                <?php foreach ($feedbacks as $feedback): ?>
                    <div class="feedback-item">
                        <div class="feedback-text">
                            <p><?php echo htmlspecialchars($feedback["feedback_text"]); ?></p>
                            <small>Abgegeben am: <?php echo date("d.m.Y H:i", strtotime($feedback["created_at"])); ?></small>
                            <div class="feedback-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?php echo ($i <= $feedback["rating"]) ? 'filled' : ''; ?>">★</span>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <button class="delete-feedback" onclick="deleteFeedback(<?php echo $feedback['feedback_id']; ?>)">Löschen</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Du hast noch kein Feedback abgegeben.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function deleteFeedback(feedbackId) {
    if (confirm("Möchtest du dieses Feedback wirklich löschen?")) {
        fetch("delete_feedback.php?id=" + feedbackId, { method: "GET" })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                location.reload(); // Seite neuladen nach erfolgreichem Löschen
            } else {
                alert("Fehler beim Löschen des Feedbacks.");
            }
        });
    }
}
</script>

<?php include '../components/footer.php'; ?>

</body>
</html>
