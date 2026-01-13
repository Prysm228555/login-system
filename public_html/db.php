<?php
$host = "my_mysql";
$dbname = "db";
$user = "root";       // adapte si besoin
$pass = "root123";           // adapte si besoin

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