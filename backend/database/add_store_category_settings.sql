-- Add category display settings to stores table
ALTER TABLE stores 
ADD COLUMN
IF NOT EXISTS group_by_category BOOLEAN DEFAULT FALSE AFTER status,
ADD COLUMN
IF NOT EXISTS show_category_images BOOLEAN DEFAULT TRUE AFTER group_by_category;
