-- Sliders Migration
-- Run this SQL to create the sliders table

CREATE TABLE IF NOT EXISTS sliders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT DEFAULT 1,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    description TEXT,
    button_text VARCHAR(100),
    button_link VARCHAR(255),
    button2_text VARCHAR(100),
    button2_link VARCHAR(255),
    image VARCHAR(255),
    text_position ENUM('left', 'center', 'right') DEFAULT 'left',
    text_color VARCHAR(20) DEFAULT '#ffffff',
    overlay_opacity DECIMAL(3,2) DEFAULT 0.40,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Insert default sliders
INSERT INTO sliders (store_id, title, subtitle, description, button_text, button_link, button2_text, button2_link, image, sort_order, status) VALUES
(1, 'Elegance Redefined', 'New Collection 2025', 'Discover the perfect blend of tradition and modernity', 'Shop Women', 'shop/category/women', 'Shop Men', 'shop/category/men', NULL, 1, 'active'),
(1, 'Authenticity in Every Stitch', 'Premium Quality', 'Handcrafted with love, designed for you', 'Explore Collection', 'shop', NULL, NULL, NULL, 2, 'active'),
(1, 'Style for Little Ones', 'Kids Collection', 'Comfort meets fashion for your children', 'Shop Kids', 'shop/category/children', NULL, NULL, NULL, 3, 'active');
