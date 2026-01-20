<?php
$host = 'localhost';
$dbname = 'verduleria';
$username = 'root';
$password = '';

try {
    // First connect without DB to ensure it exists (handled in install.php) or just connect
    // For general usage, we connect to the specific DB
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If the database doesn't exist, we might be running the installer.
    // In a production app you'd handle this better, but for this dev setup:
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        // Allow script to continue if it's the installer that will create it
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}
?>
