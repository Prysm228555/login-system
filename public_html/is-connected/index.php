<?php
// Test de connexion MySQL
$conn = new mysqli('mysql', 'user', 'password', 'db');

if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

echo "<h1>✅ Connexion réussie!</h1>";
echo "<p>MySQL: " . $conn->server_info . "</p>";
echo "<p>PHP: " . phpversion() . "</p>";

$conn->close();
?>