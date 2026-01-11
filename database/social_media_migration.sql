-- =============================================
-- Social Media Manager Migration
-- Manage social media links dynamically
-- =============================================

CREATE TABLE IF NOT EXISTS social_media (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL DEFAULT 1,
    platform VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    url VARCHAR(500) NOT NULL,
    icon VARCHAR(100) NOT NULL,
    icon_style ENUM('brands', 'solid', 'regular') DEFAULT 'brands',
    color VARCHAR(20) DEFAULT '#000000',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    show_in_header TINYINT(1) DEFAULT 0,
    show_in_footer TINYINT(1) DEFAULT 1,
    open_new_tab TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Insert default social media platforms
INSERT INTO social_media (store_id, platform, name, url, icon, icon_style, color, sort_order, is_active, show_in_header, show_in_footer) VALUES
(1, 'facebook', 'Facebook', 'https://facebook.com/', 'fa-facebook-f', 'brands', '#1877F2', 1, 1, 1, 1),
(1, 'instagram', 'Instagram', 'https://instagram.com/', 'fa-instagram', 'brands', '#E4405F', 2, 1, 1, 1),
(1, 'twitter', 'Twitter/X', 'https://twitter.com/', 'fa-x-twitter', 'brands', '#000000', 3, 1, 1, 1),
(1, 'youtube', 'YouTube', 'https://youtube.com/', 'fa-youtube', 'brands', '#FF0000', 4, 1, 0, 1),
(1, 'tiktok', 'TikTok', 'https://tiktok.com/', 'fa-tiktok', 'brands', '#000000', 5, 1, 0, 1),
(1, 'linkedin', 'LinkedIn', 'https://linkedin.com/', 'fa-linkedin-in', 'brands', '#0A66C2', 6, 0, 0, 1),
(1, 'pinterest', 'Pinterest', 'https://pinterest.com/', 'fa-pinterest-p', 'brands', '#E60023', 7, 0, 0, 1),
(1, 'whatsapp', 'WhatsApp', 'https://wa.me/', 'fa-whatsapp', 'brands', '#25D366', 8, 1, 1, 1),
(1, 'telegram', 'Telegram', 'https://t.me/', 'fa-telegram', 'brands', '#26A5E4', 9, 0, 0, 1),
(1, 'snapchat', 'Snapchat', 'https://snapchat.com/', 'fa-snapchat', 'brands', '#FFFC00', 10, 0, 0, 0);

-- Create index for faster queries
CREATE INDEX idx_social_media_store ON social_media(store_id, is_active, sort_order);

SELECT 'Social Media migration completed successfully!' as message;
