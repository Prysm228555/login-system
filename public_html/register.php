<?php
session_start();
require "db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name              = trim($_POST["name"] ?? "");
    $mail              = trim($_POST["mail"] ?? "");
    $password          = $_POST["password"] ?? "";
    $password_confirm  = $_POST["password_confirm"] ?? "";

    if ($name === "" || $mail === "" || $password === "" || $password_confirm === "") {
        $error = "All fields are required.";
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (strlen($password) < 6) {
        $error = "The password must contain at least 6 characters..";
    } elseif ($password !== $password_confirm) {
        $error = "Passwords do not match.";
    } else {

        $stmt = $pdo->prepare("SELECT id FROM users WHERE mail = ?");
        $stmt->execute([$mail]);

        if ($stmt->fetch()) {
            $error = "An account already exists with this email address..";
        } else {

            // Hachage du mot de passe
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Insertion
            $stmt = $pdo->prepare(
                "INSERT INTO users (name, mail, password, desactivated)
                 VALUES (?, ?, ?, 0)"
            );

            $stmt->execute([$name, $mail, $hash]);

            $success = "Account successfully created. You can log in now.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" href="./assets/icon.png" />
</head>
<body>
    <form method="post">
        <img src="./assets/icon.png" class="logo">
        <h1>Register</h1>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <label for="name">Name</label>
        <input type="text" name="name" placeholder="Name" required>
        <label for="email">Email address</label>
        <input type="email" name="mail" placeholder="Email address" required>
        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Password" required>
        <label for="password_confirm">Confirm password</label>
        <input type="password" name="password_confirm" placeholder="Confirm password" required>

        <label for="remember">
            <input type="checkbox" name="remember" style="width: min-content;">
            Remember me
        </label>

        <div class="buttons">
            <button type="button" class="register" id="btnRegister">Log in</button>
            <button type="submit" class="submit">Register</button>
        </div>
    </form>

    <script>
        document.getElementById('btnRegister').addEventListener('click', function () {
            window.location.href = './';
        });
    </script>
</body>
</html>
