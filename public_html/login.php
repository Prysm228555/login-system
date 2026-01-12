<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"]) && isset($_COOKIE["remember_me"])) {

    try {
        $token_hash = hash("sha256", $_COOKIE["remember_me"]);

        $stmt = $pdo->prepare(
            "SELECT id, name FROM users WHERE remember_token = ? AND desactivated = 0"
        );
        $stmt->execute([$token_hash]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
        }

    } catch (Exception $e) {
        // on ignore silencieusement
    }
}


$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mail = trim($_POST["mail"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE mail = ?");
    $stmt->execute([$mail]);
    $user = $stmt->fetch();

    if ($user && !$user["desactivated"] && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["name"];
        if (isset($_POST["remember"])) {

            // Générer un token sécurisé
            $token = bin2hex(random_bytes(32));
            $token_hash = hash("sha256", $token);

            // Stocker le hash en base
            $stmt = $pdo->prepare(
                "UPDATE users SET remember_token = ? WHERE id = ?"
            );
            $stmt->execute([$token_hash, $user["id"]]);

            // Cookie (30 jours)
            setcookie(
                "remember_me",
                $token,
                time() + (30 * 24 * 60 * 60),
                "/",
                "",
                false, // true si HTTPS
                true   // HttpOnly
            );
        };
        header("Location: index.php");
        exit;
    } else {
        $error = "Email ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>

<form method="post" class="login-form">
    <h2>Connexion</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <input type="email" name="mail" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <label>
        <input type="checkbox" name="remember">
        Se souvenir de moi
    </label>

    <button type="submit">Se connecter</button>
</form>

</body>
</html>
