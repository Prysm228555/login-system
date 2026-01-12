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
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
        }
        form {
            width: 320px;
            margin: 80px auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
        }
        .error {
            color: red;
            text-align: center;
        }
        .success {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>

<form method="post">
    <h2>Inscription</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <input type="text" name="name" placeholder="Nom" required>
    <input type="email" name="mail" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <input type="password" name="password_confirm" placeholder="Confirmer le mot de passe" required>

    <button type="submit">Créer le compte</button>

    <p style="text-align:center;">
        <a href="login.php">Déjà un compte ? Connexion</a>
    </p>
</form>

</body>
</html>
