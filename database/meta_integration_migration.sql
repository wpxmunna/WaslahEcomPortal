-- =============================================
-- Meta (Facebook/WhatsApp) Integration Migration
-- Connect to Facebook Pages & WhatsApp Business
-- =============================================

-- Meta Integration Settings
CREATE TABLE IF NOT EXISTS meta_integrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    platform ENUM('facebook', 'instagram', 'whatsapp') NOT NULL,
    page_id VARCHAR(100),
    page_name VARCHAR(255),
    page_access_token TEXT,
    user_access_token TEXT,
    token_expires_at DATETIME,
    phone_number_id VARCHAR(100),
    whatsapp_business_id VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    last_sync_at DATETIME,
    settings JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_store_platform (store_id, platform),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Facebook/Instagram Page Messages
CREATE TABLE IF NOT EXISTS meta_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    platform ENUM('facebook', 'instagram', 'whatsapp') NOT NULL,
    message_id VARCHAR(255) NOT NULL UNIQUE,
    conversation_id VARCHAR(255),
    sender_id VARCHAR(100),
    sender_name VARCHAR(255),
    sender_profile_pic VARCHAR(500),
    recipient_id VARCHAR(100),
    message_type ENUM('text', 'image', 'video', 'audio', 'file', 'sticker', 'template', 'interactive') DEFAULT 'text',
    content TEXT,
    media_url VARCHAR(500),
    is_incoming TINYINT(1) DEFAULT 1,
    is_read TINYINT(1) DEFAULT 0,
    status ENUM('sent', 'delivered', 'read', 'failed') DEFAULT 'sent',
    metadata JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    INDEX idx_conversation (conversation_id),
    INDEX idx_sender (sender_id)
);

-- Facebook/Instagram Page Insights
CREATE TABLE IF NOT EXISTS meta_page_insights (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    platform ENUM('facebook', 'instagram') NOT NULL,
    page_id VARCHAR(100) NOT NULL,
    metric_name VARCHAR(100) NOT NULL,
    metric_value DECIMAL(14,2),
    period ENUM('day', 'week', 'month', 'lifetime') DEFAULT 'day',
    stat_date DATE NOT NULL,
    metadata JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_metric_date (store_id, platform, page_id, metric_name, stat_date, period),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Message Templates (for WhatsApp)
CREATE TABLE IF NOT EXISTS meta_message_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    template_id VARCHAR(100),
    name VARCHAR(255) NOT NULL,
    language VARCHAR(10) DEFAULT 'en',
    category ENUM('MARKETING', 'UTILITY', 'AUTHENTICATION') DEFAULT 'MARKETING',
    status ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
    components JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Create indexes
CREATE INDEX idx_meta_messages_date ON meta_messages(created_at);
CREATE INDEX idx_meta_insights_date ON meta_page_insights(stat_date);

SELECT 'Meta Integration migration completed!' as message;
