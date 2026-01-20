<?php
require_once 'db.php';

try {
    echo "Updating schema...\n";
    $pdo->exec("USE verduleria");
    $pdo->exec("ALTER TABLE products MODIFY image_url TEXT");
    echo "Schema updated successfully. 'image_url' is now TEXT.\n";
} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}
?>