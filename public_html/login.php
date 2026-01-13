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
        // ignore silently
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

            $token = bin2hex(random_bytes(32));
            $token_hash = hash("sha256", $token);

            $stmt = $pdo->prepare(
                "UPDATE users SET remember_token = ? WHERE id = ?"
            );
            $stmt->execute([$token_hash, $user["id"]]);

            setcookie(
                "remember_me",
                $token,
                time() + (30 * 24 * 60 * 60),
                "/",
                "",
                false,
                true
            );
        };
        header("Location: ./");
        exit;
    } else {
        if ($user && $user["desactivated"] == 1){
            $error = "This account was desactivated";
        } else {
            $error = "Incorrect email address / password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Log in</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" href="./assets/icon.png" />
</head>
<body>

<form method="post" class="login-form">
    <img src="./assets/icon.png" class="logo">
    <h1>Log in</h1>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <label for="mail">Email address</label>
    <input type="email" name="mail" placeholder="Email address" required>

    <label for="password">Password</label>
    <input type="password" name="password" placeholder="Password" required>

    <label for="remember">
        <input type="checkbox" name="remember" style="width: min-content;">
        Remember me
    </label>

    <div class="buttons">
        <button type="button" class="register" id="btnRegister">Create an account</button>
        <button type="submit" class="submit">Login</button>
    </div>
</form>

<script>
  document.getElementById('btnRegister').addEventListener('click', function () {
    window.location.href = './register.php';
  });
</script>

</body>
</html>
