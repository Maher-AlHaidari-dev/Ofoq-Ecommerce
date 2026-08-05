<?php
require_once __DIR__ . '/config/db.php';

try {
    $pdo->exec("ALTER TABLE orders ADD COLUMN shipping_address TEXT NULL;");
    echo "Database updated successfully.";
} catch (PDOException $e) {
    echo "Error updating database.";
}