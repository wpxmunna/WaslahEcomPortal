-- Lookbook/Instagram Gallery Migration
-- Run this SQL to create the lookbook table

CREATE TABLE IF NOT EXISTS lookbook (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT DEFAULT 1,
    image VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    caption VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Insert default lookbook images (using placeholder URLs)
INSERT INTO lookbook (store_id, image, link, caption, is_featured, sort_order, status) VALUES
(1, 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=500&q=80', 'https://instagram.com', 'Summer Vibes', 1, 1, 'active'),
(1, 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=300&q=80', 'https://instagram.com', 'Street Style', 0, 2, 'active'),
(1, 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=300&q=80', 'https://instagram.com', 'Elegant Look', 0, 3, 'active'),
(1, 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=300&q=80', 'https://instagram.com', 'Casual Friday', 0, 4, 'active'),
(1, 'https://images.unsplash.com/photo-1485968579169-11d4a1fa432d?w=300&q=80', 'https://instagram.com', 'Weekend Ready', 0, 5, 'active');
