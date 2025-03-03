<?php
session_start(); // Session starten
require_once "../components/db_connect.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string(trim($_POST["email"]));
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $username, $hashed_password);
        $stmt->fetch();
        
        if (password_verify($password, $hashed_password)) {
            session_regenerate_id(true); // Neue Session-ID erzeugen (Sicherheitsmaßnahme)
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $username;

            // Dauerhafte Anmeldung mit Cookies
            if (isset($_POST["remember"])) {
                setcookie("user_id", $user_id, time() + (86400 * 30), "/");
                setcookie("username", $username, time() + (86400 * 30), "/");
            }

            header("Location: profile.php");
            exit();
        } else {
            $error = "Falsches Passwort.";
        }
    } else {
        $error = "Kein Benutzer mit dieser E-Mail gefunden.";
    }
    $stmt->close();
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Anmelden</title>
    <link rel="stylesheet" href="../css/style_auth.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include '../components/header.php'; ?>

<div class="form-container">
    <h2>Anmelden</h2>
    <?php if (isset($_GET['registered'])) echo "<p class='success'>Registrierung erfolgreich! Bitte anmelden.</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form action="login.php" method="POST">
        <input type="email" name="email" placeholder="E-Mail" required>
        <input type="password" name="password" placeholder="Passwort" required>
        <label><input type="checkbox" name="remember"> Angemeldet bleiben</label>
        <button type="submit">Login</button>
    </form>
    <p>Kein Konto? <a href="register.php">Registrieren</a></p>
</div>

<?php include '../components/footer.php'; ?>

</body>
</html>
