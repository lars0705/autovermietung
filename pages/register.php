<?php
session_start();
require_once "../components/db_connect.php"; // Separater DB-Connect

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string(trim($_POST["username"]));
    $email = $conn->real_escape_string(trim($_POST["email"]));
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Korrekte Überprüfung, ob E-Mail existiert
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        // Benutzer erstellen
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            header("Location: login.php?registered=true");
            exit();
        } else {
            $error = "Registrierung fehlgeschlagen.";
        }
    } else {
        $error = "Ein Benutzer mit dieser E-Mail existiert bereits.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Registrieren</title>
    <link rel="stylesheet" href="../css/style_auth.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include '../components/header.php'; ?>

<div class="form-container">
    <h2>Registrieren</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form action="register.php" method="POST">
        <input type="text" name="username" placeholder="Benutzername" required>
        <input type="email" name="email" placeholder="E-Mail" required>
        <input type="password" name="password" placeholder="Passwort" required>
        <button type="submit">Registrieren</button>
    </form>
    <p>Schon ein Konto? <a href="login.php">Anmelden</a></p>
</div>

<?php include '../components/footer.php'; ?>

</body>
</html>
