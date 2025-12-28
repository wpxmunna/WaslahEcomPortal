-- Pathao Integration Migration
-- Run this SQL to add required columns for Pathao integration

-- Add columns to shipments table for Pathao
ALTER TABLE shipments
ADD COLUMN courier_name VARCHAR(100) NULL AFTER courier_id,
ADD COLUMN delivery_fee DECIMAL(10,2) DEFAULT 0.00 AFTER status,
ADD COLUMN pathao_status VARCHAR(100) NULL AFTER delivery_fee;

-- Add index for faster lookups
ALTER TABLE shipments ADD INDEX idx_tracking (tracking_number);
ALTER TABLE shipments ADD INDEX idx_courier_name (courier_name);

-- Update orders table to support shipping_address (single field)
ALTER TABLE orders
ADD COLUMN shipping_address TEXT NULL AFTER shipping_phone,
ADD COLUMN shipping_zip VARCHAR(20) NULL AFTER shipping_state;

-- Add Pathao settings defaults
INSERT INTO settings (store_id, setting_key, setting_value, created_at, updated_at) VALUES
(1, 'pathao_enabled', '0', NOW(), NOW()),
(1, 'pathao_environment', 'sandbox', NOW(), NOW()),
(1, 'pathao_auto_create', '1', NOW(), NOW()),
(1, 'pathao_default_weight', '0.5', NOW(), NOW()),
(1, 'pathao_sandbox_client_id', '7N1aMJQbWm', NOW(), NOW()),
(1, 'pathao_sandbox_client_secret', 'wRcaibZkUdSNz2EI9ZyuXLlNrnAv0TdPUPXMnD39', NOW(), NOW()),
(1, 'pathao_sandbox_username', 'test@pathao.com', NOW(), NOW()),
(1, 'pathao_sandbox_password', 'lovePathao', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();
