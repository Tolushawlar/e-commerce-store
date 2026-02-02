-- Add guest shipping address fields to orders table for guest checkout
-- Run this migration to add inline shipping address fields

SET @dbname = DATABASE
();
SET @tablename = 'orders';

-- Add shipping_address column
SET @col_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'shipping_address');

SET @query =
IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN shipping_address VARCHAR(255) NULL AFTER customer_phone',
    'SELECT "shipping_address already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add shipping_city column
SET @col_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'shipping_city');

SET @query =
IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN shipping_city VARCHAR(100) NULL AFTER shipping_address',
    'SELECT "shipping_city already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add shipping_state column
SET @col_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'shipping_state');

SET @query =
IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN shipping_state VARCHAR(100) NULL AFTER shipping_city',
    'SELECT "shipping_state already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add shipping_postal_code column
SET @col_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'shipping_postal_code');

SET @query =
IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN shipping_postal_code VARCHAR(20) NULL AFTER shipping_state',
    'SELECT "shipping_postal_code already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add shipping_country column
SET @col_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'shipping_country');

SET @query =
IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN shipping_country VARCHAR(100) DEFAULT "Nigeria" AFTER shipping_postal_code',
    'SELECT "shipping_country already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Guest shipping address fields migration completed' AS status;
