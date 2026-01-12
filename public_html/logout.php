<?php
session_start();

if (isset($_SESSION["user_id"])) {

    require "db.php";

    // Supprimer le token en base
    $stmt = $pdo->prepare(
        "UPDATE users SET remember_token = NULL WHERE id = ?"
    );
    $stmt->execute([$_SESSION["user_id"]]);
}

// Supprimer cookie
setcookie("remember_me", "", time() - 3600, "/");

// Détruire session
session_destroy();

header("Location: login.php");
exit;
