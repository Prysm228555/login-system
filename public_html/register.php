<?php
session_start();
require "db.php";

$error = "";

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

        $stmt = $pdo->prepare("SELECT desactivated FROM users WHERE mail = ?");
        $stmt->execute([$mail]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && $result['desactivated'] == 3) {
            $error = "This email adress is banned";
        } else{
            if ($result && $result["desactivated"] == 2){
                $stmt = $pdo->prepare("DELETE FROM users WHERE mail = ?");
                $stmt->execute([$mail]);
                $result = false;
            }
            if (!$result) {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare(
                    "INSERT INTO users (name, mail, password, desactivated)
                     VALUES (?, ?, ?, 0)"
                );
                $stmt->execute([$name, $mail, $hash]);

                $stmt = $pdo->prepare("SELECT * FROM users WHERE mail = ?");
                $stmt->execute([$mail]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user["password"])) {
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
                }
            } else {
                $error = "An account already exists with this email address...";
            }
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
    <img src="./assets/icon.png" class="logo">
    <form method="post">
        <h1>Register</h1>

        <span class="error" id="error"><?= htmlspecialchars($error) ?></span>

        <label for="name">Name</label>
        <input type="text" name="name" placeholder="Name" required>
        <label for="email">Email address</label>
        <input type="email" name="mail" placeholder="Email address" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <label for="password_confirm">Confirm password</label>
        <input type="password" id="password_confirm" name="password_confirm" placeholder="Confirm password" required>

        <label for="remember" class="remember">
            <input type="checkbox" name="remember" class="remember">
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

        document.addEventListener("DOMContentLoaded", () => {
            const password = document.getElementById("password");
            const confirm = document.getElementById("password_confirm");
            const message = document.getElementById("error");

            confirm.addEventListener("input", () => {
                if (password.value === confirm.value) {
                    message.textContent = "";
                } else {
                    message.textContent = "Passwords do not match";
                }
            });
        });
    </script>
</body>
</html>
