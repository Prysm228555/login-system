<?php
$host = "my_mysql";
$dbname = "db";
$user = "root";
$pass = "root123";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Connection error : " . $e->getMessage());
}
?>