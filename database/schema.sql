-- Waslah E-Commerce Database Schema
-- Run this file in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS waslah_ecom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE waslah_ecom;

-- ============================================
-- STORES (Multi-store support)
-- ============================================
CREATE TABLE stores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    logo VARCHAR(255),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    currency_code VARCHAR(3) DEFAULT 'USD',
    currency_symbol VARCHAR(5) DEFAULT '$',
    tax_rate DECIMAL(5,2) DEFAULT 0.00,
    status TINYINT(1) DEFAULT 1,
    is_default TINYINT(1) DEFAULT 0,
    settings JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- USERS (Customers & Admins)
-- ============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT DEFAULT 1,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255),
    role ENUM('customer', 'admin', 'manager') DEFAULT 'customer',
    status TINYINT(1) DEFAULT 1,
    email_verified_at TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL
);

-- ============================================
-- USER ADDRESSES
-- ============================================
CREATE TABLE user_addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    label VARCHAR(50) DEFAULT 'Home',
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'United States',
    is_default TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- CATEGORIES
-- ============================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT DEFAULT 1,
    parent_id INT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    icon VARCHAR(50),
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    UNIQUE KEY unique_slug_store (slug, store_id)
);

-- ============================================
-- PRODUCTS
-- ============================================
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT DEFAULT 1,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) NULL,
    cost_price DECIMAL(10,2) NULL,
    sku VARCHAR(50),
    barcode VARCHAR(50),
    stock_quantity INT DEFAULT 0,
    low_stock_threshold INT DEFAULT 5,
    weight DECIMAL(8,2) NULL,
    is_featured TINYINT(1) DEFAULT 0,
    is_new TINYINT(1) DEFAULT 0,
    status ENUM('active', 'inactive', 'draft') DEFAULT 'active',
    meta_title VARCHAR(255),
    meta_description VARCHAR(500),
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    UNIQUE KEY unique_slug_store (slug, store_id),
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured)
);

-- ============================================
-- PRODUCT IMAGES
-- ============================================
CREATE TABLE product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_primary TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ============================================
-- PRODUCT VARIANTS (Size, Color)
-- ============================================
CREATE TABLE product_variants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    size VARCHAR(20),
    color VARCHAR(50),
    color_code VARCHAR(7),
    sku VARCHAR(50),
    price_modifier DECIMAL(10,2) DEFAULT 0.00,
    stock_quantity INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_variant (product_id, size, color)
);

-- ============================================
-- CART
-- ============================================
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    session_id VARCHAR(100),
    store_id INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- ============================================
-- CART ITEMS
-- ============================================
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    variant_id INT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES cart(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
);

-- ============================================
-- ORDERS
-- ============================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT DEFAULT 1,
    user_id INT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),

    -- Pricing
    subtotal DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    shipping_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,

    -- Shipping Address
    shipping_name VARCHAR(100),
    shipping_phone VARCHAR(20),
    shipping_address_line1 VARCHAR(255),
    shipping_address_line2 VARCHAR(255),
    shipping_city VARCHAR(100),
    shipping_state VARCHAR(100),
    shipping_postal_code VARCHAR(20),
    shipping_country VARCHAR(100),

    -- Billing Address
    billing_name VARCHAR(100),
    billing_phone VARCHAR(20),
    billing_address_line1 VARCHAR(255),
    billing_address_line2 VARCHAR(255),
    billing_city VARCHAR(100),
    billing_state VARCHAR(100),
    billing_postal_code VARCHAR(20),
    billing_country VARCHAR(100),

    notes TEXT,
    admin_notes TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_user (user_id),
    INDEX idx_order_number (order_number)
);

-- ============================================
-- ORDER ITEMS
-- ============================================
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT,
    variant_id INT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(50),
    variant_info VARCHAR(100),
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
);

-- ============================================
-- PAYMENTS (Mock Gateway Records)
-- ============================================
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    transaction_id VARCHAR(100),
    gateway VARCHAR(50) NOT NULL,
    method VARCHAR(50),
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    gateway_response JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- ============================================
-- COURIERS (Mock Shipping Services)
-- ============================================
CREATE TABLE couriers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT DEFAULT 1,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL,
    description TEXT,
    logo VARCHAR(255),
    base_rate DECIMAL(10,2) DEFAULT 0.00,
    per_kg_rate DECIMAL(10,2) DEFAULT 0.00,
    estimated_days VARCHAR(20),
    tracking_url VARCHAR(255),
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- ============================================
-- SHIPMENTS (Order Shipping Info)
-- ============================================
CREATE TABLE shipments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    courier_id INT,
    tracking_number VARCHAR(100),
    status ENUM('pending', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed') DEFAULT 'pending',
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (courier_id) REFERENCES couriers(id) ON DELETE SET NULL
);

-- ============================================
-- SHIPMENT TRACKING HISTORY
-- ============================================
CREATE TABLE shipment_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shipment_id INT NOT NULL,
    status VARCHAR(100) NOT NULL,
    location VARCHAR(255),
    description TEXT,
    tracked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE
);

-- ============================================
-- WISHLIST
-- ============================================
CREATE TABLE wishlist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id)
);

-- ============================================
-- REVIEWS
-- ============================================
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(255),
    comment TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- COUPONS
-- ============================================
CREATE TABLE coupons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT DEFAULT 1,
    code VARCHAR(50) NOT NULL,
    type ENUM('fixed', 'percentage') DEFAULT 'fixed',
    value DECIMAL(10,2) NOT NULL,
    minimum_amount DECIMAL(10,2) DEFAULT 0.00,
    maximum_discount DECIMAL(10,2) NULL,
    usage_limit INT NULL,
    used_count INT DEFAULT 0,
    starts_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    UNIQUE KEY unique_code_store (code, store_id)
);

-- ============================================
-- SETTINGS
-- ============================================
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT DEFAULT 1,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    setting_type VARCHAR(20) DEFAULT 'string',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    UNIQUE KEY unique_key_store (setting_key, store_id)
);

-- ============================================
-- BANNERS
-- ============================================
CREATE TABLE banners (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT DEFAULT 1,
    title VARCHAR(255),
    subtitle VARCHAR(255),
    image VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    button_text VARCHAR(50),
    position VARCHAR(50) DEFAULT 'home_slider',
    sort_order INT DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    starts_at TIMESTAMP NULL,
    ends_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- ============================================
-- INSERT DEFAULT DATA
-- ============================================

-- Default Store
INSERT INTO stores (name, slug, description, is_default) VALUES
('Waslah Fashion', 'waslah-fashion', 'Fashion for Everyone - Men, Women & Children', 1);

-- Default Admin User (password: admin123)
INSERT INTO users (store_id, name, email, password, role, status) VALUES
(1, 'Admin', 'admin@waslah.com', '$2y$12$6KVN9rgLk6JgX5NLjuHS2.tHk4OhuPDHFNnofE7v5jcE6ud/tNr3e', 'admin', 1);

-- Main Categories
INSERT INTO categories (store_id, name, slug, description, icon, sort_order) VALUES
(1, 'Men', 'men', 'Men''s Fashion Collection', 'fa-male', 1),
(1, 'Women', 'women', 'Women''s Fashion Collection', 'fa-female', 2),
(1, 'Children', 'children', 'Kids Fashion Collection', 'fa-child', 3);

-- Subcategories for Men
INSERT INTO categories (store_id, parent_id, name, slug, description, sort_order) VALUES
(1, 1, 'T-Shirts', 'men-tshirts', 'Men''s T-Shirts', 1),
(1, 1, 'Shirts', 'men-shirts', 'Men''s Formal & Casual Shirts', 2),
(1, 1, 'Jeans', 'men-jeans', 'Men''s Jeans & Pants', 3),
(1, 1, 'Jackets', 'men-jackets', 'Men''s Jackets & Outerwear', 4),
(1, 1, 'Shoes', 'men-shoes', 'Men''s Footwear', 5);

-- Subcategories for Women
INSERT INTO categories (store_id, parent_id, name, slug, description, sort_order) VALUES
(1, 2, 'Dresses', 'women-dresses', 'Women''s Dresses', 1),
(1, 2, 'Tops', 'women-tops', 'Women''s Tops & Blouses', 2),
(1, 2, 'Jeans', 'women-jeans', 'Women''s Jeans & Pants', 3),
(1, 2, 'Skirts', 'women-skirts', 'Women''s Skirts', 4),
(1, 2, 'Shoes', 'women-shoes', 'Women''s Footwear', 5);

-- Subcategories for Children
INSERT INTO categories (store_id, parent_id, name, slug, description, sort_order) VALUES
(1, 3, 'Boys', 'children-boys', 'Boys Clothing', 1),
(1, 3, 'Girls', 'children-girls', 'Girls Clothing', 2),
(1, 3, 'Baby', 'children-baby', 'Baby Clothing', 3);

-- Default Couriers
INSERT INTO couriers (store_id, name, code, description, base_rate, per_kg_rate, estimated_days, status) VALUES
(1, 'Standard Shipping', 'standard', 'Regular delivery service', 5.00, 0.50, '5-7 days', 1),
(1, 'Express Shipping', 'express', 'Fast delivery service', 15.00, 1.00, '2-3 days', 1),
(1, 'Free Shipping', 'free', 'Free shipping on orders over $100', 0.00, 0.00, '7-10 days', 1);

-- Default Settings
INSERT INTO settings (store_id, setting_key, setting_value) VALUES
(1, 'site_name', 'Waslah Fashion'),
(1, 'site_tagline', 'Fashion for Everyone'),
(1, 'contact_email', 'info@waslah.com'),
(1, 'contact_phone', '+1 234 567 890'),
(1, 'address', '123 Fashion Street, Style City'),
(1, 'facebook_url', 'https://facebook.com/waslah'),
(1, 'instagram_url', 'https://instagram.com/waslah'),
(1, 'twitter_url', 'https://twitter.com/waslah'),
(1, 'free_shipping_threshold', '100'),
(1, 'default_shipping_cost', '10');
