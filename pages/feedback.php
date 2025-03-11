<?php
session_start();
require_once "../components/db_connect.php";

// check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"] ?? "Unbekannt";

// handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rating = isset($_POST["rating"]) ? intval($_POST["rating"]) : 0;
    $feedback_text = trim($_POST["feedback_text"]);

    // validate input, rating must be between 1 and 5, text cannot be empty
    if ($rating < 1 || $rating > 5 || empty($feedback_text)) {
        $error = "Bitte geben Sie eine Bewertung von 1 bis 5 Sternen und einen Text ein.";
    } else {

        // insert feedback into database
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, feedback_text, rating) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $user_id, $feedback_text, $rating);
        if ($stmt->execute()) {
            header("Location: bookings.php?feedback_success=true"); // redirect after successful submission
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

    <!-- show error message if validation fails -->
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- feedback form -->
    <form method="POST">
        <label>Bewertung (1-5 Sterne):</label>
        <select name="rating" required>
            <option disabled selected hidden>Bitte auswählen</option>
            <?php
            // generate star rating options dynamically
            for ($i = 1; $i <= 5; $i++) {
                echo "<option value='$i'>$i ⭐</option>";
            }
            ?>
        </select>

        <label>Ihr Feedback (max. 500 Zeichen):</label>
        <textarea name="feedback_text" maxlength="500" required></textarea>

        <button type="submit" class="fb_submit_button">Feedback absenden</button>
    </form>
</div>

<?php include '../components/footer.php'; ?>

</body>
</html>
