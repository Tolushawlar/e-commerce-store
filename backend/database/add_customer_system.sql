-- ============================================================================
-- Customer System Migration
-- Adds store-specific customer accounts with guest checkout support
-- Version: 1.0
-- Date: 2026-02-02
-- ============================================================================

USE ecommerce_platform;

-- ============================================================================
-- 1. STORE CUSTOMERS TABLE
-- Store-specific customer accounts (supports both registered and guest)
-- ============================================================================
CREATE TABLE
IF NOT EXISTS store_customers
(
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    email VARCHAR
(100) NOT NULL,
    password_hash VARCHAR
(255) NULL COMMENT 'NULL for guest customers',
    first_name VARCHAR
(50),
    last_name VARCHAR
(50),
    phone VARCHAR
(20),
    is_guest BOOLEAN DEFAULT FALSE COMMENT 'TRUE for guest checkout, FALSE for registered',
    email_verified BOOLEAN DEFAULT FALSE,
    status ENUM
('active', 'inactive', 'blocked') DEFAULT 'active',
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON
UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY
(store_id) REFERENCES stores
(id) ON
DELETE CASCADE,
    UNIQUE KEY unique_store_email (store_id, email
),
    INDEX idx_email
(email),
    INDEX idx_store_id
(store_id),
    INDEX idx_is_guest
(is_guest)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Store-specific customer accounts';

-- ============================================================================
-- 2. CUSTOMER ADDRESSES TABLE
-- Shipping and billing addresses for customers
-- ============================================================================
CREATE TABLE
IF NOT EXISTS customer_addresses
(
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    address_type ENUM
('shipping', 'billing', 'both') DEFAULT 'shipping',
    full_name VARCHAR
(100),
    phone VARCHAR
(20),
    address_line1 VARCHAR
(255) NOT NULL,
    address_line2 VARCHAR
(255),
    city VARCHAR
(100) NOT NULL,
    state VARCHAR
(100) NOT NULL,
    postal_code VARCHAR
(20),
    country VARCHAR
(100) DEFAULT 'Nigeria',
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON
UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY
(customer_id) REFERENCES store_customers
(id) ON
DELETE CASCADE,
    INDEX idx_customer_id (customer_id),
    INDEX idx_is_default
(is_default)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Customer shipping and billing addresses';

-- ============================================================================
-- 3. SHOPPING CART TABLE
-- Persistent shopping cart for registered customers
-- ============================================================================
CREATE TABLE
IF NOT EXISTS shopping_carts
(
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON
UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY
(customer_id) REFERENCES store_customers
(id) ON
DELETE CASCADE,
    FOREIGN KEY (product_id)
REFERENCES products
(id) ON
DELETE CASCADE,
    UNIQUE KEY unique_customer_product (customer_id, product_id
),
    INDEX idx_customer_id
(customer_id),
    INDEX idx_product_id
(product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Persistent shopping cart items';

-- ============================================================================
-- 4. UPDATE ORDERS TABLE
-- Link orders to customers and add payment/shipping info
-- ============================================================================

-- Check if columns don't exist before adding them
SET @dbname = DATABASE
();
SET @tablename = 'orders';

-- Add customer_id column
SET @col_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'customer_id');

SET @query =
IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN customer_id INT NULL AFTER store_id',
    'SELECT "customer_id already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add shipping_address_id column
SET @col_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'shipping_address_id');

SET @query =
IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN shipping_address_id INT NULL AFTER customer_id',
    'SELECT "shipping_address_id already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add billing_address_id column
SET @col_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'billing_address_id');

SET @query =
IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN billing_address_id INT NULL AFTER shipping_address_id',
    'SELECT "billing_address_id already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add shipping_cost column
SET @col_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'shipping_cost');

SET @query =
IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN shipping_cost DECIMAL(10,2) DEFAULT 0.00 AFTER total_amount',
    'SELECT "shipping_cost already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add payment_method column
SET @col_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'payment_method');

SET @query =
IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN payment_method ENUM(''cash_on_delivery'', ''bank_transfer'', ''card'', ''wallet'') DEFAULT ''cash_on_delivery'' AFTER shipping_cost',
    'SELECT "payment_method already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add payment_status column
SET @col_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'payment_status');

SET @query =
IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN payment_status ENUM(''pending'', ''paid'', ''failed'', ''refunded'') DEFAULT ''pending'' AFTER payment_method',
    'SELECT "payment_status already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add order_notes column
SET @col_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'order_notes');

SET @query =
IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN order_notes TEXT NULL AFTER payment_status',
    'SELECT "order_notes already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add tracking_number column
SET @col_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'tracking_number');

SET @query =
IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN tracking_number VARCHAR(100) NULL AFTER order_notes',
    'SELECT "tracking_number already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key constraints (check if they don't exist first)
SET @fk_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename
    AND CONSTRAINT_NAME = 'fk_orders_customer');

SET @query =
IF(@fk_exists = 0,
    'ALTER TABLE orders ADD CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES store_customers(id) ON DELETE SET NULL',
    'SELECT "fk_orders_customer already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @fk_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename
    AND CONSTRAINT_NAME = 'fk_orders_shipping_address');

SET @query =
IF(@fk_exists = 0,
    'ALTER TABLE orders ADD CONSTRAINT fk_orders_shipping_address FOREIGN KEY (shipping_address_id) REFERENCES customer_addresses(id) ON DELETE SET NULL',
    'SELECT "fk_orders_shipping_address already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @fk_exists = (SELECT COUNT(*)
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename
    AND CONSTRAINT_NAME = 'fk_orders_billing_address');

SET @query =
IF(@fk_exists = 0,
    'ALTER TABLE orders ADD CONSTRAINT fk_orders_billing_address FOREIGN KEY (billing_address_id) REFERENCES customer_addresses(id) ON DELETE SET NULL',
    'SELECT "fk_orders_billing_address already exists" AS message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- 5. CREATE INDEXES FOR PERFORMANCE
-- ============================================================================
CREATE INDEX
IF NOT EXISTS idx_orders_customer_id ON orders
(customer_id);
CREATE INDEX
IF NOT EXISTS idx_orders_payment_status ON orders
(payment_status);
CREATE INDEX
IF NOT EXISTS idx_orders_tracking_number ON orders
(tracking_number);

-- ============================================================================
-- MIGRATION COMPLETE
-- ============================================================================

SELECT 'Customer system migration completed successfully!' AS status;
SELECT COUNT(*) AS store_customers_count
FROM store_customers;
SELECT COUNT(*) AS customer_addresses_count
FROM customer_addresses;
SELECT COUNT(*) AS shopping_carts_count
FROM shopping_carts;
