<?php
$servername = "localhost";
$username = "postgres";
$password = "Salah.963";
$dbname = "restaurant_management";
$port = "5432";

try {
    // إنشاء اتصال PDO
    $pdo = new PDO("pgsql:host=$servername;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>