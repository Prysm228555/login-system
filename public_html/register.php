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
        $error = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($password !== $password_confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {

        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE mail = ?");
        $stmt->execute([$mail]);

        if ($stmt->fetch()) {
            $error = "Un compte existe déjà avec cet email.";
        } else {

            // Hachage du mot de passe
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Insertion
            $stmt = $pdo->prepare(
                "INSERT INTO users (name, mail, password, desactivated)
                 VALUES (?, ?, ?, 0)"
            );

            $stmt->execute([$name, $mail, $hash]);

            $success = "Compte créé avec succès. Vous pouvez vous connecter.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" href="./assets/icon.png" />
</head>
<body>
    <form method="post">
        <img src="./assets/icon.png" class="logo">
        <h1>Inscription</h1>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <label for="name">Nom</label>
        <input type="text" name="name" placeholder="Nom" required>
        <label for="email">Email</label>
        <input type="email" name="mail" placeholder="Email" required>
        <label for="password">Mot de passe</label>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <label for="password_confirm">Confirmer le mot de passe</label>
        <input type="password" name="password_confirm" placeholder="Confirmer le mot de passe" required>

        <label for="remember">
            <input type="checkbox" name="remember" style="width: min-content;">
            Se souvenir de moi
        </label>

        <div class="buttons">
            <button type="button" class="register" id="btnRegister">J'ai un compte</button>
            <button type="submit" class="submit">S'inscrire</button>
        </div>
    </form>

    <script>
        document.getElementById('btnRegister').addEventListener('click', function () {
            window.location.href = './';
        });
    </script>
</body>
</html>
