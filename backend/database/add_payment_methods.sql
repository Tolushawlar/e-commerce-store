-- Add additional payment method settings to stores table

-- Bank Transfer settings
ALTER TABLE stores
ADD COLUMN
IF NOT EXISTS bank_transfer_enabled BOOLEAN DEFAULT FALSE,
ADD COLUMN
IF NOT EXISTS bank_name VARCHAR
(100) DEFAULT NULL,
ADD COLUMN
IF NOT EXISTS account_number VARCHAR
(50) DEFAULT NULL,
ADD COLUMN
IF NOT EXISTS account_name VARCHAR
(100) DEFAULT NULL;

-- Cash on Delivery settings
ALTER TABLE stores
ADD COLUMN
IF NOT EXISTS cod_enabled BOOLEAN DEFAULT TRUE;
