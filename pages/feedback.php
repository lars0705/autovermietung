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
<style>
/* 🌌 Hintergrund und Layout */
body {
    margin: 0;
    padding: 0;
    background-image: url("../assets/images/hintergrund.jpg");
    background-size: cover;
    background-position: center;
    color: white;
    font-family: "Inter", sans-serif;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* 🚀 Feedback-Container */
.feedback-container {
    max-width: 500px;
    margin: auto;
    margin-top: 100px;
    padding: 25px;
    background: rgba(255, 255, 255, 0.15); /* Transparenter Effekt */
    border-radius: 15px;
    backdrop-filter: blur(10px); /* Glas-Effekt */
    box-shadow: 0px 10px 20px rgba(255, 255, 255, 0.2);
    text-align: center;
    animation: fadeIn 1s ease-in-out;
}

/* ✨ Überschrift */
.feedback-container h2 {
    font-size: 26px;
    font-weight: bold;
    margin-bottom: 15px;
}

/* 📝 Eingabefelder */
textarea {
    width: 100%;
    height: 120px;
    resize: none;
    padding: 10px;
    border-radius: 8px;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    font-size: 16px;
    outline: none;
}

/* 🟡 Sterne-Bewertung */
.star-rating {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-bottom: 15px;
}

.star {
    font-size: 30px;
    cursor: pointer;
    transition: transform 0.3s ease-in-out, color 0.3s;
    color: rgba(255, 255, 255, 0.5);
}

.star:hover,
.star.active {
    color: #FFD700;
    transform: scale(1.2);
}

/* ✅ Absenden-Button */
.submit_button {
    display: block;
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: black;
    border: none;
    border-radius: 8px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
    transition: transform 0.3s, box-shadow 0.3s;
}

.submit_button:hover {
    transform: scale(1.05);
    box-shadow: 0px 10px 20px rgba(255, 215, 0, 0.5);
}

/* ⚠️ Fehleranzeige */
.error {
    color: #ff4d4d;
    font-weight: bold;
    margin-bottom: 10px;
}

/* 📌 Footer fixieren */
footer {
    margin-top: auto;
    background: #111;
    padding: 20px 0;
    text-align: center;
}

/* 🌟 Animation für sanftes Einblenden */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

</style>
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

        <button type="submit" class="submit_button">Feedback absenden</button>
    </form>
</div>

<?php include '../components/footer.php'; ?>

</body>
</html>
