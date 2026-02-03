<?php

/**
 * Run Paystack payment integration migration
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Config\Database;

try {
    $database = new Database();
    $db = $database->getConnection();

    $sql = file_get_contents(__DIR__ . '/add_paystack_integration.sql');

    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    echo "Running Paystack integration migration...\n\n";

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            echo "Executing: " . substr($statement, 0, 60) . "...\n";
            $db->exec($statement);
        }
    }

    echo "\n✅ Migration completed successfully!\n";
    echo "Paystack payment fields added to stores and orders tables.\n";
    echo "\nNext steps:\n";
    echo "1. Add Paystack public and secret keys to your store settings\n";
    echo "2. Enable payment processing for the store\n";
    echo "3. Test payment flow with Paystack test keys\n";
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
