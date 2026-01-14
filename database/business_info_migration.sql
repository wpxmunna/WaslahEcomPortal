-- =============================================
-- Business Information Settings Migration
-- Store all business links, credentials, and info
-- =============================================

CREATE TABLE IF NOT EXISTS business_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    description VARCHAR(255),
    is_encrypted TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_store_key (store_id, setting_key),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    INDEX idx_group (setting_group)
);

-- Insert default business settings for store 1
INSERT INTO business_settings (store_id, setting_key, setting_value, setting_group, description) VALUES
(1, 'facebook_page_url', 'https://facebook.com/waslahbd', 'social', 'Facebook Page URL'),
(1, 'website_url', 'www.waslahbd.com', 'general', 'Main Website URL'),
(1, 'wordpress_admin_url', 'https://www.waslahbd.com/wp-admin/', 'general', 'WordPress Admin URL'),
(1, 'whatsapp_number', '8801755853091', 'contact', 'WhatsApp Business Number'),
(1, 'whatsapp_link', 'https://wa.me/8801755853091', 'contact', 'WhatsApp Direct Link'),
(1, 'business_email', 'waslahbd@gmail.com', 'contact', 'Business Gmail'),
(1, 'pathao_login_url', 'https://merchant.pathao.com/login', 'shipping', 'Pathao Merchant Login'),
(1, 'steadfast_url', 'https://steadfast.com.bd/', 'shipping', 'SteadFast Courier URL'),
(1, 'namecheap_url', '', 'domain', 'NameCheap Domain Management'),
(1, 'instagram_url', '', 'social', 'Instagram Profile URL'),
(1, 'youtube_url', '', 'social', 'YouTube Channel URL'),
(1, 'linkedin_url', '', 'social', 'LinkedIn Profile URL'),
(1, 'twitter_url', '', 'social', 'Twitter/X Profile URL'),
(1, 'tiktok_url', '', 'social', 'TikTok Profile URL'),
(1, 'business_phone', '', 'contact', 'Business Phone Number'),
(1, 'support_email', '', 'contact', 'Support Email'),
(1, 'business_address', '', 'contact', 'Physical Business Address');

SELECT 'Business settings migration completed!' as message;
