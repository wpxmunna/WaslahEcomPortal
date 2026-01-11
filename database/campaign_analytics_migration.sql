-- =============================================
-- Campaign Analytics Migration
-- Track campaign performance and engagement
-- =============================================

-- Campaign Analytics - Track individual events
CREATE TABLE IF NOT EXISTS campaign_analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    campaign_id INT NOT NULL,
    event_type ENUM('view', 'copy', 'click', 'share', 'engagement') NOT NULL,
    platform VARCHAR(50),
    source VARCHAR(100),
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    referrer VARCHAR(500),
    metadata JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaign_messages(id) ON DELETE CASCADE
);

-- Campaign Daily Stats - Aggregated daily statistics
CREATE TABLE IF NOT EXISTS campaign_daily_stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    campaign_id INT NOT NULL,
    stat_date DATE NOT NULL,
    views INT DEFAULT 0,
    copies INT DEFAULT 0,
    clicks INT DEFAULT 0,
    shares INT DEFAULT 0,
    engagements INT DEFAULT 0,
    unique_views INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_campaign_date (campaign_id, stat_date),
    FOREIGN KEY (campaign_id) REFERENCES campaign_messages(id) ON DELETE CASCADE
);

-- Campaign Goals - Set performance targets
CREATE TABLE IF NOT EXISTS campaign_goals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    campaign_id INT NOT NULL,
    goal_type ENUM('views', 'copies', 'clicks', 'shares', 'engagements') NOT NULL,
    target_value INT NOT NULL,
    current_value INT DEFAULT 0,
    start_date DATE,
    end_date DATE,
    is_achieved TINYINT(1) DEFAULT 0,
    achieved_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaign_messages(id) ON DELETE CASCADE
);

-- Campaign Notes/Comments - For team collaboration
CREATE TABLE IF NOT EXISTS campaign_notes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    campaign_id INT NOT NULL,
    user_id INT,
    note TEXT NOT NULL,
    note_type ENUM('general', 'performance', 'issue', 'idea') DEFAULT 'general',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaign_messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Add performance columns to campaign_messages
ALTER TABLE campaign_messages
ADD COLUMN IF NOT EXISTS total_views INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS total_clicks INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS total_shares INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS total_engagements INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS conversion_rate DECIMAL(5,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS last_activity_at DATETIME;

-- Create indexes for faster queries
CREATE INDEX idx_campaign_analytics_campaign ON campaign_analytics(campaign_id, event_type);
CREATE INDEX idx_campaign_analytics_date ON campaign_analytics(created_at);
CREATE INDEX idx_campaign_daily_stats_date ON campaign_daily_stats(stat_date);

SELECT 'Campaign Analytics migration completed successfully!' as message;
