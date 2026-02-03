<?php

/**
 * Run guest shipping fields migration
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Config\Database;

try {
    $database = new Database();
    $db = $database->getConnection();

    $sql = file_get_contents(__DIR__ . '/add_guest_shipping_fields.sql');

    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $db->exec($statement);
        }
    }

    echo "✅ Migration completed successfully!\n";
    echo "Guest shipping address fields added to orders table.\n";
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
