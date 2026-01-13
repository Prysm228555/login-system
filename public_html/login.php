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
        header("Location: ./");
        exit;
    } else {
        if ($user["desactivated"]){
            $error = "Ce compte est désactivé";
        } else {
            $error = "Email ou mot de passe incorrect";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" href="./assets/icon.png" />
</head>
<body>

<form method="post" class="login-form">
    <img src="./assets/icon.png" class="logo">
    <h1>Connexion</h1>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <label for="mail">Adresse mail</label>
    <input type="email" name="mail" placeholder="Email" required>

    <label for="password">Mot de passe</label>
    <input type="password" name="password" placeholder="Mot de passe" required>

    <label for="remember">
        <input type="checkbox" name="remember" style="width: min-content;">
        Se souvenir de moi
    </label>

    <div class="buttons">
        <button type="button" class="register" id="btnRegister">Créer un compte</button>
        <button type="submit" class="submit">Se connecter</button>
    </div>
</form>

<script>
  document.getElementById('btnRegister').addEventListener('click', function () {
    window.location.href = './register.php';
  });
</script>

</body>
</html>
