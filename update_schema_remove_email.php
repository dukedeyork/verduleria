<?php
require_once 'db.php';

try {
    echo "Updating schema to remove Email...\n";
    $pdo->exec("USE verduleria");

    try {
        $pdo->exec("ALTER TABLE users DROP COLUMN email");
        echo "Dropped 'email' column.\n";
    } catch (PDOException $e) {
        echo "Error dropping email (maybe already gone): " . $e->getMessage() . "\n";
    }

    echo "Schema updated successfully. Email removed.\n";

} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}
?>