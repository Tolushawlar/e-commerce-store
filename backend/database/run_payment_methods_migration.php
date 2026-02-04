<?php

/**
 * Run additional payment methods migration
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Config\Database;

try {
    $database = new Database();
    $db = $database->getConnection();

    $sql = file_get_contents(__DIR__ . '/add_payment_methods.sql');

    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    echo "Running additional payment methods migration...\n\n";

    foreach ($statements as $statement) {
        if (!empty($statement) && !str_starts_with($statement, '--')) {
            echo "Executing: " . substr($statement, 0, 60) . "...\n";
            $db->exec($statement);
        }
    }

    echo "\n✅ Migration completed successfully!\n";
    echo "Bank transfer and COD payment fields added to stores table.\n";
    echo "\nNext steps:\n";
    echo "1. Configure bank details in store settings for bank transfer\n";
    echo "2. Enable/disable payment methods per store\n";
    echo "3. Test checkout with different payment options\n";
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
