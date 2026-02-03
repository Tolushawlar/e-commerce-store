-- Add Paystack payment fields to stores table
ALTER TABLE stores 
ADD COLUMN paystack_public_key VARCHAR
(255) DEFAULT NULL,
ADD COLUMN paystack_secret_key VARCHAR
(255) DEFAULT NULL,
ADD COLUMN payment_enabled BOOLEAN DEFAULT FALSE;

-- Add payment tracking fields to orders table
ALTER TABLE orders
ADD COLUMN payment_reference VARCHAR
(100) DEFAULT NULL,
ADD COLUMN payment_gateway VARCHAR
(50) DEFAULT 'paystack',
ADD COLUMN payment_verified_at DATETIME DEFAULT NULL;

-- Add index for faster lookups
ALTER TABLE orders ADD INDEX idx_payment_reference (payment_reference);
