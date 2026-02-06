<?php

/**
 * Run Password Resets Table Migration
 * Execute this file to create the password_resets table
 */

require_once __DIR__ . '/../config/Database.php';

use App\Config\Database;

try {
    $db = Database::getConnection();
    
    echo "Creating password_resets table...\n";
    
    $sql = "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        user_type ENUM('client', 'admin') NOT NULL DEFAULT 'client',
        token VARCHAR(255) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_token (token),
        INDEX idx_user (user_id, user_type),
        INDEX idx_expires (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $db->exec($sql);
    
    echo "✓ password_resets table created successfully!\n";
    echo "\nTable structure:\n";
    echo "  - id (INT, PRIMARY KEY)\n";
    echo "  - user_id (INT)\n";
    echo "  - user_type (ENUM: 'client', 'admin')\n";
    echo "  - token (VARCHAR 255, hashed)\n";
    echo "  - expires_at (DATETIME)\n";
    echo "  - created_at (TIMESTAMP)\n";
    echo "\nIndexes:\n";
    echo "  - idx_token on token\n";
    echo "  - idx_user on user_id, user_type\n";
    echo "  - idx_expires on expires_at\n";
    echo "\nMigration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "✗ Error creating table: " . $e->getMessage() . "\n";
    exit(1);
}
