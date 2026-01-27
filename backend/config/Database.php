<?php

namespace App\Config;

use PDO;
use PDOException;

/**
 * Database Connection Handler
 */
class Database
{
    private static ?PDO $connection = null;
    private static array $config;

    /**
     * Get database connection instance (Singleton)
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            self::$config = require __DIR__ . '/config.php';
            $dbConfig = self::$config['database'];

            try {
                $dsn = sprintf(
                    "mysql:host=%s;dbname=%s;charset=%s",
                    $dbConfig['host'],
                    $dbConfig['name'],
                    $dbConfig['charset']
                );

                self::$connection = new PDO(
                    $dsn,
                    $dbConfig['username'],
                    $dbConfig['password'],
                    $dbConfig['options']
                );
            } catch (PDOException $e) {
                error_log("Database Connection Error: " . $e->getMessage());
                throw new PDOException("Could not connect to database");
            }
        }

        return self::$connection;
    }

    /**
     * Begin transaction
     */
    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    /**
     * Rollback transaction
     */
    public static function rollback(): bool
    {
        return self::getConnection()->rollBack();
    }
}
