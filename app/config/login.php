<?php
// login.php (Docker-ready)
// DB name: Cars_database

$host = 'db';                 // <- en Docker NO es localhost
$db   = 'Cars_database';      // <- tu base real
$user = 'root';
$pass = 'rootpassword';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // Mensaje simple para debug local
    die("DB connection failed: " . $e->getMessage());
}
?>
