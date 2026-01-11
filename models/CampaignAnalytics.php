<?php
/**
 * Campaign Analytics Model
 * Track and analyze campaign performance
 */

class CampaignAnalytics
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Track an event
     */
    public function trackEvent(int $campaignId, string $eventType, array $data = []): bool
    {
        // Insert analytics record
        $sql = "INSERT INTO campaign_analytics (campaign_id, event_type, platform, source, ip_address, user_agent, referrer, metadata)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $result = $this->db->execute($sql, [
            $campaignId,
            $eventType,
            $data['platform'] ?? null,
            $data['source'] ?? null,
            $data['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
            $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null,
            $data['referrer'] ?? $_SERVER['HTTP_REFERER'] ?? null,
            !empty($data['metadata']) ? json_encode($data['metadata']) : null
        ]);

        if ($result) {
            // Update daily stats
            $this->updateDailyStats($campaignId, $eventType);

            // Update campaign totals
            $this->updateCampaignTotals($campaignId, $eventType);
        }

        return $result;
    }

    /**
     * Update daily statistics
     */
    private function updateDailyStats(int $campaignId, string $eventType): void
    {
        $today = date('Y-m-d');
        $column = $this->getColumnForEvent($eventType);

        if (!$column) return;

        // Try to update existing record
        $sql = "INSERT INTO campaign_daily_stats (campaign_id, stat_date, {$column})
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE {$column} = {$column} + 1, updated_at = NOW()";

        $this->db->execute($sql, [$campaignId, $today]);

        // Update unique views if it's a view event
        if ($eventType === 'view') {
            $this->updateUniqueViews($campaignId, $today);
        }
    }

    /**
     * Update unique views count
     */
    private function updateUniqueViews(int $campaignId, string $date): void
    {
        $uniqueCount = $this->db->fetch(
            "SELECT COUNT(DISTINCT ip_address) as count
             FROM campaign_analytics
             WHERE campaign_id = ? AND event_type = 'view'
             AND DATE(created_at) = ?",
            [$campaignId, $date]
        );

        $this->db->execute(
            "UPDATE campaign_daily_stats SET unique_views = ? WHERE campaign_id = ? AND stat_date = ?",
            [$uniqueCount['count'] ?? 0, $campaignId, $date]
        );
    }

    /**
     * Update campaign total counts
     */
    private function updateCampaignTotals(int $campaignId, string $eventType): void
    {
        $columnMap = [
            'view' => 'total_views',
            'click' => 'total_clicks',
            'share' => 'total_shares',
            'engagement' => 'total_engagements',
            'copy' => 'total_views' // Count copies as engagement
        ];

        $column = $columnMap[$eventType] ?? null;
        if (!$column) return;

        $this->db->execute(
            "UPDATE campaign_messages SET {$column} = {$column} + 1, last_activity_at = NOW() WHERE id = ?",
            [$campaignId]
        );

        // Update conversion rate
        $this->updateConversionRate($campaignId);
    }

    /**
     * Update conversion rate (clicks / views * 100)
     */
    private function updateConversionRate(int $campaignId): void
    {
        $this->db->execute(
            "UPDATE campaign_messages
             SET conversion_rate = CASE
                 WHEN total_views > 0 THEN ROUND((total_clicks / total_views) * 100, 2)
                 ELSE 0
             END
             WHERE id = ?",
            [$campaignId]
        );
    }

    /**
     * Get column name for event type
     */
    private function getColumnForEvent(string $eventType): ?string
    {
        $map = [
            'view' => 'views',
            'copy' => 'copies',
            'click' => 'clicks',
            'share' => 'shares',
            'engagement' => 'engagements'
        ];

        return $map[$eventType] ?? null;
    }

    /**
     * Get campaign performance summary
     */
    public function getCampaignPerformance(int $campaignId): array
    {
        $campaign = $this->db->fetch(
            "SELECT id, title, total_views, total_clicks, total_shares, total_engagements,
                    conversion_rate, copy_count, last_activity_at, created_at
             FROM campaign_messages WHERE id = ?",
            [$campaignId]
        );

        if (!$campaign) {
            return [];
        }

        // Get daily stats for last 30 days
        $dailyStats = $this->db->fetchAll(
            "SELECT stat_date, views, copies, clicks, shares, engagements, unique_views
             FROM campaign_daily_stats
             WHERE campaign_id = ? AND stat_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             ORDER BY stat_date",
            [$campaignId]
        );

        // Get hourly distribution
        $hourlyDistribution = $this->db->fetchAll(
            "SELECT HOUR(created_at) as hour, COUNT(*) as count
             FROM campaign_analytics
             WHERE campaign_id = ?
             GROUP BY HOUR(created_at)
             ORDER BY hour",
            [$campaignId]
        );

        // Get platform breakdown
        $platformStats = $this->db->fetchAll(
            "SELECT platform, event_type, COUNT(*) as count
             FROM campaign_analytics
             WHERE campaign_id = ? AND platform IS NOT NULL
             GROUP BY platform, event_type",
            [$campaignId]
        );

        // Get recent activity
        $recentActivity = $this->db->fetchAll(
            "SELECT event_type, platform, created_at
             FROM campaign_analytics
             WHERE campaign_id = ?
             ORDER BY created_at DESC
             LIMIT 20",
            [$campaignId]
        );

        return [
            'campaign' => $campaign,
            'daily_stats' => $dailyStats,
            'hourly_distribution' => $this->formatHourlyData($hourlyDistribution),
            'platform_stats' => $this->formatPlatformStats($platformStats),
            'recent_activity' => $recentActivity,
            'summary' => $this->calculateSummary($campaign, $dailyStats)
        ];
    }

    /**
     * Format hourly data for chart
     */
    private function formatHourlyData(array $data): array
    {
        $hours = array_fill(0, 24, 0);
        foreach ($data as $row) {
            $hours[(int)$row['hour']] = (int)$row['count'];
        }
        return $hours;
    }

    /**
     * Format platform stats
     */
    private function formatPlatformStats(array $data): array
    {
        $result = [];
        foreach ($data as $row) {
            $platform = $row['platform'] ?? 'unknown';
            if (!isset($result[$platform])) {
                $result[$platform] = ['views' => 0, 'clicks' => 0, 'copies' => 0, 'shares' => 0];
            }
            $result[$platform][$row['event_type'] . 's'] = (int)$row['count'];
        }
        return $result;
    }

    /**
     * Calculate performance summary
     */
    private function calculateSummary(array $campaign, array $dailyStats): array
    {
        $totalViews = (int)$campaign['total_views'];
        $totalClicks = (int)$campaign['total_clicks'];

        // Calculate trends (compare last 7 days vs previous 7 days)
        $last7Days = array_slice($dailyStats, -7);
        $prev7Days = array_slice($dailyStats, -14, 7);

        $last7Views = array_sum(array_column($last7Days, 'views'));
        $prev7Views = array_sum(array_column($prev7Days, 'views'));

        $viewsTrend = $prev7Views > 0
            ? round((($last7Views - $prev7Views) / $prev7Views) * 100, 1)
            : ($last7Views > 0 ? 100 : 0);

        return [
            'total_views' => $totalViews,
            'total_clicks' => $totalClicks,
            'total_copies' => (int)$campaign['copy_count'],
            'conversion_rate' => (float)$campaign['conversion_rate'],
            'avg_daily_views' => count($dailyStats) > 0
                ? round(array_sum(array_column($dailyStats, 'views')) / count($dailyStats), 1)
                : 0,
            'views_trend' => $viewsTrend,
            'last_activity' => $campaign['last_activity_at'],
            'days_active' => floor((time() - strtotime($campaign['created_at'])) / 86400)
        ];
    }

    /**
     * Get all campaigns performance overview
     */
    public function getAllCampaignsPerformance(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT cm.*,
                    (SELECT COUNT(*) FROM campaign_analytics ca WHERE ca.campaign_id = cm.id AND DATE(ca.created_at) = CURDATE()) as today_events,
                    (SELECT COUNT(*) FROM campaign_analytics ca WHERE ca.campaign_id = cm.id AND ca.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as week_events
             FROM campaign_messages cm
             WHERE cm.store_id = ?
             ORDER BY cm.total_views DESC",
            [$storeId]
        );
    }

    /**
     * Get top performing campaigns
     */
    public function getTopCampaigns(int $storeId, int $limit = 5, string $metric = 'views'): array
    {
        $column = match($metric) {
            'clicks' => 'total_clicks',
            'shares' => 'total_shares',
            'conversion' => 'conversion_rate',
            'copies' => 'copy_count',
            default => 'total_views'
        };

        return $this->db->fetchAll(
            "SELECT id, title, platform, message_type, {$column} as metric_value,
                    total_views, total_clicks, conversion_rate
             FROM campaign_messages
             WHERE store_id = ? AND is_active = 1
             ORDER BY {$column} DESC
             LIMIT ?",
            [$storeId, $limit]
        );
    }

    /**
     * Get performance trends for a date range
     */
    public function getPerformanceTrends(int $storeId, string $startDate, string $endDate): array
    {
        return $this->db->fetchAll(
            "SELECT cds.stat_date,
                    SUM(cds.views) as total_views,
                    SUM(cds.clicks) as total_clicks,
                    SUM(cds.copies) as total_copies,
                    SUM(cds.shares) as total_shares,
                    SUM(cds.unique_views) as unique_views
             FROM campaign_daily_stats cds
             JOIN campaign_messages cm ON cds.campaign_id = cm.id
             WHERE cm.store_id = ? AND cds.stat_date BETWEEN ? AND ?
             GROUP BY cds.stat_date
             ORDER BY cds.stat_date",
            [$storeId, $startDate, $endDate]
        );
    }

    /**
     * Get platform performance breakdown
     */
    public function getPlatformPerformance(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT cm.platform,
                    COUNT(*) as campaign_count,
                    SUM(cm.total_views) as total_views,
                    SUM(cm.total_clicks) as total_clicks,
                    AVG(cm.conversion_rate) as avg_conversion
             FROM campaign_messages cm
             WHERE cm.store_id = ?
             GROUP BY cm.platform
             ORDER BY total_views DESC",
            [$storeId]
        );
    }

    /**
     * Add campaign goal
     */
    public function addGoal(int $campaignId, string $goalType, int $targetValue, ?string $startDate = null, ?string $endDate = null): int|false
    {
        return $this->db->insert(
            "INSERT INTO campaign_goals (campaign_id, goal_type, target_value, start_date, end_date)
             VALUES (?, ?, ?, ?, ?)",
            [$campaignId, $goalType, $targetValue, $startDate, $endDate]
        );
    }

    /**
     * Get campaign goals
     */
    public function getGoals(int $campaignId): array
    {
        $goals = $this->db->fetchAll(
            "SELECT * FROM campaign_goals WHERE campaign_id = ? ORDER BY created_at DESC",
            [$campaignId]
        );

        // Calculate current values
        foreach ($goals as &$goal) {
            $column = match($goal['goal_type']) {
                'views' => 'total_views',
                'clicks' => 'total_clicks',
                'shares' => 'total_shares',
                'copies' => 'copy_count',
                default => 'total_views'
            };

            $current = $this->db->fetch(
                "SELECT {$column} as value FROM campaign_messages WHERE id = ?",
                [$campaignId]
            );

            $goal['current_value'] = (int)($current['value'] ?? 0);
            $goal['progress'] = $goal['target_value'] > 0
                ? min(100, round(($goal['current_value'] / $goal['target_value']) * 100, 1))
                : 0;
        }

        return $goals;
    }

    /**
     * Add campaign note
     */
    public function addNote(int $campaignId, int $userId, string $note, string $noteType = 'general'): int|false
    {
        return $this->db->insert(
            "INSERT INTO campaign_notes (campaign_id, user_id, note, note_type) VALUES (?, ?, ?, ?)",
            [$campaignId, $userId, $note, $noteType]
        );
    }

    /**
     * Get campaign notes
     */
    public function getNotes(int $campaignId): array
    {
        return $this->db->fetchAll(
            "SELECT cn.*, u.name as user_name
             FROM campaign_notes cn
             LEFT JOIN users u ON cn.user_id = u.id
             WHERE cn.campaign_id = ?
             ORDER BY cn.created_at DESC",
            [$campaignId]
        );
    }

    /**
     * Delete note
     */
    public function deleteNote(int $noteId): bool
    {
        return $this->db->execute("DELETE FROM campaign_notes WHERE id = ?", [$noteId]);
    }

    /**
     * Get insights summary for dashboard
     */
    public function getDashboardInsights(int $storeId): array
    {
        // Overall stats
        $overall = $this->db->fetch(
            "SELECT
                COUNT(*) as total_campaigns,
                SUM(total_views) as total_views,
                SUM(total_clicks) as total_clicks,
                SUM(copy_count) as total_copies,
                AVG(conversion_rate) as avg_conversion
             FROM campaign_messages
             WHERE store_id = ?",
            [$storeId]
        );

        // Today's stats
        $today = $this->db->fetch(
            "SELECT
                SUM(views) as views,
                SUM(clicks) as clicks,
                SUM(copies) as copies
             FROM campaign_daily_stats cds
             JOIN campaign_messages cm ON cds.campaign_id = cm.id
             WHERE cm.store_id = ? AND cds.stat_date = CURDATE()",
            [$storeId]
        );

        // This week vs last week
        $thisWeek = $this->db->fetch(
            "SELECT SUM(views) as views FROM campaign_daily_stats cds
             JOIN campaign_messages cm ON cds.campaign_id = cm.id
             WHERE cm.store_id = ? AND cds.stat_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)",
            [$storeId]
        );

        $lastWeek = $this->db->fetch(
            "SELECT SUM(views) as views FROM campaign_daily_stats cds
             JOIN campaign_messages cm ON cds.campaign_id = cm.id
             WHERE cm.store_id = ? AND cds.stat_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 14 DAY) AND DATE_SUB(CURDATE(), INTERVAL 7 DAY)",
            [$storeId]
        );

        $weeklyGrowth = ($lastWeek['views'] ?? 0) > 0
            ? round(((($thisWeek['views'] ?? 0) - ($lastWeek['views'] ?? 0)) / ($lastWeek['views'] ?? 1)) * 100, 1)
            : 0;

        return [
            'overall' => $overall,
            'today' => $today,
            'weekly_growth' => $weeklyGrowth,
            'top_campaigns' => $this->getTopCampaigns($storeId, 5),
            'platform_performance' => $this->getPlatformPerformance($storeId)
        ];
    }
}
