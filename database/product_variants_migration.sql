-- Product Variants Enhancement Migration
-- Run this to add color management and size-quantity tracking

-- ============================================
-- PRODUCT COLORS (Store available colors)
-- ============================================
CREATE TABLE IF NOT EXISTS product_colors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT DEFAULT 1,
    name VARCHAR(50) NOT NULL,
    color_code VARCHAR(7) NOT NULL,
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    UNIQUE KEY unique_color_store (name, store_id)
);

-- Insert default colors
INSERT INTO product_colors (store_id, name, color_code, sort_order) VALUES
(1, 'Black', '#000000', 1),
(1, 'White', '#FFFFFF', 2),
(1, 'Navy Blue', '#1a1a2e', 3),
(1, 'Red', '#e94560', 4),
(1, 'Maroon', '#800020', 5),
(1, 'Green', '#28a745', 6),
(1, 'Gray', '#6c757d', 7),
(1, 'Beige', '#F5F5DC', 8),
(1, 'Brown', '#8B4513', 9),
(1, 'Pink', '#FFC0CB', 10);

-- ============================================
-- PRODUCT SIZES (Store available sizes)
-- ============================================
CREATE TABLE IF NOT EXISTS product_sizes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT DEFAULT 1,
    name VARCHAR(20) NOT NULL,
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    UNIQUE KEY unique_size_store (name, store_id)
);

-- Insert default sizes
INSERT INTO product_sizes (store_id, name, sort_order) VALUES
(1, 'XS', 1),
(1, 'S', 2),
(1, 'M', 3),
(1, 'L', 4),
(1, 'XL', 5),
(1, 'XXL', 6),
(1, 'XXXL', 7);

-- ============================================
-- Update product_variants table structure
-- ============================================
-- Add color_id column if not exists
ALTER TABLE product_variants
ADD COLUMN IF NOT EXISTS color_id INT NULL AFTER product_id,
ADD FOREIGN KEY (color_id) REFERENCES product_colors(id) ON DELETE SET NULL;

-- Add index for faster lookups
CREATE INDEX IF NOT EXISTS idx_variant_product_size_color ON product_variants(product_id, size, color_id);
