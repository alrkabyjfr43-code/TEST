<?php
// config/db.php

$host = 'localhost';
$dbname = 'company_portal';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . " <br> Please ensure MySQL is running and database 'company_portal' exists.");
}
?>
