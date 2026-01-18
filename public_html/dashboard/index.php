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
        // ignore silently
    }
}

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../src/style.css">
    <link rel="icon" href="../assets/icon.png" />
</head>
<body>
    <h1>Hello <?= htmlspecialchars($_SESSION["user_name"]) ?></h1>

    <form action="./src/logout.php" method="post">
        <button type="submit">Log out</button>
    </form>
</body>
</html>
