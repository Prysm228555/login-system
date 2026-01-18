<?php
session_start();

if (isset($_SESSION["user_id"])) {

    require "db.php";

    // Delete remember token
    $stmt = $pdo->prepare(
        "UPDATE users SET remember_token = NULL WHERE id = ?"
    );
    $stmt->execute([$_SESSION["user_id"]]);
}

// Delete cookie
setcookie("remember_me", "", time() - 3600, "/");

// Destroy session
session_destroy();

header("Location: ../login");
exit;
