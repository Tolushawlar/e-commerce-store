-- Enhanced Store Customization Schema Update

USE ecommerce_platform;

-- Add new customization columns to stores table
ALTER TABLE stores 
ADD COLUMN tagline VARCHAR(255) DEFAULT NULL,
ADD COLUMN hero_background_url VARCHAR(255) DEFAULT NULL,
ADD COLUMN header_style ENUM('default', 'centered', 'minimal') DEFAULT 'default',
ADD COLUMN product_grid_columns INT DEFAULT 4,
ADD COLUMN font_family VARCHAR(50) DEFAULT 'Plus Jakarta Sans',
ADD COLUMN button_style ENUM('rounded', 'square', 'pill') DEFAULT 'rounded',
ADD COLUMN show_search BOOLEAN DEFAULT TRUE,
ADD COLUMN show_cart BOOLEAN DEFAULT TRUE,
ADD COLUMN show_wishlist BOOLEAN DEFAULT FALSE,
ADD COLUMN footer_text TEXT DEFAULT NULL,
ADD COLUMN social_facebook VARCHAR(255) DEFAULT NULL,
ADD COLUMN social_instagram VARCHAR(255) DEFAULT NULL,
ADD COLUMN social_twitter VARCHAR(255) DEFAULT NULL,
ADD COLUMN custom_css LONGTEXT DEFAULT NULL,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Create store_sections table for customizable page sections
CREATE TABLE store_sections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    section_type ENUM('hero', 'featured_products', 'categories', 'testimonials', 'newsletter', 'custom') NOT NULL,
    title VARCHAR(255) DEFAULT NULL,
    content TEXT DEFAULT NULL,
    background_color VARCHAR(7) DEFAULT NULL,
    text_color VARCHAR(7) DEFAULT NULL,
    is_visible BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Create store_navigation table for custom menu items
CREATE TABLE store_navigation (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    label VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    target ENUM('_self', '_blank') DEFAULT '_self',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Insert default sections for existing stores
INSERT INTO store_sections (store_id, section_type, title, content, is_visible, sort_order)
SELECT id, 'hero', 'Welcome to Our Store', 'Discover amazing products at great prices', TRUE, 1
FROM stores;

INSERT INTO store_sections (store_id, section_type, title, is_visible, sort_order)
SELECT id, 'featured_products', 'Featured Products', TRUE, 2
FROM stores;