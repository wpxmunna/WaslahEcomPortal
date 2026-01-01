-- Product Returns Migration
-- Run this to add return tracking tables

-- ============================================
-- RETURNS TABLE (main return records)
-- ============================================
CREATE TABLE IF NOT EXISTS returns (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT DEFAULT 1,
    order_id INT NOT NULL,
    return_number VARCHAR(50) NOT NULL,
    reason ENUM('defective','damaged','wrong_item','not_as_described','changed_mind','customer_refused','undelivered','other') NOT NULL,
    reason_details TEXT,
    refund_amount DECIMAL(10,2) DEFAULT 0.00,
    refund_status ENUM('not_required','pending','completed') DEFAULT 'not_required',
    admin_notes TEXT,
    returned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    UNIQUE KEY unique_return_number (return_number, store_id),
    INDEX idx_order (order_id),
    INDEX idx_store (store_id)
);

-- ============================================
-- RETURN ITEMS TABLE (items in each return)
-- ============================================
CREATE TABLE IF NOT EXISTS return_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    return_id INT NOT NULL,
    order_item_id INT,
    product_id INT,
    variant_id INT,
    product_name VARCHAR(255),
    variant_info VARCHAR(100),
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2),
    stock_restored TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (return_id) REFERENCES returns(id) ON DELETE CASCADE,
    INDEX idx_return (return_id),
    INDEX idx_product (product_id)
);
