<?php

require_once __DIR__ . '/../bootstrap.php';

use App\Config\Database;

try {
    $database = new Database();
    $pdo = $database->getConnection();

    echo "Adding payment method columns to stores table...\n\n";

    // Add bank transfer columns
    $sql1 = "ALTER TABLE stores ADD COLUMN bank_transfer_enabled TINYINT(1) DEFAULT 0";
    echo "Executing: $sql1\n";
    $pdo->exec($sql1);
    echo "✓ Added bank_transfer_enabled\n\n";

    $sql2 = "ALTER TABLE stores ADD COLUMN bank_name VARCHAR(100) DEFAULT NULL";
    echo "Executing: $sql2\n";
    $pdo->exec($sql2);
    echo "✓ Added bank_name\n\n";

    $sql3 = "ALTER TABLE stores ADD COLUMN account_number VARCHAR(50) DEFAULT NULL";
    echo "Executing: $sql3\n";
    $pdo->exec($sql3);
    echo "✓ Added account_number\n\n";

    $sql4 = "ALTER TABLE stores ADD COLUMN account_name VARCHAR(100) DEFAULT NULL";
    echo "Executing: $sql4\n";
    $pdo->exec($sql4);
    echo "✓ Added account_name\n\n";

    // Add COD column
    $sql5 = "ALTER TABLE stores ADD COLUMN cod_enabled TINYINT(1) DEFAULT 1";
    echo "Executing: $sql5\n";
    $pdo->exec($sql5);
    echo "✓ Added cod_enabled\n\n";

    echo "✅ All payment method columns added successfully!\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
