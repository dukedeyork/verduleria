<?php
require 'db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Modifying 'orders' table status column...\n";

    // Modify the column to include 'processing'
    // Note: We also include the existing values to preserve them.
    $sql = "ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending'";

    $pdo->exec($sql);

    echo "Schema updated successfully!\n";

} catch (PDOException $e) {
    die("Error updating schema: " . $e->getMessage() . "\n");
}
?>