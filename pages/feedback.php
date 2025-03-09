<?php
session_start();
require_once "../components/db_connect.php";

// Prüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"] ?? "Unbekannt";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rating = isset($_POST["rating"]) ? intval($_POST["rating"]) : 0;
    $feedback_text = trim($_POST["feedback_text"]);

    if ($rating < 1 || $rating > 5 || empty($feedback_text)) {
        $error = "Bitte geben Sie eine Bewertung von 1 bis 5 Sternen und einen Text ein.";
    } else {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, feedback_text, rating) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $user_id, $feedback_text, $rating);
        if ($stmt->execute()) {
            header("Location: bookings.php?feedback_success=true");
            exit();
        } else {
            $error = "Fehler beim Absenden des Feedbacks.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Feedback geben</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include '../components/header.php'; ?>

<div class="feedback-container">
    <h2>Feedback geben</h2>
    <p>Sie geben Feedback als: <strong><?php echo htmlspecialchars($username); ?></strong></p>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Bewertung (1-5 Sterne):</label>
        <select name="rating" required>
            <option value="">Sterne auswählen</option>
            <option value="1">⭐</option>
            <option value="2">⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="5">⭐⭐⭐⭐⭐</option>
        </select>

        <label>Ihr Feedback (max. 500 Zeichen):</label>
        <textarea name="feedback_text" maxlength="500" required></textarea>

        <button type="submit" class="fb_submit_button">Feedback absenden</button>
    </form>
</div>

<?php include '../components/footer.php'; ?>

</body>
</html>
