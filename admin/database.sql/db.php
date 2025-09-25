<?php
// $dsn = "mysql:host=localhost;dbname=school_cinema;charset=utf8mb4";
// $username = "root";
// $password = "root";
$dsn = "mysql:host=localhost;dbname=annexbios_hoofdkantoor;charset=utf8mb4";
$username = "annexbios_hoofdkantoor";
$password = "YVbGZmm7ZMG7bqKke4QB";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// $servername = "localhost";
// $username = "root";
// $password = "root";
// $dbname = "school_cinema";
$servername = "localhost";
$username = "annexbios_hoofdkantoor";
$password = "YVbGZmm7ZMG7bqKke4QB";
$dbname = "annexbios_hoofdkantoor";

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}