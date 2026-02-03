-- Add Categories Table and Update Products Table
-- This migration adds a categories table and updates products to use category_id

-- Create categories table
CREATE TABLE
IF NOT EXISTS categories
(
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    name VARCHAR
(100) NOT NULL,
    slug VARCHAR
(100) NOT NULL,
    description TEXT,
    icon VARCHAR
(100),
    color VARCHAR
(7) DEFAULT '#064E3B',
    parent_id INT NULL,
    display_order INT DEFAULT 0,
    status ENUM
('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON
UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY
(store_id) REFERENCES stores
(id) ON
DELETE CASCADE,
    FOREIGN KEY (parent_id)
REFERENCES categories
(id) ON
DELETE
SET NULL
,
    UNIQUE KEY unique_store_slug
(store_id, slug)
);

-- Add indexes for better query performance
CREATE INDEX
IF NOT EXISTS idx_categories_store_id ON categories
(store_id);
CREATE INDEX
IF NOT EXISTS idx_categories_parent_id ON categories
(parent_id);
CREATE INDEX
IF NOT EXISTS idx_categories_status ON categories
(status);

-- Add category_id column to products table
ALTER TABLE products 
ADD COLUMN
IF NOT EXISTS category_id INT NULL AFTER store_id;

-- Add foreign key constraint (skip if already exists)
SET @constraint_exists = (SELECT COUNT(*)
FROM information_schema.TABLE_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = DATABASE
() 
    AND TABLE_NAME = 'products' 
    AND CONSTRAINT_NAME = 'fk_products_category');

SET @sql =
IF(@constraint_exists = 0, 
    'ALTER TABLE products ADD CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL',
    'SELECT "Constraint fk_products_category already exists" AS message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index for category_id in products
CREATE INDEX
IF NOT EXISTS idx_products_category_id ON products
(category_id);

-- Migrate existing category data (if any exists as string)
-- This will create categories from existing product.category values
INSERT INTO categories
    (store_id, name, slug, status)
SELECT DISTINCT
    p.store_id,
    p.category as name,
    LOWER(REPLACE(REPLACE(p.category, ' ', '-'), '&', 'and')) as slug,
    'active' as status
FROM products p
WHERE p.category IS NOT NULL AND p.category != ''
ON DUPLICATE KEY
UPDATE
    name = VALUES
(name),
    updated_at = CURRENT_TIMESTAMP;

-- Update products to reference the new category_id
UPDATE products p
INNER JOIN categories c
ON p.store_id = c.store_id 
    AND LOWER
(REPLACE
(REPLACE
(p.category, ' ', '-'), '&', 'and')) = c.slug
SET p
.category_id = c.id
WHERE p.category IS NOT NULL AND p.category != '';

-- Optional: Remove the old category column after migration is complete
-- Uncomment the line below once you've verified the migration worked
-- ALTER TABLE products DROP COLUMN category;
