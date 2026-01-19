<?php
session_start();

if (!isset($_SESSION["user_id"]) && isset($_COOKIE["remember_me"])) {

    try {
        $token_hash = hash("sha256", $_COOKIE["remember_me"]);

        $stmt = $pdo->prepare(
            "SELECT id, name, role FROM users WHERE remember_token = ? AND desactivated = 0"
        );
        $stmt->execute([$token_hash]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_role"] = $user["role"];
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
    <title><?= htmlspecialchars($_SESSION["user_name"]) ?>'s account</title>
    <link rel="stylesheet" href="../src/css/main.css">
    <link rel="icon" href="../assets/icon.png" />
</head>
<body>
    <nav>
        <h1><?= htmlspecialchars($_SESSION["user_name"]) ?>'s account</h1>
        <div class="buttons">
            <button onclick="window.location.href='../'"><img src="../assets/home.png" alt="Home" title="Home"></button>
            <?php if ($_SESSION["user_role"] >= 2){ ?>
                <button onclick="window.location.href='../dashboard'"><img src="../assets/dash.png" alt="Dashboard" title="Dashboard"></button>
            <?} ?>
            <button onclick="window.location.href='../me'"><img src="../assets/account.png" alt="My Account" title="My Account"></button>
            <form action="../src/logout.php" method="post"><button type="submit" class="logout"><img src="../assets/logout.png" alt="logout" title="Logout"></button></form>
        </div>
    </nav>
</body>
</html>
