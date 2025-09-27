<?php
// PDO kapcsolat – biztonságos és újrafelhasználható
$host = "localhost";
$dbname = "gyulek";   // adatbázis neve
$username = "root";   // állítsd be a sajátod
$password = "sgtnHS#dW76";       // állítsd be a sajátod

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    die("Adatbázis kapcsolat sikertelen: " . $e->getMessage());
}
?>
