<?php
require_once 'db.php';

try {
    echo "Updating schema for DNI support...\n";
    $pdo->exec("USE verduleria");

    // Add DNI column
    // We try/catch this specific one in case it already exists (idempotency)
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN dni VARCHAR(20) NOT NULL UNIQUE AFTER name");
        echo "Added 'dni' column.\n";
    } catch (PDOException $e) {
        echo "Column 'dni' might already exist or error: " . $e->getMessage() . "\n";
    }

    // Make Email optional and remove unique constraint if necessary
    // Note: Dropping index by name 'email' is common if unique constraint was named 'email'
    try {
        // First check if index exists to drop it? MySQL gives error if not exists.
        // We'll try to drop the unique index on email.
        $pdo->exec("ALTER TABLE users DROP INDEX email");
        echo "Dropped UNIQUE constraint on 'email'.\n";
    } catch (PDOException $e) {
        // Index might not exist or have different name
    }

    // Modify ID to allow NULL
    $pdo->exec("ALTER TABLE users MODIFY email VARCHAR(100) NULL");
    echo "Modified 'email' to be nullable.\n";

    // Update existing admin user with a DNI if needed
    // Assuming admin doesn't have DNI yet, let's give him a default one 
    $pdo->exec("UPDATE users SET dni = '00000000' WHERE role = 'admin' AND (dni IS NULL OR dni = '')");

    echo "Schema updated successfully for DNI.\n";

} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}
?>