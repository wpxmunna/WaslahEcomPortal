-- =============================================
-- POS Enhancements Migration
-- Run this after the pos_hr_migration.sql
-- Adds: Held Orders, Refunds, Split Payments
-- =============================================

-- =============================================
-- POS Held Orders (for Hold/Recall feature)
-- =============================================

CREATE TABLE IF NOT EXISTS pos_held_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    shift_id INT,
    terminal_id INT,
    hold_number VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT,
    customer_name VARCHAR(255),
    customer_phone VARCHAR(50),
    items_json LONGTEXT NOT NULL,
    note TEXT,
    status ENUM('held','recalled','expired') DEFAULT 'held',
    held_by INT,
    recalled_at DATETIME,
    expires_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (shift_id) REFERENCES pos_shifts(id) ON DELETE SET NULL,
    FOREIGN KEY (terminal_id) REFERENCES pos_terminals(id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (held_by) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- POS Refunds
-- =============================================

CREATE TABLE IF NOT EXISTS pos_refunds (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    shift_id INT,
    terminal_id INT,
    transaction_id INT NOT NULL,
    refund_number VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT,
    customer_name VARCHAR(255),
    refund_amount DECIMAL(14,2) NOT NULL,
    refund_method ENUM('cash','card','store_credit','original_method') DEFAULT 'cash',
    reason VARCHAR(100),
    items_json LONGTEXT,
    notes TEXT,
    status ENUM('pending','completed','cancelled') DEFAULT 'completed',
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (shift_id) REFERENCES pos_shifts(id) ON DELETE SET NULL,
    FOREIGN KEY (terminal_id) REFERENCES pos_terminals(id) ON DELETE SET NULL,
    FOREIGN KEY (transaction_id) REFERENCES pos_transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- POS Split Payments
-- =============================================

CREATE TABLE IF NOT EXISTS pos_split_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_id INT NOT NULL,
    payment_method ENUM('cash','card','mobile_banking') NOT NULL,
    amount DECIMAL(14,2) NOT NULL,
    reference_number VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES pos_transactions(id) ON DELETE CASCADE
);

-- =============================================
-- Add indexes for performance
-- =============================================

CREATE INDEX idx_held_orders_store ON pos_held_orders(store_id, status);
CREATE INDEX idx_held_orders_shift ON pos_held_orders(shift_id);
CREATE INDEX idx_refunds_store ON pos_refunds(store_id, status);
CREATE INDEX idx_refunds_transaction ON pos_refunds(transaction_id);
CREATE INDEX idx_split_payments_transaction ON pos_split_payments(transaction_id);

-- =============================================
-- Add refunded_amount column to pos_transactions
-- =============================================

-- Check if refunded_amount column exists, if not add it
SET @dbname = DATABASE();
SET @tablename = 'pos_transactions';
SET @columnname = 'refunded_amount';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = @dbname
     AND TABLE_NAME = @tablename
     AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE pos_transactions ADD COLUMN refunded_amount DECIMAL(14,2) DEFAULT 0.00 AFTER status'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================
-- Add barcode column to products if not exists
-- =============================================

-- Check if barcode column exists, if not add it
SET @dbname = DATABASE();
SET @tablename = 'products';
SET @columnname = 'barcode';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = @dbname
     AND TABLE_NAME = @tablename
     AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE products ADD COLUMN barcode VARCHAR(100) NULL AFTER sku'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index on barcode for quick lookups (only if not exists)
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'products'
     AND INDEX_NAME = 'idx_products_barcode') > 0,
    'SELECT 1',
    'CREATE INDEX idx_products_barcode ON products(barcode)'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'POS Enhancements migration completed successfully!' as message;
