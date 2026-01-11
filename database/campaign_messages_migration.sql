-- =============================================
-- Campaign Messages Migration
-- Manage social media campaign/promotional messages
-- =============================================

CREATE TABLE IF NOT EXISTS campaign_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL DEFAULT 1,
    title VARCHAR(255) NOT NULL,
    platform ENUM('all', 'facebook', 'instagram', 'whatsapp', 'telegram', 'twitter') DEFAULT 'all',
    message_type ENUM('promotion', 'announcement', 'greeting', 'offer', 'event', 'custom') DEFAULT 'promotion',
    content TEXT NOT NULL,
    short_content VARCHAR(500),
    hashtags VARCHAR(500),
    call_to_action VARCHAR(255),
    cta_url VARCHAR(500),
    image_path VARCHAR(255),
    scheduled_at DATETIME,
    expires_at DATETIME,
    is_active TINYINT(1) DEFAULT 1,
    is_pinned TINYINT(1) DEFAULT 0,
    copy_count INT DEFAULT 0,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create indexes for faster queries
CREATE INDEX idx_campaign_messages_store ON campaign_messages(store_id, is_active);
CREATE INDEX idx_campaign_messages_platform ON campaign_messages(platform, is_active);
CREATE INDEX idx_campaign_messages_type ON campaign_messages(message_type);

-- Insert sample campaign messages
INSERT INTO campaign_messages (store_id, title, platform, message_type, content, short_content, hashtags, call_to_action, cta_url, is_active, is_pinned) VALUES
(1, 'New Collection Launch', 'all', 'announcement',
'Exciting news! Our brand new collection has just dropped! Discover the latest trends in fashion that combine style, comfort, and quality. From elegant dresses to casual wear, we have something for everyone.\n\nVisit our store today and be the first to grab these exclusive pieces before they sell out!',
'New collection just dropped! Shop the latest trends now.',
'#NewCollection #Fashion #WaslahStyle #TrendyClothes #ShopNow',
'Shop Now', 'https://waslah.com/shop', 1, 1),

(1, 'Weekend Sale - 30% Off', 'facebook', 'offer',
'WEEKEND FLASH SALE!\n\nGet 30% OFF on all items this weekend only! Use code: WEEKEND30 at checkout.\n\nDon''t miss this amazing opportunity to upgrade your wardrobe at unbeatable prices. Offer valid till Sunday midnight!',
'30% OFF this weekend! Use code WEEKEND30',
'#Sale #WeekendSale #Discount #Fashion #WaslahFashion',
'Grab the Deal', 'https://waslah.com/sale', 1, 0),

(1, 'Instagram Story Template', 'instagram', 'promotion',
'NEW ARRIVALS ALERT!\n\nSwipe up to check out our newest styles.\n\nTag us in your photos wearing Waslah for a chance to be featured!',
'New arrivals! Swipe up to shop',
'#Waslah #NewArrivals #OOTD #FashionInspo #StyleGuide',
'Swipe Up', 'https://waslah.com/new', 1, 0),

(1, 'WhatsApp Broadcast - Eid Offer', 'whatsapp', 'event',
'Eid Mubarak!\n\nCelebrate this festive season with our exclusive Eid collection. Enjoy up to 40% off on selected items.\n\nReply ''EID'' to get your personalized recommendations!\n\nFree delivery on orders above 3000 BDT.',
'Eid Mubarak! Up to 40% off on Eid collection',
NULL,
'Reply EID', NULL, 1, 0),

(1, 'Customer Appreciation Message', 'all', 'greeting',
'Dear Valued Customer,\n\nThank you for choosing Waslah Fashion! Your support means the world to us.\n\nAs a token of appreciation, enjoy 15% off on your next purchase. Use code: THANKYOU15\n\nWe look forward to serving you again!',
'Thank you! Enjoy 15% off with code THANKYOU15',
'#ThankYou #CustomerAppreciation #WaslahFamily',
'Shop Now', 'https://waslah.com', 1, 0);

SELECT 'Campaign Messages migration completed successfully!' as message;
