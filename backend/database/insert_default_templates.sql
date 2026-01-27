-- Insert default store templates
INSERT INTO store_templates (name, description, preview_image) VALUES 
('CampMart Style', 'Modern marketplace design inspired by campus commerce', '/assets/templates/campmart-preview.jpg'),
('Minimal Clean', 'Clean and minimalist template with focus on products', '/assets/templates/minimal-preview.jpg'),
('Bold Modern', 'Bold and vibrant design for modern brands', '/assets/templates/bold-preview.jpg'),
('Classic Ecommerce', 'Traditional ecommerce layout with proven conversions', '/assets/templates/classic-preview.jpg'),
('Premium Luxury', 'Elegant template for high-end products', '/assets/templates/luxury-preview.jpg')
ON DUPLICATE KEY UPDATE 
    description = VALUES(description),
    preview_image = VALUES(preview_image);
