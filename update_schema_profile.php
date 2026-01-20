<?php
require_once 'db.php';

try {
    echo "Updating schema for Profile info...\n";
    $pdo->exec("USE verduleria");

    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL AFTER email");
        echo "Added 'phone' column.\n";
    } catch (PDOException $e) {
    }

    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN address TEXT NULL AFTER phone");
        echo "Added 'address' column.\n";
    } catch (PDOException $e) {
    }

    echo "Schema updated successfully for Profile info.\n";

} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}
?>