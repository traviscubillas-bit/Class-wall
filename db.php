<?php
$host = 'localhost';
$db   = 'classwall';
$user = 'postgres';
$pass = '123';

try {
    $dsn = "pgsql:host=$host;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (\PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>