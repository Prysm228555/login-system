<?php
session_start();

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

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" href="./assets/icon.png" />
</head>
<body>
    <h1>Bonjour <?= htmlspecialchars($_SESSION["user_name"]) ?></h1>

    <form action="logout.php" method="post">
        <button type="submit">Se déconnecter</button>
    </form>
</body>
</html>
