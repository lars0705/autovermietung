<?php
session_start();
require_once "../components/db_connect.php";

// Falls der Benutzer bereits eingeloggt ist, direkt zur Profilseite weiterleiten
if (isset($_SESSION["user_id"]) || isset($_COOKIE["user_id"])) {
    header("Location: profile.php");
    exit();
}

$return_url = isset($_GET['type_id']) ? "type_id=" . $_GET['type_id'] . "&pickup_date=" . $_GET['pickup_date'] . "&return_date=" . $_GET['return_date'] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string(trim($_POST["username"]));
    $email = $conn->real_escape_string(trim($_POST["email"]));
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $redirect_url = "login.php?registered=true";
            if (!empty($return_url)) {
                $redirect_url .= "&" . $return_url;
            }
            header("Location: " . $redirect_url);
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
    <title>Sigmacars | Registrierung</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include '../components/header.php'; ?>

<div class="main_content">
    <div class="form-container">
        <h2>Registrieren</h2>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form action="register.php<?php echo $return_url ? '?' . $return_url : ''; ?>" method="POST">
            <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($return_url); ?>">

            <input type="text" name="username" placeholder="Benutzername" required>
            <input type="email" name="email" placeholder="E-Mail" required>
            <input type="password" name="password" placeholder="Passwort" required>
            
            <button type="submit">Registrieren</button>
        </form>

        <p>Schon ein Konto? <a href="login.php<?php echo $return_url ? '?' . $return_url : ''; ?>">Anmelden</a></p>
    </div>
</div>

<?php include '../components/footer.php'; ?>

</body>
</html>
